<?php
/**
 * Durst - project - DiscountValidatorInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 09:30
 */

namespace Pyz\Zed\Discount\Business\Validator;


use Generated\Shared\Transfer\CartDiscountGroupValidationTransfer;

interface CartDiscountGroupValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
     * @return bool
     */
    public function isValid(
        CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
    ): bool;
}
