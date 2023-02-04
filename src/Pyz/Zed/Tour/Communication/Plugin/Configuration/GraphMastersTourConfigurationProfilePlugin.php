<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Configuration;

use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ProcessConfigurationPluginInterface;

/**
 * Class GraphMastersConfigurationProfilePlugin
 * @package Pyz\Zed\GraphMasters\Communication\Plugin\Configuration
 */
class GraphMastersTourConfigurationProfilePlugin extends TourConfigurationProfilePlugin
{
    /**
     * @return ProcessConfigurationPluginInterface[]
     */
    public function getProcessConfigurationPlugins(): array
    {
        return $this
            ->getFactory()
            ->getGraphMastersTourProcesses();
    }
}
