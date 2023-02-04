<?php
/**
 * Durst - project - ProductImportMap.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.19
 * Time: 20:43
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Mapper\Map;

use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\Mapper\Map\ProductImportMap as SprykerEcoProductImportMap;

class ProductImportMap extends SprykerEcoProductImportMap
{
    public function getMap(): array
    {
        $map = parent::getMap();

        $map['prices'] = function ($item) {
            $result[] = [
                'price' => null,
                'price_type' => null,
                'currency' => null,
                'store' => null,
                'concrete_sku' => $item['identifier'],
                'value_gross' => null,
                'value_net' => null,
            ];
            return $result;
        };
        $map['abstract_sku'] = 'parent';

        return $map;
    }
}