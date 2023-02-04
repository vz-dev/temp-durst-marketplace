<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant;

use ArrayObject;
use Generated\Shared\Transfer\BranchDiscountTransfer;
use Generated\Shared\Transfer\MoneyTransfer;
use Money\Money;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Spryker\Yves\Money\Plugin\MoneyPlugin;

class AbstractDiscountHydrator
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var MoneyPlugin
     */
    protected $moneyPlugin;

    /**
     * @param ArrayObject $discounts
     * @param int $idMerchant
     * @param string $sku
     * @param MoneyTransfer $price
     * @return int|null
     */
    protected function getDiscountPriceForBranchAndSku(
        ArrayObject $discounts,
        int $idMerchant,
        string $sku,
        MoneyTransfer $price
    ): ?int
    {
        /* @var $discount BranchDiscountTransfer */
        foreach ($discounts as $discount) {
            if (
                $discount->getFkBranch() === $idMerchant &&
                (string)$discount->getDiscountSku() === $sku
            ) {
                /* @var $currentPrice Money */
                $currentPrice = Money::EUR((int)$price->getAmount());
                /* @var $currentDiscount Money */
                $currentDiscount = Money::EUR((int)$discount->getDiscountPrice());

                $discountedPrice = $currentPrice
                    ->subtract(
                        $currentDiscount
                    );

                $finalDiscount = 0;

                if ($discountedPrice->isNegative() === false) {
                    $finalDiscount = (int)$discountedPrice
                        ->getAmount();
                }

                return $finalDiscount;
            }
        }

        return null;
    }

    /**
     * @param \ArrayObject $discounts
     * @param int $idMerchant
     * @param string $sku
     * @return bool|null
     */
    protected function getIsExpiredDiscountForBranchAndSku(
        ArrayObject $discounts,
        int $idMerchant,
        string $sku
    ): ?bool
    {
        /* @var $discount BranchDiscountTransfer */
        foreach ($discounts as $discount) {
            if (
                $discount->getFkBranch() === $idMerchant &&
                (string)$discount->getDiscountSku() === $sku
            ) {
                return $discount->getIsExpiredDiscount();
            }
        }

        return null;
    }

    /**
     * @param \ArrayObject $discounts
     * @param int $idMerchant
     * @param string $sku
     * @return bool|null
     */
    protected function getIsCarouselForBranchAndSku(
        ArrayObject $discounts,
        int $idMerchant,
        string $sku
    ): ?bool
    {
        /* @var $discount BranchDiscountTransfer */
        foreach ($discounts as $discount) {
            if (
                $discount->getFkBranch() === $idMerchant &&
                (string)$discount->getDiscountSku() === $sku
            ) {
                return $discount->getIsCarousel();
            }
        }

        return null;
    }

    /**
     * @param \ArrayObject $discounts
     * @param int $idMerchant
     * @param string $sku
     * @return int|null
     */
    protected function getCarouselPriorityForBranchAndSku(
        ArrayObject $discounts,
        int $idMerchant,
        string $sku
    ): ?int
    {
        /* @var $discount BranchDiscountTransfer */
        foreach ($discounts as $discount) {
            if (
                $discount->getFkBranch() === $idMerchant &&
                (string)$discount->getDiscountSku() === $sku
            ) {
                return $discount->getCarouselPriority();
            }
        }

        return null;
    }

    /**
     * @param int $discountPrice
     * @param int $volume
     * @return int
     */
    protected function replaceUnitPrice(
        int $discountPrice,
        int $volume
    ): int
    {
        if ($volume <= 0) {
            return 0;
        }

        return ($discountPrice / ($volume / 1000.0));
    }
}
