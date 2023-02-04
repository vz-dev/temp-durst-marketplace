<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - AkeneoPimMiddlewareConnectorFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.03.18
 * Time: 09:51
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business;

use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\AkeneoPimMiddlewareConnectorFacade as SprykerEcoAkeneoPimMiddlewareConnectorFacade;

/**
 * Class AkeneoPimMiddlewareConnectorFacade
 * @package Pyz\Zed\AkeneoPimMiddlewareConnector\Business
 * @method AkeneoPimMiddlewareConnectorBusinessFactory getFactory()
 */
class AkeneoPimMiddlewareConnectorFacade extends SprykerEcoAkeneoPimMiddlewareConnectorFacade implements AkeneoPimMiddlewareConnectorFacadeInterface
{
    /**
     * @param array $data
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function importCategories(array $data)
    {
        $this->getFactory()
            ->createCategoryImporter()
            ->import($data);
    }

    /**
     * @param array $data
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function importAttributes(array $data)
    {
        $this->getFactory()
            ->createAttributeImporter()
            ->import($data);
    }

    /**
     * @param array $data
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function importProductsConcrete(array $data)
    {
        $this->getFactory()
            ->createProductConcreteImporter()
            ->import($data);
    }

    /**
     * @param array $data
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function importProductsAbstract(array $data)
    {
        $this->getFactory()
            ->createProductAbstractImporter()
            ->import($data);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function importAttributeKeys($data)
    {
        $this->getFactory()
            ->createAttributeKeyImporter()
            ->import($data);
    }
}