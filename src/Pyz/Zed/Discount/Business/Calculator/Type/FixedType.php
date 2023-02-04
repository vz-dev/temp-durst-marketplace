<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-15
 * Time: 12:49
 */

namespace Pyz\Zed\Discount\Business\Calculator\Type;

use Generated\Shared\Transfer\DiscountableItemTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Spryker\Shared\Discount\DiscountConstants;
use Spryker\Zed\Discount\Business\Calculator\Type\FixedType as SprykerFixedType;

class FixedType extends SprykerFixedType
{
    /**
     * {@inheritdoc}
     *
     * @param array $discountableItems
     * @param DiscountTransfer $discountTransfer
     * @return int
     */
    public function calculateDiscount(array $discountableItems, DiscountTransfer $discountTransfer)
    {
        $amount = $this
            ->getDiscountAmountsForCurrentCurrency(
                $discountableItems,
                $discountTransfer
            );

        if ($amount <= 0) {
            return 0;
        }

        return $amount;
    }

    /**
     * @param array $discountableItems
     * @param DiscountTransfer $discountTransfer
     * @return int
     */
    protected function getDiscountAmountsForCurrentCurrency(array $discountableItems, DiscountTransfer $discountTransfer)
    {
        $currentCurrency = $discountTransfer->getCurrency();

        foreach ($discountTransfer->getMoneyValueCollection() as $moneyValueTransfer) {
            if ($currentCurrency->getCode() !== $moneyValueTransfer->getCurrency()->getCode()) {
                continue;
            }

            $quantity = 1;

            if ($discountTransfer->getDiscountType() !== DiscountConstants::TYPE_VOUCHER) {
                foreach ($discountableItems as $discountableItemTransfer) {
                    $quantity = $this
                        ->getDiscountableObjectQuantity($discountableItemTransfer);
                }
            }

            if ($discountTransfer->getPriceMode() === static::PRICE_NET_MODE) {

                return $quantity * $moneyValueTransfer->getNetAmount();
            }

            return $quantity * $moneyValueTransfer->getGrossAmount();
        }

        return 0;
    }

    /**
     * @param DiscountableItemTransfer $discountableItemTransfer
     * @return int
     */
    protected function getDiscountableObjectQuantity(DiscountableItemTransfer $discountableItemTransfer): int
    {
        $quantity = $discountableItemTransfer->getQuantity();

        if (empty($quantity)) {
            return 1;
        }

        return $quantity;
    }
}
