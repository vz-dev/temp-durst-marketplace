<?php
/**
 * Durst - project - CartDiscountGroupDiscountHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 11:45
 */

namespace Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup;


use Generated\Shared\Transfer\CartDiscountGroupDiscountTransfer;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroup;

interface CartDiscountGroupDiscountHydratorInterface
{
    /**
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupDiscountTransfer $cartDiscountGroupDiscountTransfer
     * @return void
     */
    public function hydrateCartDiscountGroupDiscount(
        DstCartDiscountGroup $cartDiscountGroup,
        CartDiscountGroupDiscountTransfer $cartDiscountGroupDiscountTransfer
    ):void;
}
