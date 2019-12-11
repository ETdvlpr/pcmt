<?php

declare(strict_types=1);

namespace PcmtCoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PcmtCoreBundle\Exception\InvalidJobConfigurationException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeReferenceDataImport extends ContainerAwareCommand
{
    protected const CONSECUTIVE_JOBS = [
        1 => [
            'connector'             => 'Pcmt Connector',
            'job'                   => 'reference_data_download_xmls',
            'code'                  => 'reference_data_download_xmls',
            'type'                  => 'data_download',
            'job_execution_handler' => 'pcmt:handler:download_reference_data',
        ],
        2 => [
            'connector'             => 'Pcmt Connector',
            'job'                   => 'reference_data_import_xml',
            'code'                  => 'reference_data_import_xml',
            'type'                  => 'import_hidden',
            'config'                => '{"dirPath": "%s"}',
            'job_execution_handler' => 'pcmt:handler:import_reference_data',
        ],
    ];

    /** @var string */
    protected static $defaultName = 'pcmt:reference_data:create';

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /**
         * first check for download job instance, and if not there, create it
         */
        $trialCount = 4; //set max trial to create command
        try {
            $this->checkIfJobsExist($output, $trialCount);
        } catch (InvalidJobConfigurationException $exception) {
            $output->writeln($exception->getMessage());
            die;
        }
        /*
         * then fire up commands consecutively
         */
        $this->executeJobs($output);
    }

    protected function executeJobs(OutputInterface $output): void // put the jobs into one queue and execute them consecutively
    {
        foreach (self::CONSECUTIVE_JOBS as $job) {
            $handler = $this->getApplication()->find($job['job_execution_handler']);
            $output->writeln(sprintf('Executing job:  %s', $job['job_execution_handler']));

            $arguments = new ArrayInput([
                'code'    => $job['code'] ?? null,
                'dirPath' => $job['dirPath'] ?? null,
            ]);

            $handler->run($arguments, $output);
        }
    }

    /**
     * @return array|bool|null
     *
     * @throws \PcmtCoreBundle\Exception\InvalidJobConfigurationException
     */
    protected function checkIfJobsExist(OutputInterface $output, int $trialCount)
    {
        try {
            foreach (self::CONSECUTIVE_JOBS as $jobInstanceParameters) {
                $jobInstanceClass = $this->getContainer()->getParameter('akeneo_batch.entity.job_instance.class');
                $jobInstance = $this->getEntityManager()->getRepository($jobInstanceClass)->findOneBy(['code' => $jobInstanceParameters['code']]);

                if (null === $jobInstance) {
                    throw new \Exception('Job  ' . $jobInstanceParameters['code'] . ' undefinded.');
                }

                $output->writeln('Job instance : ' . $jobInstanceParameters['code'] . ' found.');
            }

            return true;
        } catch (\Throwable $exception) {
            $output->writeln($exception->getMessage());

            if ($trialCount > 0) {
                $trialCount --;
                $output->writeln('Trying to (re)create job instance: ' . $jobInstanceParameters['code']);

                if ($this->createJobInstanceFromParameters($jobInstanceParameters, $output)) {
                    $this->checkIfJobsExist($output, $trialCount);
                }
            } else {
                throw new InvalidJobConfigurationException('Unable to (re)create job instance: ' . $jobInstanceParameters['code'] . ' check config.');
            }
        }
    }

    protected function createJobInstanceFromParameters(array $parameters, OutputInterface $output): bool
    {
        $command = $this->getApplication()->find('akeneo:batch:create-job');

        $arguments = [
            'connector' => $parameters['connector'],
            'job'       => $parameters['job'],
            'type'      => $parameters['type'],
            'code'      => $parameters['code'],
            'config'    => $parameters['config'] ?? null,
        ];

        $input = new ArrayInput($arguments);

        if (0 === $command->run($input, $output)) {
            return true;
        }

        return false;
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }
}