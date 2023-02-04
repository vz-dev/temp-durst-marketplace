<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Product;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\AbstractDiscountHydrator;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantProductKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\MerchantProductKeyResponseInterface;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class DiscountHydrator extends AbstractDiscountHydrator implements HydratorInterface
{
    /**
     * @param AppRestApiClientInterface $client
     * @param MoneyPlugin $moneyPlugin
     */
    public function __construct(AppRestApiClientInterface $client, MoneyPlugin $moneyPlugin)
    {
        $this->client = $client;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $idBranch = $requestObject->{MerchantProductKeyRequestInterface::KEY_BRANCH_ID};
        $sku = $requestObject->{MerchantProductKeyRequestInterface::KEY_SKU};

        if (is_int($idBranch) === false || $idBranch <= 0 || is_string($sku) === false || $sku === '') {
            return;
        }

        $requestTransfer = (new AppApiRequestTransfer())
            ->setIdBranch($idBranch)
            ->setSku($sku);

        $responseTransfer = $this
            ->client
            ->getDiscountsForProduct(
                $requestTransfer
            );

        $this
            ->hydrateDiscounts(
                $responseTransfer->getDiscounts(),
                $responseObject,
                $idBranch
            );
    }

    /**
     * @param ArrayObject $discounts
     * @param stdClass $responseObject
     * @param int $idBranch
     * @return void
     */
    protected function hydrateDiscounts(ArrayObject $discounts, stdClass $responseObject, int $idBranch): void
    {
        $product = $responseObject->{MerchantProductKeyResponseInterface::KEY_PRODUCT};

        foreach ($product->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNITS} as $unit) {
            $currentSku = $product->{MerchantProductKeyResponseInterface::KEY_PRODUCT_SKU}
                . $unit->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_CODE};

            $volume = $unit->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_VOLUME};

            foreach ($unit->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICES} as $price) {
                $priceOriginal = $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_PRICE};

                $currentPrice = $this
                    ->moneyPlugin
                    ->fromInteger($priceOriginal);

                $discountPrice = $this
                    ->getDiscountPriceForBranchAndSku(
                        $discounts,
                        $idBranch,
                        $currentSku,
                        $currentPrice
                    );

                if ($discountPrice !== null) {
                    $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_PRICE_ORIGINAL} = $priceOriginal;
                    $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_PRICE} = $discountPrice;
                    $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_DISCOUNT} = ($priceOriginal - $discountPrice);

                    $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_UNIT_PRICE} = $this->replaceUnitPrice(
                        $discountPrice,
                        $volume
                    );

                    $isExpiredDiscount = $this
                        ->getIsExpiredDiscountForBranchAndSku(
                            $discounts,
                            $idBranch,
                            $currentSku
                        );

                    if ($isExpiredDiscount !== null) {
                        $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_IS_EXPIRED_DISCOUNT} = $isExpiredDiscount;
                    }

                    $isCarousel = $this
                        ->getIsCarouselForBranchAndSku(
                            $discounts,
                            $idBranch,
                            $currentSku
                        );

                    if ($isCarousel !== null) {
                        $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_IS_CAROUSEL} = $isCarousel;
                    }

                    $carouselPriority = $this
                        ->getCarouselPriorityForBranchAndSku(
                            $discounts,
                            $idBranch,
                            $currentSku
                        );

                    if (
                        $carouselPriority !== null &&
                        $isCarousel === true
                    ) {
                        $price->{MerchantProductKeyResponseInterface::KEY_PRODUCT_UNIT_PRICE_CAROUSEL_PRIORITY} = $carouselPriority;
                    }
                }
            }
        }
    }
}
