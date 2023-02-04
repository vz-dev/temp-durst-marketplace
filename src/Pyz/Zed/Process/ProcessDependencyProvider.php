<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - ProcessDependencyProvider.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.03.18
 * Time: 14:37
 */

namespace Pyz\Zed\Process;

use Pyz\Zed\Accounting\Communication\Plugin\Configuration\RealaxConfigurationProfilePlugin;
use Pyz\Zed\Accounting\Communication\Plugin\Configuration\RealaxFixedConfigurationProfilePlugin;
use Pyz\Zed\Product\Communication\Plugin\Configuration\ProductExporterConfigurationProfilePlugin;
use Pyz\Zed\Tour\Communication\Plugin\Configuration\DepositConfigurationProfilePlugin;
use Pyz\Zed\Tour\Communication\Plugin\Configuration\GraphMastersDepositConfigurationProfilePlugin;
use Pyz\Zed\Tour\Communication\Plugin\Configuration\GraphMastersTourConfigurationProfilePlugin;
use Pyz\Zed\Tour\Communication\Plugin\Configuration\TourConfigurationProfilePlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\Configuration\AkeneoPimConfigurationProfilePlugin;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin\Configuration\DefaultAkeneoPimConfigurationProfilePlugin;
use SprykerMiddleware\Zed\Process\Communication\Plugin\Configuration\DefaultConfigurationProfilePlugin;
use SprykerMiddleware\Zed\Process\ProcessDependencyProvider as SprykerMiddlewareProcessDependencyProvider;


class ProcessDependencyProvider extends SprykerMiddlewareProcessDependencyProvider
{
    /**
     * @return array
     */
    protected function getConfigurationProfilePluginsStack(): array
    {
        $profileStack = parent::getConfigurationProfilePluginsStack();
        $profileStack[] = new AkeneoPimConfigurationProfilePlugin();
        $profileStack[] = new DefaultAkeneoPimConfigurationProfilePlugin();
        $profileStack[] = new DefaultConfigurationProfilePlugin();
        $profileStack[] = new ProductExporterConfigurationProfilePlugin();
        $profileStack[] = new TourConfigurationProfilePlugin();
        $profileStack[] = new DepositConfigurationProfilePlugin();
        $profileStack[] = new RealaxConfigurationProfilePlugin();
        $profileStack[] = new RealaxFixedConfigurationProfilePlugin();
        $profileStack[] = new GraphMastersTourConfigurationProfilePlugin();
        $profileStack[] = new GraphMastersDepositConfigurationProfilePlugin();

        return $profileStack;
    }
}
