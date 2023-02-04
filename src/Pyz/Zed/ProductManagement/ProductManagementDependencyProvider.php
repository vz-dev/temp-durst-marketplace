<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\ProductManagement;

use Spryker\Zed\CmsBlockProductConnector\Communication\Plugin\CmsBlockProductAbstractBlockListViewPlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Money\Communication\Plugin\Form\MoneyFormTypePlugin;
use Spryker\Zed\ProductManagement\ProductManagementDependencyProvider as SprykerProductManagementDependencyProvider;
use Spryker\Zed\Store\Communication\Plugin\Form\StoreRelationToggleFormTypePlugin;

class ProductManagementDependencyProvider extends SprykerProductManagementDependencyProvider
{
    const QUERY_CONTAINER_DEPOSIT = 'QUERY_CONTAINER_DEPOSIT';

    /**
     * @return \Spryker\Zed\ProductManagement\Communication\Plugin\ProductAbstractViewPluginInterface[]
     */
    protected function getProductAbstractViewPlugins()
    {
        return [
            new CmsBlockProductAbstractBlockListViewPlugin(),
        ];
    }

    /**
     * @return \Spryker\Zed\Kernel\Communication\Form\FormTypeInterface
     */
    protected function getStoreRelationFormTypePlugin()
    {
        return new StoreRelationToggleFormTypePlugin();
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Money\Communication\Plugin\Form\MoneyFormTypePlugin
     */
    protected function createMoneyFormTypePlugin(Container $container)
    {
        return new MoneyFormTypePlugin();
    }

    /**
     * @param Container $container
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container =  parent::provideCommunicationLayerDependencies($container);
        $container = $this->addDepositQueryContainer($container);

        return $container;
    }

    /**
     * @param Container $container
     * @return Container
     */
    protected function addDepositQueryContainer(Container $container)
    {
        $container[static::QUERY_CONTAINER_DEPOSIT] = function (Container $container) {
            return $container->getLocator()->deposit()->queryContainer();
        };

        return $container;
    }

}
