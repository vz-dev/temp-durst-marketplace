<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Configuration;

use Pyz\Zed\Tour\Communication\Plugin\Configuration\DepositConfigurationProfilePlugin as TourDepositConfigurationProfilePlugin;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Configuration\ProcessConfigurationPluginInterface;

/**
 * Class GraphMastersDepositConfigurationProfilePlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\Configuration
 */
class GraphMastersDepositConfigurationProfilePlugin extends TourDepositConfigurationProfilePlugin
{
    /**
     * @return ProcessConfigurationPluginInterface[]
     */
    public function getProcessConfigurationPlugins(): array
    {
        return $this
            ->getFactory()
            ->getGraphMastersDepositProcesses();
    }
}
