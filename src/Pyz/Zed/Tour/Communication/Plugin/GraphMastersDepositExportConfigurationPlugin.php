<?php

namespace Pyz\Zed\Tour\Communication\Plugin;

use Pyz\Zed\Tour\Communication\Plugin\DepositExportConfigurationPlugin as TourDepositExportConfigurationPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PreProcessorHookPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\InputStreamPluginInterface;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Stream\OutputStreamPluginInterface;

/**
 * Class GraphMastersDepositExportConfigurationPlugin
 * @package Pyz\Zed\GraphMasters\Communication\Plugin
 */
class GraphMastersDepositExportConfigurationPlugin extends TourDepositExportConfigurationPlugin
{
    public const PROCESS_NAME = 'GRAPHMASTERS_DEPOSIT_EXPORT_PROCESS';

    /**
     * @return InputStreamPluginInterface
     */
    public function getInputStreamPlugin(): InputStreamPluginInterface
    {
        return $this
            ->getFactory()
            ->getGraphMastersDepositExportInputStreamPlugin();
    }

    /**
     * @return OutputStreamPluginInterface
     */
    public function getOutputStreamPlugin(): OutputStreamPluginInterface
    {
        return $this
            ->getFactory()
            ->getGraphMastersDepositExportOutputStreamPlugin();
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
            ->getDepositExportPreProcessorPluginsStack(true);
    }
}
