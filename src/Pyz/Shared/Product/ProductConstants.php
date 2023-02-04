<?php
/**
 * Durst - project - ProductConstants.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 13.09.18
 * Time: 15:58
 */

namespace Pyz\Shared\Product;


use Spryker\Shared\Product\ProductConstants as SprykerProductConstants;

interface ProductConstants extends SprykerProductConstants
{
    public const PRODUCT_EXPORTER_PATH = 'PRODUCT_EXPORTER_PATH';


    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a product
     */
    public const RESOURCE_TYPE_PRODUCT = 'RESOURCE_TYPE_PRODUCT';



    /**
     * Name of type inside Elasticsearch
     */
    public const PRODUCT_SEARCH_TYPE = 'product';
}