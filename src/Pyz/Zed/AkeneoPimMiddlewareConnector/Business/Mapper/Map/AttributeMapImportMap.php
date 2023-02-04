<?php
/**
 * Durst - project - AttributeMapImportMap.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.04.19
 * Time: 16:13
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Mapper\Map;

use SprykerEco\Zed\AkeneoPimMiddlewareConnector\Business\Mapper\Map\AttributeMapImportMap as SprykerEcoAttributeMapImportMap;

class AttributeMapImportMap extends SprykerEcoAttributeMapImportMap
{
    protected const ATTRIBUTE_MAP = [
        'pim_catalog_boolean' => 'number',
        'pim_catalog_textarea' => 'textarea',
        'pim_catalog_text' => 'text',
        'pim_catalog_simpleselect' => 'select',
        'pim_reference_data_multiselect' => 'text',
        'pim_reference_data_simpleselect' => 'text',
        'pim_catalog_price_collection' => 'text',
        'pim_catalog_number' => 'number',
        'pim_catalog_multiselect' => 'text',
        'pim_catalog_metric' => 'text',
        'pim_catalog_image' => 'text',
        'pim_catalog_identifier' => 'text',
        'pim_catalog_file' => 'text',
        'pim_catalog_date' => 'date',
        'pim_assets_collection' => 'text',
    ];
}