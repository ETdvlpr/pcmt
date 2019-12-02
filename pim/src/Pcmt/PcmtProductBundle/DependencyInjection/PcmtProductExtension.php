<?php

declare(strict_types=1);

namespace Pcmt\PcmtProductBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PcmtProductExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        //load and merge configuration
        $configuration = $this->getConfiguration($configs, $container); //instantiate /DependencyInjection/Configuration class
        $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('array_converters.yml');
        $loader->load('attribute_types.yml');
        $loader->load('comparators.yml');
        $loader->load('controllers.yml');
        $loader->load('event_subscribers.yml');
        $loader->load('entities.yml');
        $loader->load('factories.yml');
        $loader->load('fetchers.yml');
        $loader->load('forms.yml');
        $loader->load('handlers.yml');
        $loader->load('jobs.yml');
        $loader->load('job_defaults.yml');
        $loader->load('job_constraints.yml');
        $loader->load('normalizers.yml');
        $loader->load('processors.yml');
        $loader->load('providers.yml');
        $loader->load('queries.yml');
        $loader->load('readers.yml');
        $loader->load('renderers.yml');
        $loader->load('repositories.yml');
        $loader->load('savers.yml');
        $loader->load('services.yml');
        $loader->load('steps.yml');
        $loader->load('updaters.yml');
        $loader->load('utils.yml');
        $loader->load('widget.yml');
        $loader->load('writers.yml');
    }

    public function getAlias(): string
    {
        return 'pcmt_product';
    }
}