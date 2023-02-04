<?php
/**
 * Durst - project - DiscountAvailableValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.05.21
 * Time: 09:33
 */

namespace Pyz\Zed\Discount\Business\Validator;


use Generated\Shared\Transfer\CartDiscountGroupValidationTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\Discount\Business\Exception\DiscountProductNotAvailableException;

class CartDiscountAvailableGroupValidator implements CartDiscountGroupValidatorInterface
{
    protected const DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    protected const KEY_NAME = 'name';

    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $facade;

    /**
     * DiscountAvailableValidator constructor.
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $facade
     */
    public function __construct(
        DiscountFacadeInterface $facade
    )
    {
        $this->facade = $facade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
     * @return bool
     * @throws \Pyz\Zed\Discount\Business\Exception\DiscountProductNotAvailableException
     */
    public function isValid(
        CartDiscountGroupValidationTransfer $cartDiscountGroupValidationTransfer
    ): bool
    {
        $validFrom = $cartDiscountGroupValidationTransfer
            ->getValidFrom();

        if ($validFrom instanceOf \DateTime) {
            $validFrom = $validFrom
                ->format(
                    static::DATETIME_FORMAT
                );
        }

        $validTo = $cartDiscountGroupValidationTransfer
            ->getValidTo();

        if ($validTo instanceOf \DateTime) {
            $validTo = $validTo
                ->format(
                    static::DATETIME_FORMAT
                );
        }

        $discountedSkus = $this
            ->facade
            ->getDiscountedSkuForBranchByStartAndEnd(
                $validFrom,
                $validTo,
                $cartDiscountGroupValidationTransfer
                    ->getFkBranch()
            );

        foreach ($discountedSkus as $idDiscount => $discountedSku) {
            if (
                $discountedSku === $cartDiscountGroupValidationTransfer->getDiscountSku() &&
                $idDiscount === $cartDiscountGroupValidationTransfer->getIdDiscount()
            ) {
                continue;
            }

            if ($discountedSku === $cartDiscountGroupValidationTransfer->getDiscountSku()) {
                $productName = $this->facade->getProductNameOfDiscountByBranchId(
                    $cartDiscountGroupValidationTransfer->getFkBranch(),
                    $discountedSku
                );
                throw new DiscountProductNotAvailableException(
                    sprintf(
                        DiscountProductNotAvailableException::MESSAGE,
                        json_decode($productName)->{static::KEY_NAME},
                        $discountedSku
                    )
                );
            }
        }

        return true;
    }
}
