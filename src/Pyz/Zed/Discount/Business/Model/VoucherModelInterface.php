<?php
/**
 * Durst - project - VoucherModelInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 08:33
 */

namespace Pyz\Zed\Discount\Business\Model;


use Generated\Shared\Transfer\DiscountApiRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface VoucherModelInterface
{
    /**
     * @param \Generated\Shared\Transfer\DiscountApiRequestTransfer $discountApiRequestTransfer
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addVoucherCodeToQuote(DiscountApiRequestTransfer $discountApiRequestTransfer): QuoteTransfer;
}
