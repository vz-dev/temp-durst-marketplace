<?php

namespace Pyz\Zed\Tour\Communication\Plugin;

use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PreProcessorHookPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class GraphMastersTourExportConfigurationPlugin
 * @package Pyz\Zed\GraphMasters\Communication\Plugin
 */
class GraphMastersTourExportConfigurationPlugin extends TourExportConfigurationPlugin
{
    public const PROCESS_NAME = 'GRAPHMASTERS_TOUR_EXPORT_PROCESS';

    /**
     * @return InputStreamPluginInterface
     */
    public function getInputStreamPlugin(): InputStreamPluginInterface
    {
        return $this
            ->getFactory()
            ->getGraphMastersTourExportInputStreamPlugin();
    }

    /**
     * @return OutputStreamPluginInterface
     */
    public function getOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return $this
            ->getFactory()
            ->getGraphMastersTourExportOutputStreamPlugin();
    }

    /**
     * @return PreProcessorHookPluginInterface[]
     *
     * @throws ContainerKeyNotFoundException
     */
    public function getPreProcessorHookPlugins(): array
    {
        return $this
            ->getFactory()
            ->getTourExportPreProcessorPluginsStack(true);
    }
}
