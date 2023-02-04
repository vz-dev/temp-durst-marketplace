<?php
/**
 * Durst - project - CampaignPeriodBranchOrderProductPriceHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 23.06.21
 * Time: 12:53
 */

namespace Pyz\Zed\Campaign\Business\Hydrator\CampaignPeriodBranchOrderProduct;

use Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer;
use Generated\Shared\Transfer\CatalogProductTransfer;
use Generated\Shared\Transfer\CatalogUnitTransfer;
use Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface;
use Pyz\Zed\Campaign\CampaignConfig;
use Pyz\Zed\Deposit\Business\DepositFacadeInterface;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class CampaignPeriodBranchOrderProductProductInformationHydrator implements CampaignPeriodBranchOrderProductHydratorInterface
{
    /**
     * @var \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface
     */
    protected $merchantPriceFacade;

    /**
     * @var \Pyz\Zed\Discount\Business\DiscountFacadeInterface
     */
    protected $discountFacade;

    /**
     * @var \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface
     */
    protected $imageUtil;

    /**
     * @var \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var \Pyz\Zed\Campaign\CampaignConfig
     */
    protected $config;

    /**
     * @var \Pyz\Zed\Deposit\Business\DepositFacadeInterface
     */
    protected $depositFacade;

    /**
     * @var \Spryker\Zed\Discount\Communication\Plugin\Calculator\FixedPlugin
     */
    protected $calculatorPlugin;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var CatalogProductTransfer
     */
    protected $catalogProduct;

    /**
     * @var \Generated\Shared\Transfer\DepositTransfer
     */
    protected $depositTransfer;

    /**
     * CampaignPeriodBranchOrderProductPriceHydrator constructor.
     * @param \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface $merchantPriceFacade
     * @param \Pyz\Zed\Discount\Business\DiscountFacadeInterface $discountFacade
     * @param \Pyz\Zed\Campaign\Business\Utility\ImageUtilInterface $imageUtil
     * @param \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacade
     * @param \Spryker\Zed\Currency\Business\CurrencyFacadeInterface $currencyFacade
     * @param \Pyz\Zed\Campaign\CampaignConfig $config
     * @param \Pyz\Zed\Deposit\Business\DepositFacadeInterface $depositFacade
     */
    public function __construct(
        MerchantPriceFacadeInterface $merchantPriceFacade,
        DiscountFacadeInterface $discountFacade,
        ImageUtilInterface $imageUtil,
        MoneyFacadeInterface $moneyFacade,
        CurrencyFacadeInterface $currencyFacade,
        CampaignConfig $config,
        DepositFacadeInterface $depositFacade
    )
    {
        $this->merchantPriceFacade = $merchantPriceFacade;
        $this->discountFacade = $discountFacade;
        $this->imageUtil = $imageUtil;
        $this->moneyFacade = $moneyFacade;
        $this->currencyFacade = $currencyFacade;
        $this->config = $config;
        $this->depositFacade = $depositFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    public function hydrateCampaignPeriodBranchOrderProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        $this
            ->setCatalogProduct(
                $campaignPeriodBranchOrderProductTransfer
            );

        $this
            ->setDepositTransfer(
                $campaignPeriodBranchOrderProductTransfer
            );

        $discountPrice = $this
            ->getDiscountPrice(
                $campaignPeriodBranchOrderProductTransfer
            );

        $discountPriceValue = $this
            ->getDiscountPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        $merchantPrice = $this
            ->getMerchantPrice(
                $campaignPeriodBranchOrderProductTransfer
            );

        $merchantPriceValue = $this
            ->getMerchantPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        $status = $this->getStatus(
            $campaignPeriodBranchOrderProductTransfer
        );

        $endPrice = $this
            ->getEndPrice(
                $campaignPeriodBranchOrderProductTransfer
            );

        $endPriceValue = $this
            ->getEndPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        $bigImage = $this
            ->getBigImage(
                $campaignPeriodBranchOrderProductTransfer
            );

        $thumbImage = $this
            ->getThumbImage(
                $campaignPeriodBranchOrderProductTransfer
            );

        $deeplinkUrl = $this
            ->getDeepLinkUrl(
                $campaignPeriodBranchOrderProductTransfer
            );

        $pricePerLiter = $this
            ->getPricePerLiter(
                $campaignPeriodBranchOrderProductTransfer
            );

        $refund = $this
            ->getRefund(
                $campaignPeriodBranchOrderProductTransfer
            );

        $abstractSku = $campaignPeriodBranchOrderProductTransfer
            ->getProductConcrete()
            ->getAbstractSku();

        $campaignPeriodBranchOrderProductTransfer
            ->setProductPrice(
                $merchantPrice
            )
            ->setAbstractSku(
                $abstractSku
            )
            ->setProductPriceValue(
                $merchantPriceValue
            )
            ->setDiscountPrice(
                $discountPrice
            )
            ->setDiscountPriceValue(
                $discountPriceValue
            )
            ->setEndPrice(
                $endPrice
            )
            ->setEndPriceValue(
                $endPriceValue
            )
            ->setBigProductImage(
                $bigImage
            )
            ->setThumbProductImage(
                $thumbImage
            )
            ->setDeeplinkUrl(
                $deeplinkUrl
            )
            ->setPriceLiter(
                $pricePerLiter
            )
            ->setRefund(
                $refund
            )
            ->setIdDiscount(
                $campaignPeriodBranchOrderProductTransfer->getFkDiscount()
            )->setStatus(
                $status
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string|null
     */
    protected function getDiscountPrice(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?string
    {
        $discountPrice = $this
            ->getDiscountPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        if ($discountPrice !== null) {
            $discount = $campaignPeriodBranchOrderProductTransfer
                ->getDiscount();

            $calculator = $discount
                ->getDiscountCalculator()
                ->getCalculatorPlugin();

            $calculatorPlugins = $this
                ->discountFacade
                ->getCalculatorPlugins();

            $this->calculatorPlugin = $calculatorPlugins[$calculator];

            foreach ($discount->getDiscountCalculator()->getMoneyValueCollection() as $money) {
                $this->currencyCode = $money
                    ->getCurrency()
                    ->getCode();

                $discountPrice = $this
                    ->calculatorPlugin
                    ->getFormattedAmount(
                        $discountPrice,
                        $this
                            ->currencyCode
                    );
            }
        }

        return $discountPrice;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string|null
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function getMerchantPrice(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?string
    {
        $merchantPrice = $this
            ->getMerchantPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        if ($merchantPrice !== null) {
            $catalogUnit = $this
                ->getCatalogUnit(
                    $campaignPeriodBranchOrderProductTransfer
                );

            foreach ($catalogUnit->getPrices() as $price) {
                $merchantPrice = $this
                    ->formatPrice(
                        $merchantPrice
                    );
            }
        }

        return $merchantPrice;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string|null
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function getEndPrice(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?string
    {
        $endPrice = $this
            ->getEndPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        if ($endPrice !== null) {
            return $this
                ->formatPrice(
                    $endPrice
                );
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string
     */
    protected function getBigImage(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): string
    {
        return $this
            ->imageUtil
            ->formatBig(
                $this
                    ->getUnitImage(
                        $campaignPeriodBranchOrderProductTransfer
                    )
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string
     */
    protected function getThumbImage(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): string
    {
        return $this
            ->imageUtil
            ->formatThumb(
                $this
                    ->getUnitImage(
                        $campaignPeriodBranchOrderProductTransfer
                    )
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string|null
     */
    protected function getUnitImage(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?string
    {
        $catalogUnit = $this
            ->getCatalogUnit(
                $campaignPeriodBranchOrderProductTransfer
            );

        if ($catalogUnit === null) {
            return null;
        }

        return $catalogUnit
            ->getUrlUnitImageBottle();
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return \Generated\Shared\Transfer\CatalogUnitTransfer|null
     */
    protected function getCatalogUnit(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?CatalogUnitTransfer
    {
        foreach ($this->catalogProduct->getUnits() as $unit) {
            if ($unit->getSku() !== $campaignPeriodBranchOrderProductTransfer->getSku()) {
                continue;
            }

            return $unit;
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     */
    protected function setCatalogProduct(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        $this->catalogProduct = $this
            ->merchantPriceFacade
            ->getCatalogProductForBranchBySku(
                $campaignPeriodBranchOrderProductTransfer
                    ->getFkBranch(),
                $campaignPeriodBranchOrderProductTransfer
                    ->getProductConcrete()
                    ->getAbstractSku(),
                $campaignPeriodBranchOrderProductTransfer
                    ->getProductConcrete()
                    ->getSku(),
                true,
                true
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return void
     */
    protected function setDepositTransfer(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): void
    {
        $this->depositTransfer = $this
            ->depositFacade
            ->getDepositById(
                $campaignPeriodBranchOrderProductTransfer
                    ->getProductConcrete()
                    ->getFkDeposit()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return int|null
     */
    protected function getDiscountPriceValue(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?int
    {
        $discountPrice = null;

        if ($campaignPeriodBranchOrderProductTransfer->getDiscount() !== null) {
            $discount = $campaignPeriodBranchOrderProductTransfer
                ->getDiscount();

            foreach ($discount->getDiscountCalculator()->getMoneyValueCollection() as $money) {
                if ($money->getGrossAmount() !== null) {
                    $discountPrice = $money
                        ->getGrossAmount();
                }
            }
        }

        return $discountPrice;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return int|null
     */
    protected function getMerchantPriceValue(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?int
    {
        $merchantPrice = null;

        $catalogUnit = $this
            ->getCatalogUnit(
                $campaignPeriodBranchOrderProductTransfer
            );

        if ($catalogUnit !== null) {
            foreach ($catalogUnit->getPrices() as $price) {
                $merchantPrice = $price
                    ->getPrice();
            }
        }

        return $merchantPrice;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string|null
     */
    protected function getStatus(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?string
    {
        $status = null;

        $catalogUnit = $this
            ->getCatalogUnit(
                $campaignPeriodBranchOrderProductTransfer
            );

        if ($catalogUnit !== null) {
            foreach ($catalogUnit->getPrices() as $price) {
                $status = $price
                    ->getStatus();
            }
        }

        return $status;
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return int|null
     */
    protected function getEndPriceValue(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): ?int
    {
        $merchantPrice = $this
            ->getMerchantPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );
        $discountPrice = $this
            ->getDiscountPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        return ($merchantPrice - $discountPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string
     */
    protected function getDeepLinkUrl(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): string
    {
        $branchCode = $campaignPeriodBranchOrderProductTransfer
            ->getBranch()
            ->getCode();
        $abstractSku = $campaignPeriodBranchOrderProductTransfer
            ->getProductConcrete()
            ->getAbstractSku();

        return $this
            ->config
            ->getDeepLinkUrl(
                $branchCode,
                $abstractSku
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function getPricePerLiter(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): string
    {
        $endPrice = $this
            ->getEndPriceValue(
                $campaignPeriodBranchOrderProductTransfer
            );

        $unitVolume = $this
            ->depositTransfer
            ->getVolumePerBottle();

        $bottles = $this
            ->depositTransfer
            ->getBottles();

        if (
            $endPrice === null ||
            $unitVolume === null ||
            $bottles === null
        ) {
            return '-';
        }

        $pricePerLiter = (int)round($endPrice * 1000 / ($unitVolume * $bottles));

        return $this
            ->formatPrice(
                $pricePerLiter
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
     * @return string
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function getRefund(
        CampaignPeriodBranchOrderProductTransfer $campaignPeriodBranchOrderProductTransfer
    ): string
    {
        return $this
            ->formatPrice(
                $this
                    ->depositTransfer
                    ->getDeposit()
            );
    }

    /**
     * @param int $price
     * @return string
     * @throws \Spryker\Zed\Currency\Business\Model\Exception\CurrencyNotFoundException
     */
    protected function formatPrice(
        int $price
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
                $price,
                $mainCurrency
                    ->getCode()
            );

        return $this
            ->moneyFacade
            ->formatWithSymbol(
                $moneyTransfer
            );
    }
}
