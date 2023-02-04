<?php
/**
 * Durst - project - MerchantPriceConstants.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.06.18
 * Time: 11:24
 */

namespace Pyz\Shared\MerchantPrice;


interface MerchantPriceConstants
{
    const DEFAULT_COUNTRY_ISO_3_CODE = 'DEFAULT_COUNTRY_ISO_3_CODE';

    public const COUNT_SOLD_ITEMS = 'COUNT_SOLD_ITEMS';

    public const PRICE_MODE_NET_NAME  = 'net';
    public const PRICE_MODE_GROSS_NAME = 'gross';

    /**
     * This is used for the collector as an identification in the touch table,
     * that the touched item is a merchant price
     */
    public const RESOURCE_TYPE_PRICE = 'RESOURCE_TYPE_PRICE';

    /**
     * Name of type inside Elasticsearch
     */
    public const PRICE_SEARCH_TYPE = 'price';

    /**
     * Type of statuses for prices
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';
}
