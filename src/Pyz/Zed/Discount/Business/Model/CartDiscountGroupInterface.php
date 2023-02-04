<?php
/**
 * Durst - project - CartDiscountGroupInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 12.04.21
 * Time: 10:42
 */

namespace Pyz\Zed\Discount\Business\Model;


use Generated\Shared\Transfer\CartDiscountGroupTransfer;

interface CartDiscountGroupInterface
{
    /**
     * @param int $idCartDiscountGroup
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\CartDiscountGroupTransfer
     */
    public function getCartDiscountGroupById(
        int $idCartDiscountGroup,
        int $idBranch
    ): CartDiscountGroupTransfer;

    /**
     * @param int $idBranch
     * @return array|CartDiscountGroupTransfer[]
     */
    public function getCartDiscountGroupsByBranch(
        int $idBranch
    ): array;

    /**
     * @return array|CartDiscountGroupTransfer[]
     */
    public function generateCartDiscountGroups(): array;
}
