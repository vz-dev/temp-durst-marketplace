<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-27
 * Time: 12:30
 */

namespace Pyz\Zed\Discount\Business\Model;

use ArrayObject;
use DateTime;
use Generated\Shared\Transfer\BranchDiscountTransfer;
use Generated\Shared\Transfer\DiscountCalculatorTransfer;
use Generated\Shared\Transfer\DiscountConditionTransfer;
use Generated\Shared\Transfer\DiscountConfiguratorTransfer;
use Generated\Shared\Transfer\DiscountGeneralTransfer;
use Generated\Shared\Transfer\DiscountVoucherTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\SpyCurrencyEntityTransfer;
use Generated\Shared\Transfer\SpyDiscountAmountEntityTransfer;
use Generated\Shared\Transfer\SpyDiscountEntityTransfer;
use Generated\Shared\Transfer\SpySalesDiscountEntityTransfer;
use Orm\Zed\Campaign\Persistence\Base\DstCampaignPeriodBranchOrderProduct;
use Orm\Zed\Discount\Persistence\Base\DstCartDiscountGroup;
use Orm\Zed\Discount\Persistence\Map\SpyDiscountTableMap;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Orm\Zed\Sales\Persistence\Base\SpySalesDiscount;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface;
use Pyz\Zed\Discount\DiscountDependencyProvider;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Shared\Discount\DiscountConstants;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Discount\Business\Persistence\DiscountEntityMapper;
use Spryker\Zed\Discount\Dependency\Facade\DiscountToCurrencyInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class DiscountModel implements DiscountModelInterface
{
    use LoggerTrait;

    protected const KEY_ID_DISCOUNT = 'id';
    protected const KEY_AMOUNT = 'amount';

    protected const QUERY_STRING = "sku = '%s' AND branch = '%s'";

    protected const LAST_HOUR = '23';
    protected const LAST_MINUTE = '59';
    protected const LAST_SECOND = '59';

    /**
     * @var DiscountQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var DiscountToCurrencyInterface
     */
    protected $currencyFacade;

    /**
     * @var DiscountToTaxBridgeInterface
     */
    protected $taxFacade;

    /**
     * @var DiscountEntityMapperInterface
     */
    protected $discountEntityMapper;

    /**
     * DiscountModel constructor.
     * @param DiscountQueryContainerInterface $queryContainer
     * @param MerchantFacadeInterface $merchantFacade
     * @param DiscountToCurrencyInterface $currencyFacade
     * @param DiscountToTaxBridgeInterface $taxFacade
     */
    public function __construct(
        DiscountQueryContainerInterface $queryContainer,
        MerchantFacadeInterface         $merchantFacade,
        DiscountToCurrencyInterface     $currencyFacade,
        DiscountToTaxBridgeInterface    $taxFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->merchantFacade = $merchantFacade;
        $this->currencyFacade = $currencyFacade;
        $this->taxFacade = $taxFacade;

        $this->discountEntityMapper = new DiscountEntityMapper(
            $currencyFacade
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param int $idBranch
     * @return SpyDiscountEntityTransfer[]
     * @throws PropelException
     */
    public function getDiscountsByIdBranch(int $idBranch): array
    {
        $discountEntities = $this
            ->queryContainer
            ->getDiscountsByIdBranch($idBranch)
            ->orderByValidFrom(Criteria::DESC)
            ->find();

        $discountTransfers = [];

        foreach ($discountEntities as $discountEntity) {
            $discountTransfers[] = $this
                ->entityToTransfer($discountEntity);
        }

        return $discountTransfers;
    }

    /**
     * @param int $idBranch
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getNonVoucherDiscountsByIdBranch(int $idBranch): array
    {
        $discountEntities = $this
            ->queryContainer
            ->getDiscountsByIdBranch($idBranch)
            ->filterByDiscountType(DiscountConstants::TYPE_VOUCHER, Criteria::NOT_EQUAL)
            ->orderByValidFrom(Criteria::DESC)
            ->find();

        $discountTransfers = [];

        foreach ($discountEntities as $discountEntity) {
            $discountTransfers[] = $this
                ->entityToTransfer($discountEntity);
        }

        return $discountTransfers;
    }

    /**
     * {@inheritdoc}
     *
     * @return SpyDiscountEntityTransfer[]
     * @throws PropelException
     */
    public function getDiscountsForCurrentBranch(): array
    {
        return $this
            ->getDiscountsByIdBranch(
                $this
                    ->merchantFacade
                    ->getCurrentBranch()
                    ->getIdBranch()
            );
    }

    /**
     * @param int $idDiscount
     * @return DiscountConfiguratorTransfer
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getDiscountConfiguratorTransferById(int $idDiscount): DiscountConfiguratorTransfer
    {
        $discountConfiguratorTransfer = new DiscountConfiguratorTransfer();

        $discount = $this
            ->queryContainer
            ->queryDiscount()
            ->filterByIdDiscount($idDiscount)
            ->findOne();

        $discountConfiguratorTransfer
            ->setDiscountGeneral(
                $this
                    ->createDiscountGeneralTransfer($discount)
            );
        $discountConfiguratorTransfer
            ->setDiscountCalculator(
                $this
                    ->createDiscountCalculatorTransfer($discount)
            );
        $discountConfiguratorTransfer
            ->setDiscountCondition(
                $this
                    ->createDiscountConditionTransfer($discount)
            );
        $discountConfiguratorTransfer
            ->setDiscountVoucher(
                $this
                    ->createDiscountVoucherTransfer($discount)
            );

        return $discountConfiguratorTransfer;
    }

    /**
     * {@inheritdoc}
     *
     * @param int[] $branchIds
     * @return BranchDiscountTransfer[]
     *
     * @throws PropelException
     */
    public function getActiveDiscountsByBranches(array $branchIds): array
    {
        $discounts = $this
            ->getActiveDiscountsForBranchesAndSku($branchIds);

        return $this
            ->createBranchDiscountTransfers($discounts);
    }

    /**
     * {@inheritdoc}
     *
     * @throws PropelException
     */
    public function getActiveDiscountsForProduct(int $idBranch, string $sku): array
    {
        $discounts = $this
            ->getActiveDiscountsForBranchesAndSku([$idBranch], $sku);

        return $this
            ->createBranchDiscountTransfers($discounts);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return bool
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function activateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool
    {
        $discount = $this
            ->queryContainer
            ->queryDiscount()
            ->filterByDiscountType(DiscountConstants::TYPE_VOUCHER, Criteria::NOT_EQUAL)
            ->filterByFkBranch($idBranch)
            ->findOneByIdDiscount($idDiscount);

        if ($discount instanceof SpyDiscount) {
            $discount
                ->setIsActive(true)
                ->save();

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @param $discounts
     * @return bool
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function resetDiscountVouchers($discounts): bool
    {
        foreach ($discounts as $discount) {
            $salesDiscount = $this->getSalesDiscountById($discount->getIdSalesDiscount());
            $idVoucherDiscount = $this->getIdVoucherDiscountByName($salesDiscount->getDisplayName());

            if($idVoucherDiscount === null){
                continue;
            }

            $discountCode = $this->findSalesDiscountCode($discount->getIdSalesDiscount());

            $discount = $this
                ->queryContainer
                ->queryDiscount()
                ->filterByDiscountType(DiscountConstants::TYPE_VOUCHER)
                ->findOneByIdDiscount($idVoucherDiscount);

            if ($discount instanceof SpyDiscount) {
                $discountVouchers = $discount
                    ->getVoucherPool()
                    ->getDiscountVouchers();
                foreach ($discountVouchers as $voucher) {
                    if ($voucher->getCode() == $discountCode) {
                        $voucher->setNumberOfUses(
                            $voucher->getNumberOfUses() - 1
                        )->save();
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $discountName
     * @return int|null
     * @throws AmbiguousComparisonException
     */
    public function getIdVoucherDiscountByName(string $discountName): ?int
    {
        $discount = $this
            ->queryContainer
            ->queryDiscount()
            ->filterByDiscountType(DiscountConstants::TYPE_VOUCHER)
            ->findOneByDisplayName($discountName);

        if ($discount == null) {
            return null;
        }

        return $discount->getIdDiscount();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscount
     * @param int $idBranch
     * @return bool
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function deactivateDiscountByIdDiscountForBranchId(int $idDiscount, int $idBranch): bool
    {
        $discount = $this
            ->queryContainer
            ->queryDiscount()
            ->filterByDiscountType(DiscountConstants::TYPE_VOUCHER, Criteria::NOT_EQUAL)
            ->filterByFkBranch($idBranch)
            ->findOneByIdDiscount($idDiscount);

        if ($discount instanceof SpyDiscount) {
            $discount
                ->setIsActive(false)
                ->save();

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesDiscount
     * @return SpySalesDiscountEntityTransfer
     */
    public function getSalesDiscountById(int $idSalesDiscount): SpySalesDiscountEntityTransfer
    {
        $entity = $this
            ->findSalesDiscountById(
                $idSalesDiscount
            );

        return (new SpySalesDiscountEntityTransfer())
            ->fromArray(
                $entity
                    ->toArray(),
                true
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param SpySalesDiscountEntityTransfer $discountEntityTransfer
     * @return void
     * @throws PropelException
     */
    public function updateSalesDiscount(SpySalesDiscountEntityTransfer $discountEntityTransfer): void
    {
        $entity = $this
            ->findSalesDiscountById(
                $discountEntityTransfer
                    ->getIdSalesDiscount()
            );

        $entity
            ->fromArray(
                $discountEntityTransfer
                    ->modifiedToArray()
            );

        $entity
            ->save();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $displayName
     * @param string $discountName
     * @param string $sku
     * @param int $amount
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param bool $isActive
     * @return DiscountConfiguratorTransfer
     * @throws CurrencyNotFoundException
     */
    public function createDiscountConfiguratorTransfer(
        int $idBranch,
        string $displayName,
        string $discountName,
        string $sku,
        int $amount,
        DateTime $validFrom,
        DateTime $validTo,
        bool $isActive = false
    ): DiscountConfiguratorTransfer
    {
        $queryString = $this
            ->getQueryStringForBranchAndSku(
                $idBranch,
                $sku
            );

        $moneyValueCollection = $this
            ->createMoneyValueCollectionFromAmount(
                $amount
            );

        $validTo
            ->setTime(
                static::LAST_HOUR,
                static::LAST_MINUTE,
                static::LAST_SECOND
            );

        $discountConfiguratorTransfer = new DiscountConfiguratorTransfer();

        $general = (new DiscountGeneralTransfer())
            ->setFkBranch(
                $idBranch
            )
            ->setIsExclusive(
                false
            )
            ->setIsActive(
                $isActive
            )
            ->setDiscountType(
                DiscountConstants::TYPE_CART_RULE
            )
            ->setDisplayName(
                $displayName
            )
            ->setValidFrom(
                $validFrom
            )
            ->setValidTo(
                $validTo
            )
            ->setDiscountName(
                $discountName
            )
            ->setDiscountSku(
                $sku
            );

        $calculator = (new DiscountCalculatorTransfer())
            ->setAmount(
                0
            )
            ->setCollectorQueryString(
                $queryString
            )
            ->setDiscountPromotion(
                null
            )
            ->setCollectorStrategyType(
                DiscountConstants::DISCOUNT_COLLECTOR_STRATEGY_QUERY_STRING
            )
            ->setCalculatorPlugin(
                DiscountDependencyProvider::PLUGIN_CALCULATOR_FIXED
            )
            ->setMoneyValueCollection(
                $moneyValueCollection
            );

        $condition = (new DiscountConditionTransfer())
            ->setDecisionRuleQueryString(
                $queryString
            );

        $voucher = new DiscountVoucherTransfer();

        $discountConfiguratorTransfer
            ->setDiscountCalculator(
                $calculator
            )
            ->setDiscountCondition(
                $condition
            )
            ->setDiscountGeneral(
                $general
            )
            ->setDiscountVoucher(
                $voucher
            );

        return $discountConfiguratorTransfer;
    }

    /**
     * @param int $idDiscount
     * @param int $idBranch
     * @return SpyDiscount
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function getSpySalesDiscountByIdAndBranch(
        int $idDiscount,
        int $idBranch
    ): SpyDiscount
    {
        return $this
            ->queryContainer
            ->queryDiscount()
            ->filterByIdDiscount(
                $idDiscount
            )
            ->filterByFkBranch(
                $idBranch
            )
            ->filterByDiscountType(
                \Pyz\Shared\Discount\DiscountConstants::TYPE_CART_RULE
            )
            ->findOneOrCreate();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @param string $sku
     * @return string
     */
    public function getQueryStringForBranchAndSku(
        int $idBranch,
        string $sku
    ): string
    {
        return sprintf(
            static::QUERY_STRING,
            $sku,
            $idBranch
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $amount
     * @return ArrayObject|MoneyValueTransfer[]
     * @throws CurrencyNotFoundException
     */
    public function createMoneyValueCollectionFromAmount(
        int $amount
    ): ArrayObject
    {
        $collection = new ArrayObject();

        $moneyValue = new MoneyValueTransfer();

        $store = $this
            ->currencyFacade
            ->getCurrentStoreWithCurrencies();

        $mainCurrency = $store
            ->getCurrencies()
            ->offsetGet(0);

        $vat = (float)$this
            ->taxFacade
            ->getDefaultTaxRateForDate(
                new DateTime('now')
            );

        $grossAmount = round($amount);
        $netAmount = round(
            ($grossAmount * 100) / (100 + $vat)
            , 0
        );

        $moneyValue
            ->setFkStore(
                $store
                    ->getStore()
                    ->getIdStore()
            )
            ->setFkCurrency(
                $mainCurrency
                    ->getIdCurrency()
            )
            ->setCurrency(
                $mainCurrency
            )
            ->setGrossAmount(
                $grossAmount
            )
            ->setNetAmount(
                $netAmount
            );

        $collection
            ->append(
                $moneyValue
            );

        return $collection;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idDiscountAmount
     * @param int $amount
     * @return ArrayObject
     */
    public function updateMoneyValueCollectionWithAmount(
        int $idDiscountAmount,
        int $amount
    ): ArrayObject
    {
        $collection = new ArrayObject();

        $discountAmount = $this
            ->queryContainer
            ->queryDiscountAmountById(
                $idDiscountAmount
            )
            ->findOne();

        $vat = (float)$this
            ->taxFacade
            ->getDefaultTaxRateForDate(
                new DateTime('now')
            );

        $grossAmount = round($amount);
        $netAmount = round(
            ($grossAmount * 100) / (100 + $vat)
            , 0
        );

        $moneyValue = (new MoneyValueTransfer())
            ->fromArray(
                $discountAmount
                    ->toArray(),
                true
            )
            ->setIdEntity(
                $discountAmount
                    ->getPrimaryKey()
            )
            ->setGrossAmount(
                $grossAmount
            )
            ->setNetAmount(
                $netAmount
            );

        $collection
            ->append(
                $moneyValue
            );

        return $collection;
    }

    /**
     * @param SpyDiscount $discount
     * @return SpyDiscountEntityTransfer
     * @throws PropelException
     */
    protected function entityToTransfer(SpyDiscount $discount): SpyDiscountEntityTransfer
    {
        $discountTransfer = (new SpyDiscountEntityTransfer())
            ->fromArray(
                $discount->toArray(
                    TableMap::TYPE_FIELDNAME,
                    true,
                    [],
                    true
                ),
                true
            );

        $discountAmounts = $discount
            ->getDiscountAmounts();

        foreach ($discountAmounts as $discountAmount) {
            $discountAmountTransfer = (new SpyDiscountAmountEntityTransfer())
                ->fromArray(
                    $discountAmount->toArray(),
                    true
                );

            $discountAmountCurrencyTransfer = (new SpyCurrencyEntityTransfer())
                ->fromArray(
                    $discountAmount
                        ->getCurrency()
                        ->toArray(),
                    true
                );

            $discountAmountTransfer
                ->setCurrency($discountAmountCurrencyTransfer);

            $discountTransfer
                ->addSpyDiscountAmounts($discountAmountTransfer);
        }

        return $discountTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $validFrom
     * @param string $validTo
     * @param int $idBranch
     * @return array
     * @throws AmbiguousComparisonException
     */
    public function getDiscountedSkuForBranchByStartAndEnd(
        string $validFrom,
        string $validTo,
        int $idBranch
    ): array
    {
        $discounts = $this
                ->queryContainer
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
                ->useDstCartDiscountGroupQuery()
                    ->filterByIsDeleted(false)
                ->endUse()
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
     * @param int $idBranch
     * @param string $sku
     * @return string
     */
    public function getProductNameOfDiscountByBranchId(
        int $idBranch,
        string $sku
    ): string
    {
        $product = $this->queryContainer
            ->getDiscountsByIdBranch($idBranch)
            ->addJoin(SpyDiscountTableMap::COL_DISCOUNT_SKU, SpyProductTableMap::COL_SKU, Criteria::LEFT_JOIN)
            ->withColumn(SpyProductTableMap::COL_ATTRIBUTES, 'attributes')
            ->addAnd(SpyProductTableMap::COL_SKU, $sku)
            ->find();

        return $product->getData()[0]->getVirtualColumn('attributes');
    }

    /**
     * @param SpyDiscount $discount
     * @return DiscountGeneralTransfer
     * @throws PropelException
     */
    protected function createDiscountGeneralTransfer(SpyDiscount $discount): DiscountGeneralTransfer
    {
        $transfer = new DiscountGeneralTransfer();

        $transfer
            ->setFkBranch($discount->getFkBranch())
            ->setIsExclusive($discount->getIsExclusive())
            ->setIsActive($discount->getIsActive())
            ->setDiscountType($discount->getDiscountType())
            ->setDisplayName($discount->getDisplayName())
            ->setValidTo($discount->getValidTo())
            ->setValidFrom($discount->getValidFrom())
            ->setDescription($discount->getDescription())
            ->setDiscountName($discount->getDiscountName())
            ->setDiscountSku($discount->getDiscountSku())
            ->setEndDate($discount->getEndDate())
            ->setIdDiscount($discount->getIdDiscount())
            ->setStartDate($discount->getStartDate());

        return $transfer;
    }

    /**
     * @param SpyDiscount $discount
     * @return DiscountCalculatorTransfer
     */
    protected function createDiscountCalculatorTransfer(SpyDiscount $discount): DiscountCalculatorTransfer
    {
        $transfer = new DiscountCalculatorTransfer();

        $strategy = '';
        $promotion = null;
        $moneyCollection = $this
            ->discountEntityMapper
            ->getMoneyValueCollectionForEntity(
                $discount
            );

        $transfer
            ->setCollectorQueryString($discount->getCollectorQueryString())
            ->setCollectorStrategyType($strategy)
            ->setAmount($discount->getAmount())
            ->setCalculatorPlugin($discount->getCalculatorPlugin())
            ->setDiscountPromotion($promotion)
            ->setMoneyValueCollection($moneyCollection);

        return $transfer;
    }

    /**
     * @param SpyDiscount $discount
     * @return DiscountConditionTransfer
     */
    protected function createDiscountConditionTransfer(SpyDiscount $discount): DiscountConditionTransfer
    {
        $transfer = new DiscountConditionTransfer();

        $transfer
            ->setDecisionRuleQueryString($discount->getDecisionRuleQueryString());

        return $transfer;
    }

    /**
     * @param SpyDiscount $discount
     * @return DiscountVoucherTransfer
     */
    protected function createDiscountVoucherTransfer(SpyDiscount $discount): DiscountVoucherTransfer
    {
        $transfer = new DiscountVoucherTransfer();

        return $transfer;
    }

    /**
     * @param array $discounts
     * @return BranchDiscountTransfer[]
     * @throws AmbiguousComparisonException
     */
    protected function createBranchDiscountTransfers(array $discounts): array
    {
        $branchDiscounts = [];

        foreach ($discounts as $branchId => $skuData) {
            foreach ($skuData as $sku => $amounts) {
                $branchDiscountTransfer = (new BranchDiscountTransfer())
                    ->setFkBranch($branchId)
                    ->setDiscountSku($sku);
                $money = 0;
                foreach ($amounts as $amount) {
                    $money += $amount[static::KEY_AMOUNT];

                    $cartDiscountGroup = $this
                        ->findCartDiscountGroupByIdAndBranch(
                            $amount[static::KEY_ID_DISCOUNT],
                            $branchId
                        );
                    if ($cartDiscountGroup !== null) {
                        $branchDiscountTransfer
                            ->setIsCarousel(
                                $cartDiscountGroup
                                    ->getIsCarousel()
                            )
                            ->setCarouselPriority(
                                $cartDiscountGroup
                                    ->getCarouselPriority()
                            )
                            ->setIsExpiredDiscount(
                                $cartDiscountGroup
                                    ->getIsExpiredDiscount()
                            );
                    }

                    $campaignProduct = $this
                        ->findCampaignProductByDiscountAndBranch(
                            $amount[static::KEY_ID_DISCOUNT],
                            $branchId
                        );
                    if ($campaignProduct !== null) {
                        $branchDiscountTransfer
                            ->setIsCarousel(
                                $campaignProduct
                                    ->getIsCarousel()
                            )
                            ->setCarouselPriority(
                                $campaignProduct
                                    ->getCarouselPriority()
                            )
                            ->setIsExpiredDiscount(
                                $campaignProduct
                                    ->getIsExpiredDiscount()
                            );
                    }
                }
                $branchDiscountTransfer
                    ->setDiscountPrice($money);
                $branchDiscounts[] = $branchDiscountTransfer;
            }
        }

        return $branchDiscounts;
    }

    /**
     * @param int[] $branchIds
     * @param string|null $sku
     * @return array
     * @throws PropelException
     */
    protected function getActiveDiscountsForBranchesAndSku(array $branchIds, string $sku = null): array
    {
        $discounts = [];

        foreach ($branchIds as $branchId) {
            $discountEntities = $this
                ->queryContainer
                ->queryActiveAndRunningDiscountsByIdBranchAndSku($branchId, $sku)
                ->find();

            foreach ($discountEntities as $discountEntity) {
                $discountSku = $discountEntity
                    ->getDiscountSku();

                $idBranch = $discountEntity
                    ->getFkBranch();

                if ($discountSku === null || $idBranch === null) {
                    continue;
                }

                $discounts[$idBranch][$discountSku][] = [
                    static::KEY_ID_DISCOUNT => $discountEntity->getIdDiscount(),
                    static::KEY_AMOUNT => $this->getMoneyFromDiscountEntity($discountEntity)
                ];
            }
        }

        return $discounts;
    }

    /**
     * @param SpyDiscount $discount
     * @return int
     * @throws PropelException
     */
    protected function getMoneyFromDiscountEntity(SpyDiscount $discount): int
    {
        $money = 0;

        $discountAmounts = $discount
            ->getDiscountAmounts();

        foreach ($discountAmounts as $discountAmount) {
            if ($discountAmount->getCurrency()->getCode() !== 'EUR') {
                continue;
            }

            $money += $discountAmount->getGrossAmount();
        }

        return $money;
    }

    /**
     * @param int $idSalesDiscount
     * @return SpySalesDiscount
     * @throws AmbiguousComparisonException
     */
    protected function findSalesDiscountById(int $idSalesDiscount): SpySalesDiscount
    {
        return $this
            ->queryContainer
            ->querySalesDiscount()
            ->filterByIdSalesDiscount(
                $idSalesDiscount
            )
            ->findOne();
    }

    /**
     * @param int $idSalesDiscount
     * @return string
     * @throws AmbiguousComparisonException
     */
    public function findSalesDiscountCode(int $idSalesDiscount): string
    {
        $salesDiscountCode =$this
            ->queryContainer
            ->querySalesDiscountCode($idSalesDiscount)
            ->findOne();

        return $salesDiscountCode->getCode();
    }

    /**
     * @param int $idDiscount
     * @param int $idBranch
     * @return DstCartDiscountGroup|null
     * @throws AmbiguousComparisonException
     */
    protected function findCartDiscountGroupByIdAndBranch(
        int $idDiscount,
        int $idBranch
    ): ?DstCartDiscountGroup
    {
        return $this
            ->queryContainer
            ->queryCartDiscountGroup()
            ->filterByFkDiscount(
                $idDiscount
            )
            ->filterByFkBranch(
                $idBranch
            )
            ->findOne();
    }

    /**
     * @param int $idDiscount
     * @param int $idBranch
     * @return DstCampaignPeriodBranchOrderProduct|null
     * @throws AmbiguousComparisonException
     */
    protected function findCampaignProductByDiscountAndBranch(
        int $idDiscount,
        int $idBranch
    ): ?DstCampaignPeriodBranchOrderProduct
    {
        return $this
            ->queryContainer
            ->queryCampaignPeriodBranchOrderProductByBranch(
                $idBranch
            )
            ->filterByFkDiscount(
                $idDiscount
            )
            ->useSpyDiscountQuery()
                ->filterByIsActive(
                    true
                )
            ->endUse()
            ->findOne();
    }
}
