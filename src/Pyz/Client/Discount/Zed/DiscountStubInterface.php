<?php
/**
 * Durst - project - DiscountStubInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.09.20
 * Time: 14:36
 */

namespace Pyz\Client\Discount\Zed;


use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\DiscountApiResponseTransfer;

interface DiscountStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\DiscountApiResponseTransfer
     */
    public function checkValidVoucher(DiscountApiRequestTransfer $discountApiRequestTransfer): DiscountApiResponseTransfer;
}
