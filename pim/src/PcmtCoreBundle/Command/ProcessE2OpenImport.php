<?php
/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */
declare(strict_types=1);

namespace PcmtCoreBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * launching the job:
 * bin/console pcmt:e2Open:import_from_files
 */
class ProcessE2OpenImport extends ContainerAwareCommand
{
    /** @var string */
    protected static $defaultName = 'pcmt:e2Open:import_from_files';

    /** @var string */
    private $parentDirectory;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    public function __construct(
        string $parentDirectory,
        LoggerInterface $logger
    ) {
        $this->parentDirectory = $parentDirectory;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $workDir = $this->parentDirectory . 'work/';
        $doneDir = $this->parentDirectory . 'done/';
        $failedDir = $this->parentDirectory . 'failed/';

        $workDirObject = new \RecursiveDirectoryIterator($workDir);
        $workDirIterator = new \RecursiveIteratorIterator($workDirObject);
        $workDirIterator = new \RegexIterator($workDirIterator, '/^.+\.xml$/i', \RecursiveRegexIterator::ALL_MATCHES);
        $workDirIterator->rewind();

        if (!$workDirIterator->current()) {
            $this->logger->info('No E2OPen files to process.');

            return;
        }

        while ($workDirIterator->current()) {
            $fileName = $workDirIterator->getFileName();

            try {
                $arguments['code'] = 'pcmt_e2open_import';
                $arguments['--config'] = sprintf('{"xmlFilePath": "%s"}', $workDir . $fileName);
                $input = new ArrayInput($arguments);
                $returnCode = $this->executeCommand($output, $input);

                if (0 === $returnCode) {
                    rename($workDirIterator->key(), $doneDir . $fileName);
                    $this->logger->log('import succesful');
                }

                $workDirIterator->next();
            } catch (\Throwable $exception) {
                $output->writeln($exception->getMessage());
                $this->logger->error('Moving file: ' . $fileName . ' to failed directory.');
                rename($workDirIterator->key(), $failedDir . $fileName);
            }
        }
    }

    private function executeCommand(OutputInterface $output, ArrayInput $arrayInput): int
    {
        $command = $this->getApplication()->find('akeneo:batch:job');

        return $command->run($arrayInput, $output);
    }
}
