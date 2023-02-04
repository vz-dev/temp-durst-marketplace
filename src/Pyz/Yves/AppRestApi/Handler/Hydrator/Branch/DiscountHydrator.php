<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-19
 * Time: 10:41
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Branch;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchDiscountTransfer;
use Generated\Shared\Transfer\MoneyTransfer;
use Money\Money;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Spryker\Yves\Money\Plugin\MoneyPlugin;
use stdClass;
use Pyz\Yves\AppRestApi\Handler\Json\Request\BranchKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\BranchKeyResponseInterface as Response;

class DiscountHydrator implements HydratorInterface
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
     * DiscountHydrator constructor.
     * @param AppRestApiClientInterface $client
     * @param MoneyPlugin $moneyPlugin
     */
    public function __construct(AppRestApiClientInterface $client, MoneyPlugin $moneyPlugin)
    {
        $this->client = $client;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        if ($responseObject->{Response::KEY_ZIP_CODE_MERCHANTS_FOUND} !== true) {
            return;
        }

        $requestTransfer = (new AppApiRequestTransfer())
            ->setBranchIds($this->getBranchIds($requestObject, $responseObject));

        $responseTransfer = $this
            ->client
            ->getDiscounts($requestTransfer);

        $this
            ->hydrateDiscounts(
                $responseTransfer->getDiscounts(),
                $responseObject
            );
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return array
     */
    protected function getBranchIds(\stdClass $requestObject, \stdClass $responseObject) : array
    {
        if ($requestObject->{Request::KEY_MERCHANT_ID} > 0) {
            return [
                $requestObject->{Request::KEY_MERCHANT_ID}
            ];
        }

        $branchIds = [];
        foreach ($responseObject->{Response::KEY_MERCHANTS} as $merchant) {
            $branchIds[] = $merchant->{Response::KEY_MERCHANTS_ID};
        }

        return $branchIds;
    }

    /**
     * @param BranchDiscountTransfer[]|\ArrayObject $discounts
     * @param stdClass $responseObject
     *
     * @return void
     */
    protected function hydrateDiscounts($discounts, \stdClass $responseObject)
    {
        foreach ($responseObject->{Response::KEY_CATEGORIES} as $categories) {
            foreach ($categories->{Response::KEY_CATEGORY_PRODUCTS} as $product) {
                foreach ($product->{Response::KEY_CATEGORY_PRODUCT_UNITS} as $unit) {

                    $currentSku = $unit->{Response::KEY_CATEGORY_PRODUCT_UNIT_SKU};

                    $volume = $unit->{Response::KEY_CATEGORY_PRODUCT_UNIT_ATTRIBUTE_VOLUME};

                    foreach ($unit->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICES} as $price) {

                        $currentBranch = $price->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_MERCHANT_ID};

                        $priceOriginal = $price->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE};

                        $currentPrice = $this
                            ->moneyPlugin
                            ->fromFloat($priceOriginal);

                        $discountPrice = $this
                            ->getDiscountPriceForBranchAndSku(
                                $discounts,
                                $currentBranch,
                                $currentSku,
                                $currentPrice
                            );

                        if ($discountPrice !== null) {
                            $price->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE_ORIGINAL} = $priceOriginal;
                            $price->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_PRICE} = $discountPrice;
                            $price->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_DISCOUNT} = round(($priceOriginal - $discountPrice), 2);

                            $price->{Response::KEY_CATEGORY_PRODUCT_UNIT_PRICE_UNIT_PRICE} = $this
                                ->replaceUnitPrice($discountPrice, $volume);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param BranchDiscountTransfer[]|\ArrayObject $discounts
     * @param int $idBranch
     * @param string $sku
     * @param MoneyTransfer $price
     * @return null|float
     */
    protected function getDiscountPriceForBranchAndSku($discounts, int $idBranch, string $sku, MoneyTransfer $price)
    {
        foreach ($discounts as $discount) {
            if ($discount->getFkBranch() === $idBranch && (string)$discount->getDiscountSku() === $sku) {
                /* @var $currentPrice \Money\Money */
                $currentPrice = Money::EUR((int)$price->getAmount());
                /* @var $currentDiscount \Money\Money */
                $currentDiscount = Money::EUR($discount->getDiscountPrice());

                $discountedPrice = $currentPrice
                    ->subtract($currentDiscount);

                $finalDiscount = 0;

                if ($discountedPrice->isNegative() === false) {
                    $finalDiscount = $this
                        ->moneyPlugin
                        ->convertIntegerToDecimal((int)$discountedPrice->getAmount());
                }

                return $finalDiscount;
            }
        }

        return null;
    }

    /**
     * @param float $discountPrice
     * @param int $volume
     * @return float
     */
    protected function replaceUnitPrice(float $discountPrice, int $volume): float
    {
        if ($volume <= 0) {
            return 0;
        }

        return round(($discountPrice / ($volume / 1000.0)), 2);
    }
}
