<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - ProductImportDictionary.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.03.18
 * Time: 10:10
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Translator\Dictionary;


use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\Translator\Dictionary\ProductImportDictionary as SprykerEcoProductImportDictionary;

class ProductImportDictionary extends SprykerEcoProductImportDictionary
{
    /**
     * @return array
     */
    public function getDictionary(): array
    {
        $dictionary = parent::getDictionary();

        unset($dictionary['values.price']);

        return $dictionary;
    }
}
