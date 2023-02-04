<?php
/**
 * Durst - project - DiscountTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-12
 * Time: 21:26
 */

namespace Pyz\Zed\MarketingManagement\Communication\Table;

use Orm\Zed\Deposit\Persistence\Map\SpyDepositTableMap;
use Orm\Zed\Discount\Persistence\Map\SpyDiscountAmountTableMap;
use Orm\Zed\Discount\Persistence\Map\SpyDiscountTableMap;
use Orm\Zed\Discount\Persistence\SpyDiscount;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Orm\Zed\MerchantPrice\Persistence\Map\MerchantPriceTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainer;
use Pyz\Zed\MarketingManagement\MarketingManagementConfig;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class DiscountTable extends AbstractTable
{
    public const IMAGE_RESIZE_PATH = 'resized/1200';
    public const PRODUCT_IMAGE_TYPES = [
        'product_logo',
        'bottleshot_big',
        'picture_detail_1',
        'caseshot_product_unit',
    ];
    public const VIEW_BTN_DEFAULTOPTIONS = ['class' => 'btn-view', 'icon' => 'fa-eye'];
    public const VIEW_BTN_CUSTOMOPTIONS = ['target' => '_blank'];

    public const ACTIVE_ADVERTISE_LINK_STRING = '<span style="color: %s"><i class="fa %s fa-lg"></i></span>';
    public const ACTIVE_ADVERTISE_LINK_STRING_FORMAT = '%s / %s';

    public const COL_BRANCH_NAME = 'branch_name';
    public const COL_MERCHANT_NAME = 'merchant_name';
    public const COL_PRODUCT_ATTRS = 'product_attrs';
    public const COL_DEPOSIT_AMOUNT = 'deposit_amount';
    public const COL_DEPOSIT_VOLUME_PER_BOTTLE = 'deposit_volume_per_bottle';
    public const COL_DEPOSIT_BOTTLE_VOL = 'deposit_bottles_per_case';
    public const COL_DISCOUNT_AMOUNT = 'discount_amount';
    public const COL_MERCHANT_PRICE = 'merchant_price';
    public const COL_START_DATE = 'start_date';
    public const COL_END_DATE = 'end_date';
    public const COL_PRODUCT_NAME = 'product_name';
    public const COL_UNIT = 'unit';
    public const COL_PRICE = 'price';
    public const COL_DISCOUNT_PRICE = 'discount_price';
    public const COL_DISCOUNT_PRICE_PER_LITER = 'discount_price_per_liter';
    public const COL_DEPOSIT_COST = 'deposit_cost';
    public const COL_MEDIA_LINKS = 'media_links';
    public const COL_ADVERTISE_DISCOUNT = 'advertise_discount';

    public const HEADER_LABEL_MERCHANT = 'Händler (Merchant)';
    public const HEADER_LABEL_BRANCH = 'Niederlassung (Branch)';
    public const HEADER_LABEL_START_DATE = 'Aktion Startdatum';
    public const HEADER_LABEL_END_DATE = 'Aktion Enddatum';
    public const HEADER_LABEL_PRODUCT_NAME = 'Produkt';
    public const HEADER_LABEL_UNIT_NAME = 'Gebinde';
    public const HEADER_LABEL_DISCOUNTED_PRICE = 'Aktionspreis';
    public const HEADER_LABEL_ORIGINAL_PRICESTREICHPREIS = 'Streichpreis';
    public const HEADER_LABEL_PRICE_PER_LITER = '€ / je Liter';
    public const HEADER_LABEL_DEPOSIT_AMOUNT = 'Pfand';
    public const HEADER_LABEL_MEDIA_LINKS = 'Media-Links';
    public const HEADER_LABEL_ACTIVE_ADVERTISE_STATUS = 'Aktiv / Soll Aktion beworden werden?';

    /**
     * @var \Pyz\Zed\Discount\Persistence\DiscountQueryContainer
     */
    protected $discountQueryContainer;

    /**
     * @var \Pyz\Zed\MarketingManagement\MarketingManagementConfig
     */
    protected $marketingManagementConfig;

    /**
     * @var \Spryker\Zed\Money\Business\MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * DiscountTable constructor.
     *
     * @param \Pyz\Zed\Discount\Persistence\DiscountQueryContainer $discountQueryContainer
     * @param \Pyz\Zed\MarketingManagement\MarketingManagementConfig $marketingManagementConfig
     * @param \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacade
     */
    public function __construct(
        DiscountQueryContainer $discountQueryContainer,
        MarketingManagementConfig $marketingManagementConfig,
        MoneyFacadeInterface $moneyFacade
    ) {
        $this->discountQueryContainer = $discountQueryContainer;
        $this->marketingManagementConfig = $marketingManagementConfig;
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            self::COL_MERCHANT_NAME => self::HEADER_LABEL_MERCHANT,
            self::COL_BRANCH_NAME => self::HEADER_LABEL_BRANCH,
            SpyDiscountTableMap::COL_VALID_FROM => self::HEADER_LABEL_START_DATE,
            SpyDiscountTableMap::COL_VALID_TO => self::HEADER_LABEL_END_DATE,
            self::COL_PRODUCT_NAME => self::HEADER_LABEL_PRODUCT_NAME,
            self::COL_UNIT => self::HEADER_LABEL_UNIT_NAME,
            self::COL_DISCOUNT_PRICE => self::HEADER_LABEL_DISCOUNTED_PRICE,
            self::COL_PRICE => self::HEADER_LABEL_ORIGINAL_PRICESTREICHPREIS,
            self::COL_DISCOUNT_PRICE_PER_LITER => self::HEADER_LABEL_PRICE_PER_LITER,
            self::COL_DEPOSIT_COST => self::HEADER_LABEL_DEPOSIT_AMOUNT,
            self::COL_MEDIA_LINKS => self::HEADER_LABEL_MEDIA_LINKS,
            self::COL_ADVERTISE_DISCOUNT => self::HEADER_LABEL_ACTIVE_ADVERTISE_STATUS,
        ]);

        $config->setRawColumns([
            self::COL_MEDIA_LINKS,
            self::COL_ADVERTISE_DISCOUNT,
        ]);

        $config->setSortable([
            SpyDiscountTableMap::COL_VALID_FROM,
            SpyDiscountTableMap::COL_VALID_TO,
            self::COL_BRANCH_NAME,
        ]);

        $config->setDefaultSortField(SpyDiscountTableMap::COL_VALID_FROM, TableConfiguration::SORT_DESC);

        $config->setSearchable([
            SpyBranchTableMap::COL_NAME,
            SpyMerchantTableMap::COL_COMPANY,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $merchantPriceQuery = '(SELECT ' . MerchantPriceTableMap::COL_GROSS_PRICE . ' FROM ' . MerchantPriceTableMap::TABLE_NAME .
            ' WHERE ' . MerchantPriceTableMap::COL_FK_BRANCH . ' = ' . SpyBranchTableMap::COL_ID_BRANCH .
            ' AND ' . MerchantPriceTableMap::COL_FK_PRODUCT . ' = ' . SpyProductTableMap::COL_ID_PRODUCT . ')';

        $discountAmountQuery = '(SELECT ' . SpyDiscountAmountTableMap::COL_GROSS_AMOUNT . ' FROM ' . SpyDiscountAmountTableMap::TABLE_NAME .
            ' WHERE ' . SpyDiscountAmountTableMap::COL_FK_DISCOUNT . ' = ' . SpyDiscountTableMap::COL_ID_DISCOUNT . ')';

        $discountQuery = $this
            ->discountQueryContainer
            ->queryDiscount()
            ->addJoin(SpyDiscountTableMap::COL_FK_BRANCH, SpyBranchTableMap::COL_ID_BRANCH)
            ->withColumn(SpyBranchTableMap::COL_NAME, self::COL_BRANCH_NAME)
            ->addJoin(SpyBranchTableMap::COL_FK_MERCHANT, SpyMerchantTableMap::COL_ID_MERCHANT)
            ->withColumn(SpyMerchantTableMap::COL_COMPANY, self::COL_MERCHANT_NAME)
            ->addJoin(SpyDiscountTableMap::COL_DISCOUNT_SKU, SpyProductTableMap::COL_SKU, Criteria::LEFT_JOIN)
            ->withColumn(SpyProductTableMap::COL_ATTRIBUTES, self::COL_PRODUCT_ATTRS)
            ->addJoin(SpyProductTableMap::COL_FK_DEPOSIT, SpyDepositTableMap::COL_ID_DEPOSIT, Criteria::LEFT_JOIN)
            ->withColumn(SpyDepositTableMap::COL_DEPOSIT, self::COL_DEPOSIT_AMOUNT)
            ->withColumn(SpyDepositTableMap::COL_VOLUME_PER_BOTTLE, self::COL_DEPOSIT_VOLUME_PER_BOTTLE)
            ->withColumn(SpyDepositTableMap::COL_BOTTLES, self::COL_DEPOSIT_BOTTLE_VOL)
            ->withColumn($discountAmountQuery, self::COL_DISCOUNT_AMOUNT)
            ->withColumn($merchantPriceQuery, self::COL_MERCHANT_PRICE)
            ->filterByFkBranch(null, Criteria::ISNOTNULL);

        $queryResults = $this
            ->runQuery($discountQuery, $config, true);

        $results = [];

        /** @var SpyDiscount $discountEntity */
        foreach ($queryResults as $discountEntity) {
            $productAttrs = $discountEntity->getVirtualColumn(self::COL_PRODUCT_ATTRS);
            $depositAmount = $discountEntity->getVirtualColumn(self::COL_DEPOSIT_AMOUNT);
            $depositVolumePerBottle = $discountEntity->getVirtualColumn(self::COL_DEPOSIT_VOLUME_PER_BOTTLE);
            $depositBottleVol = $discountEntity->getVirtualColumn(self::COL_DEPOSIT_BOTTLE_VOL);
            $discountAmount = $discountEntity->getVirtualColumn(self::COL_DISCOUNT_AMOUNT);
            $merchantPrice = $discountEntity->getVirtualColumn(self::COL_MERCHANT_PRICE);

            $results[] = [
                self::COL_MERCHANT_NAME => $discountEntity->getVirtualColumn(self::COL_MERCHANT_NAME),
                self::COL_BRANCH_NAME => $discountEntity->getSpyBranch()->getName(),
                SpyDiscountTableMap::COL_VALID_FROM => $discountEntity->getValidFrom()->format('d.m.Y'),
                SpyDiscountTableMap::COL_VALID_TO => $discountEntity->getValidTo()->format('d.m.Y'),
                self::COL_PRODUCT_NAME => ($productAttrs !== null) ? $this->getPropertyFromAttrs($productAttrs, 'name') : '-',
                self::COL_UNIT => ($productAttrs !== null) ? $this->getPropertyFromAttrs($productAttrs, self::COL_UNIT) : '-',
                self::COL_PRICE => ($merchantPrice !== null) ? $this->formatPrice($merchantPrice) : '-',
                self::COL_DISCOUNT_PRICE => ($merchantPrice !== null && $discountAmount !== null) ? $this->formatPrice($this->getDiscountPrice($merchantPrice, $discountAmount)) : '-',
                self::COL_DISCOUNT_PRICE_PER_LITER => ($merchantPrice !== null && $discountAmount !== null && $depositVolumePerBottle !== null && $depositBottleVol !== null) ? $this->formatPrice($this->getPricePerLiter($this->getDiscountPrice($merchantPrice, $discountAmount), $depositVolumePerBottle, $depositBottleVol)) : '-',
                self::COL_DEPOSIT_COST => ($depositAmount !== null) ? $this->formatPrice($depositAmount) : '-',
                self::COL_MEDIA_LINKS => ($productAttrs !== null) ? $this->createProductImageLinks($productAttrs) : '-',
                self::COL_ADVERTISE_DISCOUNT => $this->showActiveLinks($discountEntity->isActive(), null),
            ];
        }

        return $results;
    }

    /**
     * @param string $productAttrs
     * @param string $property
     *
     * @return string|null
     */
    protected function getPropertyFromAttrs(string $productAttrs, string $property) : ?string
    {
        $attrs = json_decode($productAttrs, true);
        if (array_key_exists($property, $attrs)) {
            return $attrs[$property];
        }

        return null;
    }

    /**
     * @param int $originalPrice
     * @param int $discountAmount
     *
     * @return int
     */
    protected function getDiscountPrice(int $originalPrice, int $discountAmount) : int
    {
        return $originalPrice - $discountAmount;
    }

    /**
     * @param int $discountprice
     * @param int $unitVolume
     * @param int $bottles
     *
     * @return int
     */
    protected function getPricePerLiter(int $discountprice, int $unitVolume, int $bottles) : int
    {
        return round($discountprice * 1000 / ($unitVolume * $bottles));
    }

    /**
     * @param string $productAttrs
     *
     * @return string
     */
    protected function createProductImageLinks(string $productAttrs) : string
    {
        $productImageLinks = '';

        foreach (self::PRODUCT_IMAGE_TYPES as $image_type) {
            $image = $this->getPropertyFromAttrs($productAttrs, $image_type);

            if ($image !== null) {
                $productImageLinks .= $this->generateButton($this->createImageUrlWithMediaHost($image), $image_type, self::VIEW_BTN_DEFAULTOPTIONS, self::VIEW_BTN_CUSTOMOPTIONS);
            }
        }

        return $productImageLinks;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    protected function createImageUrlWithMediaHost(string $image) : string
    {
        return sprintf(
            '%s/%s/%s',
            $this->marketingManagementConfig->getMediaHostUrl(),
            self::IMAGE_RESIZE_PATH,
            $image
        );
    }

    /**
     * @param bool|null $discountActive
     * @param bool|null $advertise
     *
     * @return string
     */
    protected function showActiveLinks(?bool $discountActive, ?bool $advertise) : string
    {
        $activeLink = $this->getLinkByStatus($discountActive);
        $advertiseLink = $this->getLinkByStatus($advertise);

        return sprintf(
            self::ACTIVE_ADVERTISE_LINK_STRING_FORMAT,
            $activeLink,
            $advertiseLink
        );
    }

    /**
     * @param bool|null $status
     *
     * @return string
     */
    protected function getLinkByStatus(?bool $status) : string
    {
        if ($status === null) {
            return '-';
        }

        $statusClass = 'fa-times-circle';
        $color = 'red';

        if ($status === true) {
            $color = 'green';
            $statusClass = 'fa-check-circle';
        }

        return sprintf(
            self::ACTIVE_ADVERTISE_LINK_STRING,
            $color,
            $statusClass
        );
    }

    /**
     * @param int $value
     * @param bool $includeSymbol
     * @param null|string $currencyIsoCode
     *
     * @return string
     */
    protected function formatPrice(int $value, $includeSymbol = true, $currencyIsoCode = null) : string
    {
        $moneyTransfer = $this->moneyFacade->fromInteger($value, $currencyIsoCode);
        if ($includeSymbol) {
            return $this->moneyFacade->formatWithSymbol($moneyTransfer);
        }

        return $this->moneyFacade->formatWithoutSymbol($moneyTransfer);
    }
}
