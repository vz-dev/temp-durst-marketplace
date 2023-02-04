<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - AttributeDataImporterPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.03.18
 * Time: 14:42
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin;

use Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\AkeneoPimMiddlewareConnectorCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Pyz\Zed\AkeneoPimMiddlewareConnector\Business\AkeneoPimMiddlewareConnectorFacadeInterface;
use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Dependency\Plugin\DataImporterPluginInterface;

/**
 * Class AttributeDataImporterPlugin
 * @package Pyz\Zed\AkeneoPimMiddlewareConnector\Communication\Plugin
 * @method AkeneoPimMiddlewareConnectorFacadeInterface getFacade()
 * @method AkeneoPimMiddlewareConnectorCommunicationFactory getFactory()
 */
class AttributeDataImporterPlugin extends AbstractPlugin implements DataImporterPluginInterface
{
    /**
     * @param array $data
     * @throws \Exception
     */
    public function import(array $data): void
    {

        $this->getFacade()
            ->importAttributeKeys($data);
        $this->getFacade()
            ->importAttributes($data);
    }
}