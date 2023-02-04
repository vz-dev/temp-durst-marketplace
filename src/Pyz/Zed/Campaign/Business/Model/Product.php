<?php
/**
 * Durst - project - Product.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 06.07.21
 * Time: 15:11
 */

namespace Pyz\Zed\Campaign\Business\Model;


use DateTime;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Generated\Shared\Transfer\PossibleCampaignProductTransfer;
use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\Product\Persistence\SpyProduct;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Shared\Discount\DiscountConstants;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Service\UtilEncoding\UtilEncodingServiceInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class Product implements ProductInterface
{
    protected const KEY_NAME = 'name';
    protected const KEY_UNIT = 'unit';
    protected const KEY_UNIT_IMAGE_BOTTLE = 'bottleshot_product_unit';

    protected const SKU_OPTIONS_TEMPLATE = '%s - %s / %s (%s)';

    /**
     * @var \Pyz\Zed\Campaign\Business\CampaignFacadeInterface
     */
    protected $facade;

    /**
     * @var \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface
     */
    protected $merchantPriceFacade;

    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * @var \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * @var \Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface
     */
    protected $discountQueryContainer;

    /**
     * @var \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface
     */
    protected $imageUtil;

    /**
     * @var \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * Product constructor.
     * @param \Pyz\Zed\Campaign\Business\CampaignFacadeInterface $facade
     * @param \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface $merchantPriceFacade
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     * @param \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacade
     * @param \Spryker\Zed\Currency\Business\CurrencyFacadeInterface $currencyFacade
     * @param \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface $productQueryContainer
     * @param \Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface $discountQueryContainer
     * @param \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface $imageUtil
     * @param \Spryker\Service\UtilEncoding\UtilEncodingServiceInterface $encodingService
     */
    public function __construct(
        CampaignFacadeInterface $facade,
        MerchantPriceFacadeInterface $merchantPriceFacade,
        DiscountFacadeInterface $discountFacade,
        MoneyFacadeInterface $moneyFacade,
        CurrencyFacadeInterface $currencyFacade,
        ProductQueryContainerInterface $productQueryContainer,
        DiscountQueryContainerInterface $discountQueryContainer,
        ImageUtilInterface $imageUtil,
        UtilEncodingServiceInterface $encodingService
    )
    {
        $this->facade = $facade;
        $this->merchantPriceFacade = $merchantPriceFacade;
        $this->discountFacade = $discountFacade;
        $this->moneyFacade = $moneyFacade;
        $this->currencyFacade = $currencyFacade;
        $this->productQueryContainer = $productQueryContainer;
        $this->discountQueryContainer = $discountQueryContainer;
        $this->imageUtil = $imageUtil;
        $this->utilEncodingService = $encodingService;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCampaignPeriod
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|PossibleCampaignProductTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Exception
     */
    public function findAvailableProductsForCampaign(
        int $idCampaignPeriod,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array
    {
        $period = $this
            ->getCampaignPeriod(
                $idCampaignPeriod
            );

        $startDate = $period
            ->getCampaignStartDate();

        if (is_string($startDate)) {
            $startDate = new DateTime($startDate);
        }

        $endDate = $period
            ->getCampaignEndDate();

        if (is_string($endDate)) {
            $endDate = new DateTime($endDate);
        }

        return $this
            ->findAvailableProductsForDateRange(
                $startDate,
                $endDate,
                $idBranch,
                $sku,
                $exceptions
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime $validFrom
     * @param \DateTime $validTo
     * @param int $idBranch
     * @param string $sku
     * @param array $exceptions
     * @return array|PossibleCampaignProductTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function findAvailableProductsForDateRange(
        DateTime $validFrom,
        DateTime $validTo,
        int $idBranch,
        string $sku,
        array $exceptions
    ): array
    {
        $exceptions = $this
            ->getExcludedSkus(
                $validFrom,
                $validTo,
                $idBranch,
                $exceptions
            );

        $products = $this
            ->getProductsForBranchExcludingSkus(
                $idBranch,
                $sku,
                $exceptions
            );

        $result = [];

        foreach ($products as $product) {
            $result[] = $this
                ->createPossibleCampaignProductTransfer(
                    $product
                );
        }

        return $result;
    }

    /**
     * @param \DateTime $validFrom
     * @param \DateTime $validTo
     * @param int $idBranch
     * @param array $exceptions
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getExcludedSkus(
        DateTime $validFrom,
        DateTime $validTo,
        int $idBranch,
        array $exceptions
    ): array
    {
        $discountedSkus = $this
            ->getDiscountSkusForBranchByDateRange(
                $validFrom,
                $validTo,
                $idBranch
            );

        if (is_array($exceptions) && count($exceptions) > 0) {
            $discountedSkus = array_unique(
                array_merge(
                    $discountedSkus,
                    $exceptions
                )
            );
        }

        return array_filter($discountedSkus);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $sku
     * @return \Generated\Shared\Transfer\PossibleCampaignProductTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getProductBySkuForBranch(
        int $idBranch,
        string $sku
    ): PossibleCampaignProductTransfer
    {
        $product = $this
            ->getProduct(
                $idBranch,
                $sku
            );

        return $this
            ->createPossibleCampaignProductTransfer(
                $product
            );
    }

    /**
     * @param \DateTime $validFrom
     * @param \DateTime $validTo
     * @param int $idBranch
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getDiscountSkusForBranchByDateRange(
        DateTime $validFrom,
        DateTime $validTo,
        int $idBranch
    ): array
    {
        $discounts = $this
            ->discountQueryContainer
            ->getDiscountsByIdBranch(
                $idBranch
            )
            ->condition(
                'condition1',
                'SpyDiscount.ValidFrom <= ?',
                $validFrom
            )
            ->condition(
                'condition2',
                'SpyDiscount.ValidTo >= ?',
                $validFrom
            )
            ->where(
                [
                    'condition1',
                    'condition2'
                ],
                Criteria::LOGICAL_AND
            )
            ->condition(
                'condition3',
                'SpyDiscount.ValidFrom <= ?',
                $validTo
            )
            ->condition(
                'condition4',
                'SpyDiscount.ValidTo >= ?',
                $validTo
            )
            ->_or()
            ->where(
                [
                    'condition3',
                    'condition4'
                ],
                Criteria::LOGICAL_AND
            )
            ->condition(
                'condition5',
                'SpyDiscount.ValidFrom >= ?',
                $validFrom
            )
            ->condition(
                'condition6',
                'SpyDiscount.ValidTo <= ?',
                $validTo
            )
            ->_or()
            ->where(
                [
                    'condition5',
                    'condition6'
                ],
                Criteria::LOGICAL_AND
            )
            ->filterByIsActive(
                true
            )
            ->filterByDiscountType(
                DiscountConstants::TYPE_CART_RULE
            )
            ->find();

        $skus = [];

        foreach ($discounts as $discount) {
            if (in_array($discount->getDiscountSku(), $skus)) {
                continue;
            }
            $skus[$discount->getIdDiscount()] = $discount
                ->getDiscountSku();
        }

        return $skus;
    }

    /**
     * @param int $idCampaignPeriod
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    protected function getCampaignPeriod(
        int $idCampaignPeriod
    ): CampaignPeriodTransfer
    {
        return $this
            ->facade
            ->getCampaignPeriodById(
                $idCampaignPeriod
            );
    }

    /**
     * @param int $idBranch
     * @param string $sku
     * @return \Orm\Zed\Product\Persistence\SpyProduct|null
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getProduct(
        int $idBranch,
        string $sku
    ): ?SpyProduct
    {
        return $this
            ->productQueryContainer
            ->queryProduct()
            ->joinWithSpyProductAbstract()
            ->joinWithMerchantPrice()
            ->useMerchantPriceQuery()
                ->filterByFkBranch(
                    $idBranch
                )
                ->filterByIsActive(
                    true
                )
            ->endUse()
            ->filterBySku(
                $sku
            )
            ->find()
            ->getFirst();
    }

    /**
     * @param int $idBranch
     * @param string|null $sku
     * @param array $excludeSkus
     * @return array
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function getProductsForBranchExcludingSkus(
        int $idBranch,
        string $sku = null,
        array $excludeSkus = []
    ): array
    {
        $query = $this
            ->productQueryContainer
            ->queryProduct()
            ->joinWithSpyProductAbstract()
            ->joinWithMerchantPrice()
            ->useMerchantPriceQuery()
                ->filterByFkBranch(
                    $idBranch
                )
                ->filterByIsActive(
                    true
                )
            ->endUse();

        if (
            $sku !== null &&
            !empty($sku)
        ) {
            $query
                ->where(
                    "(" . SpyProductTableMap::COL_ATTRIBUTES . "::json->'" . static::KEY_NAME . "')::text ILIKE ?",
                    '%' . $sku . '%'
                )
                ->_or()
                ->filterBySku(
                    '%' . $sku . '%',
                    Criteria::ILIKE
                )
                ->_or()
                ->useMerchantPriceQuery()
                    ->filterByMerchantSku(
                        '%' . $sku . '%',
                        Criteria::ILIKE
                    )
                ->endUse();
        }

        if (is_array($excludeSkus) && count($excludeSkus) > 0) {
            $query
                ->filterBySku(
                    $excludeSkus,
                    Criteria::NOT_IN
                );
        }

        $productEntities = $query
            ->find();

        $products = [];

        foreach ($productEntities as $productEntity) {
            $products[] = $productEntity;
        }

        return $products;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct|null $product
     * @return \Generated\Shared\Transfer\PossibleCampaignProductTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function createPossibleCampaignProductTransfer(
        ?SpyProduct $product
    ): PossibleCampaignProductTransfer
    {
        $transfer = new PossibleCampaignProductTransfer();

        if ($product === null) {
            return $transfer;
        }

        /* @var $merchantPrice MerchantPrice */
        $merchantPrice = $product
            ->getMerchantPrices()
            ->getFirst();

        $merchantSku = $merchantPrice
            ->getMerchantSku();

        $productName = $this
            ->getProductName(
                $product
            );

        $productUnit = $this
            ->getProductUnit(
                $product
            );

        $productPrice = $this
            ->getProductPrice(
                $merchantPrice
            );

        $transfer
            ->setFallbackText(
                sprintf(
                    static::SKU_OPTIONS_TEMPLATE,
                    $merchantSku,
                    $productName,
                    $productUnit,
                    $productPrice
                )
            )
            ->setId(
                $product
                    ->getSku()
            )
            ->setProductPrice(
                $productPrice
            )
            ->setProductPriceValue(
                $merchantPrice ? $merchantPrice->getGrossPrice() : 0
            )
            ->setProductUnit(
                $productUnit
            )
            ->setProductName(
                $productName
            )
            ->setThumbProductImage(
                $this
                    ->getThumbImage(
                        $product
                    )
            )
            ->setSku(
                $product
                    ->getSku()
            )
            ->setMerchantSku(
                $merchantSku
            )->setStatus(
                $merchantPrice->getStatus()
            );

        return $transfer;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $product
     * @return string
     */
    protected function getProductName(
        SpyProduct $product
    ): string
    {
        $attributes = $this
            ->getAttributes(
                $product
            );

        $productName = 'n/a';

        if (array_key_exists(static::KEY_NAME, $attributes) === true) {
            $productName = $attributes[static::KEY_NAME];
        }

        return $productName;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $product
     * @return string
     */
    protected function getProductUnit(
        SpyProduct $product
    ): string
    {
        $attributes = $this
            ->getAttributes(
                $product
            );

        $productUnit = 'n/a';

        if (array_key_exists(static::KEY_UNIT, $attributes) === true) {
            $productUnit = $attributes[static::KEY_UNIT];
        }

        return $productUnit;
    }

    /**
     * @param \Orm\Zed\MerchantPrice\Persistence\MerchantPrice $merchantPrice
     * @return string
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function getProductPrice(
        MerchantPrice $merchantPrice
    ): string
    {
        $store = $this
            ->currencyFacade
            ->getCurrentStoreWithCurrencies();

        /* @var $mainCurrency \Generated\Shared\Transfer\CurrencyTransfer */
        $mainCurrency = $store
            ->getCurrencies()
            ->offsetGet(0);

        $moneyTransfer = $this
            ->moneyFacade
            ->fromInteger(
                $merchantPrice
                    ->getGrossPrice(),
                $mainCurrency
                    ->getCode()
            );

        return $this
            ->moneyFacade
            ->formatWithSymbol(
                $moneyTransfer
            );
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $product
     * @return string
     */
    protected function getThumbImage(
        SpyProduct $product
    ): string
    {
        $attributes = $this
            ->getAttributes(
                $product
            );

        return $this
            ->imageUtil
            ->formatThumb(
                array_key_exists(
                    static::KEY_UNIT_IMAGE_BOTTLE, $attributes) ?
                    $attributes[static::KEY_UNIT_IMAGE_BOTTLE] :
                    null
            );
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $product
     * @return array
     */
    protected function getAttributes(
        SpyProduct $product
    ): array
    {
        $attributes = $product
            ->getAttributes();

        if (is_string($attributes) === true) {
            $attributes = $this
                ->utilEncodingService
                ->decodeJson(
                    $attributes,
                    true
                );
        }

        return $attributes;
    }
}
