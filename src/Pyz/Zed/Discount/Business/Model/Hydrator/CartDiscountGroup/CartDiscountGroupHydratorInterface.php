<?php
/**
 * Durst - project - CartDiscountGroupHydratorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 10:48
 */

namespace Pyz\Zed\Discount\Business\Model\Hydrator\CartDiscountGroup;


use Generated\Shared\Transfer\CartDiscountGroupTransfer;
use Orm\Zed\Discount\Persistence\DstCartDiscountGroup;

interface CartDiscountGroupHydratorInterface
{
    /**
     * @param \Orm\Zed\Discount\Persistence\DstCartDiscountGroup $cartDiscountGroup
     * @param \Generated\Shared\Transfer\CartDiscountGroupTransfer $cartDiscountGroupTransfer
     * @return void
     */
    public function hydrateCartDiscountGroup(
        DstCartDiscountGroup $cartDiscountGroup,
        CartDiscountGroupTransfer $cartDiscountGroupTransfer
    ): void;
}
