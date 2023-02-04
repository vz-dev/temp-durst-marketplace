<?php
/**
 * Durst - project - MerchantProductsKeyRequestInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-21
 * Time: 12:24
 */

namespace Pyz\Yves\AppRestApi\Handler\Json\Request;


interface MerchantProductsKeyRequestInterface
{
    // v1
    public const KEY_MERCHANT_ID = 'merchant_id';

    // v2
    public const KEY_BRANCH_ID = 'branch_id';
}
