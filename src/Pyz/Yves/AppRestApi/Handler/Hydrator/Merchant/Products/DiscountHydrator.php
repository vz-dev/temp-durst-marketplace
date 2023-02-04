<?php
/**
 * Durst - project - DiscountHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-22
 * Time: 10:29
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\Products;


use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\Controller\MerchantProductsController;
use Pyz\Yves\AppRestApi\Handler\Hydrator\Merchant\AbstractDiscountHydrator;
use Pyz\Yves\AppRestApi\Handler\Hydrator\VersionedHydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\MerchantProductsKeyRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\MerchantProductsKeyResponseInterface;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;

class DiscountHydrator extends AbstractDiscountHydrator implements VersionedHydratorInterface
{
    /**
     * @var string
     */
    protected $version;

    /**
     * DiscountHydrator constructor.
     * @param AppRestApiClientInterface $client
     * @param MoneyPlugin $moneyPlugin
     */
    public function __construct(
        AppRestApiClientInterface $client,
        MoneyPlugin $moneyPlugin
    )
    {
        $this->client = $client;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param string $version
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(string $version, stdClass $requestObject, stdClass $responseObject)
    {
        $this->version = $version;

        if ($this->version === MerchantProductsController::VERSION_1) {
            $requestKey = MerchantProductsKeyRequestInterface::KEY_MERCHANT_ID;
        }

        if ($this->version === MerchantProductsController::VERSION_2 || $this->version === MerchantProductsController::VERSION_3 ) {
            $requestKey = MerchantProductsKeyRequestInterface::KEY_BRANCH_ID;
        }

        $idMerchant = $requestObject->{$requestKey};

        if (is_int($idMerchant) === false || $idMerchant <= 0) {
            return;
        }

        $requestTransfer = (new AppApiRequestTransfer())
            ->setBranchIds(
                [
                    $idMerchant
                ]
            );

        $responseTransfer = $this
            ->client
            ->getDiscounts(
                $requestTransfer
            );

        $this
            ->hydrateDiscounts(
                $responseTransfer->getDiscounts(),
                $responseObject,
                $idMerchant
            );
    }

    /**
     * @param ArrayObject $discounts
     * @param stdClass $responseObject
     * @param int $idMerchant
     * @return void
     */
    protected function hydrateDiscounts(ArrayObject $discounts, stdClass $responseObject, int $idMerchant): void
    {
        foreach ($responseObject->{MerchantProductsKeyResponseInterface::KEY_CATEGORIES} as $category) {
            $this->hydrateDiscountItems($category, $discounts, $idMerchant);

            if($this->version === MerchantProductsController::VERSION_3)
            {
                if(isset($category->{MerchantProductsKeyResponseInterface::KEY_SUBCATEGORIES})){
                    foreach($category->{MerchantProductsKeyResponseInterface::KEY_SUBCATEGORIES} as $subCategory){
                        $this->hydrateDiscountItems($subCategory, $discounts, $idMerchant);
                    }
                }
            }
        }
    }

    /**
     * @param stdClass $category
     * @param ArrayObject $discounts
     * @param int $idMerchant
     */
    protected function hydrateDiscountItems(stdClass $category, ArrayObject $discounts, int $idMerchant) : void
    {
        foreach ($category->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCTS} as $product) {
            foreach ($product->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNITS} as $unit) {
                if ($this->version === MerchantProductsController::VERSION_1) {
                    $currentSku = $unit->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_SKU};
                }

                if ($this->version === MerchantProductsController::VERSION_2 || $this->version === MerchantProductsController::VERSION_3) {
                    $currentSku = $product->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_SKU}
                        . $unit->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_CODE};
                }

                $volume = $unit->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_VOLUME};

                foreach ($unit->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICES} as $price) {
                    $priceOriginal = $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE};

                    $currentPrice = $this
                        ->moneyPlugin
                        ->fromInteger($priceOriginal);

                    $discountPrice = $this
                        ->getDiscountPriceForBranchAndSku(
                            $discounts,
                            $idMerchant,
                            $currentSku,
                            $currentPrice
                        );

                    if ($discountPrice !== null) {
                        $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE_ORIGINAL} = $priceOriginal;
                        $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE} = $discountPrice;
                        $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_DISCOUNT} = ($priceOriginal - $discountPrice);

                        if ($this->version === MerchantProductsController::VERSION_1) {
                            $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_UNIT_PRICE} = $this->replaceUnitPrice(
                                $discountPrice,
                                $volume
                            );
                        }

                        $isExpiredDiscount = $this
                            ->getIsExpiredDiscountForBranchAndSku(
                                $discounts,
                                $idMerchant,
                                $currentSku
                            );

                        if ($isExpiredDiscount !== null) {
                            $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_IS_EXPIRED_DISCOUNT} = $isExpiredDiscount;
                        }

                        $isCarousel = $this
                            ->getIsCarouselForBranchAndSku(
                                $discounts,
                                $idMerchant,
                                $currentSku
                            );

                        if ($isCarousel !== null) {
                            $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_IS_CAROUSEL} = $isCarousel;
                        }

                        $carouselPriority = $this
                            ->getCarouselPriorityForBranchAndSku(
                                $discounts,
                                $idMerchant,
                                $currentSku
                            );

                        if (
                            $carouselPriority !== null &&
                            $isCarousel === true
                        ) {
                            $price->{MerchantProductsKeyResponseInterface::KEY_CATEGORY_PRODUCT_UNIT_PRICE_CAROUSEL_PRIORITY} = $carouselPriority;
                        }
                    }
                }
            }
        }
    }
}
