<?php
/**
 * Durst - project - DiscountClientInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 13:56
 */

namespace Pyz\Client\Discount;


use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountApiResponseTransfer;

interface DiscountClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\DiscountApiResponseTransfer
     */
    public function checkValidVoucher(DiscountApiRequestTransfer $discountApiRequestTransfer): DiscountApiResponseTransfer;
}
