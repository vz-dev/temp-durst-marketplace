<?php
/**
 * Durst - project - DiscountDateValidation.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 13:07
 */

namespace Pyz\Zed\Discount\Business\Validator;


use DateTime;
use Generated\Shared\Transfer\CartDiscountGroupValidationTransfer;
use Pyz\Zed\Discount\Business\Exception\DiscountValidFromInPastException;
use Pyz\Zed\Discount\Business\Exception\DiscountValidToBeforeValidFromException;
use Pyz\Zed\Discount\Business\Exception\DiscountValidToInPastException;

class CartDiscountGroupDateValidation implements CartDiscountGroupValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
     * @return bool
     * @throws \Pyz\Zed\Discount\Business\Exception\DiscountValidFromInPastException
     * @throws \Pyz\Zed\Discount\Business\Exception\DiscountValidToBeforeValidFromException
     * @throws \Pyz\Zed\Discount\Business\Exception\DiscountValidToInPastException
     */
    public function isValid(
        CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
    ): bool
    {
        $today = (new DateTime('now'))
            ->setTime(
                0,
                0,
                0
            );

        $validFrom = $cartDiscountGroupValidationTransfer
            ->getValidFrom();

        if (is_string($validFrom) === true) {
            $validFrom = new DateTime(
                $cartDiscountGroupValidationTransfer
                    ->getValidFrom()
            );
        }

        $validTo = $cartDiscountGroupValidationTransfer
            ->getValidTo();

        if (is_string($validTo) === true) {
            $validTo = new DateTime(
                $cartDiscountGroupValidationTransfer
                    ->getValidTo()
            );
        }

        if (
            $validFrom < $today &&
            $cartDiscountGroupValidationTransfer->getIdDiscount() === null
        ) {
            throw new DiscountValidFromInPastException(
                DiscountValidFromInPastException::MESSAGE
            );
        }

        if ($validTo < $today) {
            throw new DiscountValidToInPastException(
                DiscountValidToInPastException::MESSAGE
            );
        }

        if ($validTo < $validFrom) {
            throw new DiscountValidToBeforeValidFromException(
                DiscountValidToBeforeValidFromException::MESSAGE
            );
        }

        return true;
    }
}
