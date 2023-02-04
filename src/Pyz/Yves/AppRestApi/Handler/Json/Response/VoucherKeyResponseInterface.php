<?php
/**
 * Durst - project - VoucherKeyResponseInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 14:35
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Response;

interface VoucherKeyResponseInterface
{
    public const KEY_VOUCHER_VALID = 'voucher_valid';

    public const KEY_MERCHANT = 'merchant';
    public const KEY_MERCHANT_ID = 'id';
    public const KEY_MERCHANT_NAME = 'name';
    public const KEY_MERCHANT_CITY = 'city';
    public const KEY_MERCHANT_DELIVERY_AREAS = 'delivery_areas';
}
