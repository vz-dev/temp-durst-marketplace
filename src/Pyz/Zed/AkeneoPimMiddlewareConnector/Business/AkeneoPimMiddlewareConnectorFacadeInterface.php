<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - AkeneoPimMiddlewareConnectorFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.03.18
 * Time: 09:50
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\AkeneoPimMiddlewareConnectorFacadeInterface as SprykerEcoAkeneoPimMiddlewareConnectorFacadeInterface;


interface AkeneoPimMiddlewareConnectorFacadeInterface extends SprykerEcoAkeneoPimMiddlewareConnectorFacadeInterface
{
    /**
     * @param array $data
     *
     * @return void
     */
    public function importCategories(array $data);

    /**
     * @param array $data
     *
     * @return void
     */
    public function importAttributes(array $data);

    /**
     * @param array $data
     *
     * @return void
     */
    public function importProductsConcrete(array $data);

    /**
     * @param array $data
     *
     * @return void
     */
    public function importProductsAbstract(array $data);

    /**
     * @param array $data
     *
     * @return void
     */
    public function importAttributeKeys($data);
}