<?php

namespace Pyz\Zed\MarketingManagement\Communication\Table;

use Orm\Zed\Discount\Persistence\Map\SpyDiscountAmountTableMap;
use Orm\Zed\Discount\Persistence\Map\SpyDiscountTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Propel\Runtime\Propel;
use Pyz\Zed\Discount\Persistence\DiscountQueryContainer;
use Pyz\Zed\MarketingManagement\MarketingManagementConfig;
use Spryker\Service\UtilText\Model\Url\Url;
use \Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;


class VoucherTable extends AbstractTable
{
    public const COL_BRANCH_NAME = 'branch_name';
    public const COL_MERCHANT_NAME = 'merchant_name';
    public const COL_DESCRIPTION = 'description';
    public const COL_DISCOUNT_NAME = 'discount_name';
    public const COL_DISPLAY_NAME = 'display_name';
    public const COL_DISCOUNT_AMOUNT = 'discount_amount';
    public const COL_STATUS = 'status';
    public const COL_VALID_FROM = 'valid_from';
    public const COL_VALID_TO = 'valid_to';
    public const COL_VOUCHERS_AMOUNT = 'vouchers_amount';
    public const COL_VOUCHERS_REDEEMED = 'vouchers_redeemed';
    public const COL_ACTIONS = 'actions';
    public const COL_SUB_TOTAL_CONDITION = 'decision_rule_query_string';

    public const URL_DISCOUNT_ID = 'id-discount';
    public const URL_DISCOUNT_REDIRECT_URL = '/discount/index/list';
    public const URL_DISCOUNT_VISIBILITY = 'visibility';

    const BUTTON_ACTIVATE = 'Activate';
    const BUTTON_DEACTIVATE = 'Deactivate';

    public const HEADER_LABEL_MERCHANT = 'Merchant name';
    public const HEADER_LABEL_BRANCH = 'Branch name';
    public const HEADER_LABEL_DISCOUNT_NAME = 'Discount name';
    public const HEADER_LABEL_DISCOUNT_AMOUNT = 'Gross (Wert in Euro)';
    public const HEADER_LABEL_START_DATE = 'Aktion Startdatum';
    public const HEADER_LABEL_END_DATE = 'Aktion Enddatum';
    public const HEADER_LABEL_STATUS = 'Status';
    public const HEADER_LABEL_DISPLAY_NAME = 'Name';
    public const HEADER_LABEL_VOUCHERS_AMOUNT = 'Anzahl Vouchers gesamt';
    public const HEADER_LABEL_VOUCHERS_REDEEMED = 'Anzahl eingelöste Vouchers';
    public const HEADER_LABEL_DESCRIPTION = 'Description';
    public const HEADER_LABEL_ACTIONS = 'Aktion';
    public const HEADER_LABEL_SUBTOTAL_CONDITION = 'Mindestbestellwert';

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
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config->setHeader([
            self::COL_MERCHANT_NAME => self::HEADER_LABEL_MERCHANT,
            self::COL_BRANCH_NAME => self::HEADER_LABEL_BRANCH,
            SpyDiscountTableMap::COL_VALID_FROM => self::HEADER_LABEL_START_DATE,
            SpyDiscountTableMap::COL_VALID_TO => self::HEADER_LABEL_END_DATE,
            self::COL_DISPLAY_NAME => self::HEADER_LABEL_DISPLAY_NAME,
            self::COL_DISCOUNT_NAME => self::HEADER_LABEL_DISCOUNT_NAME,
            self::COL_DESCRIPTION => self::HEADER_LABEL_DESCRIPTION,
            self::COL_VOUCHERS_AMOUNT => self::HEADER_LABEL_VOUCHERS_AMOUNT,
            self::COL_VOUCHERS_REDEEMED => self::HEADER_LABEL_VOUCHERS_REDEEMED,
            self::COL_DISCOUNT_AMOUNT => self::HEADER_LABEL_DISCOUNT_AMOUNT,
            self::COL_SUB_TOTAL_CONDITION => self::HEADER_LABEL_SUBTOTAL_CONDITION,
            self::COL_ACTIONS => self::HEADER_LABEL_ACTIONS,
        ]);

        $config->addRawColumn(self::COL_ACTIONS);

        $config->setSortable([
            SpyDiscountTableMap::COL_VALID_FROM,
            SpyDiscountTableMap::COL_VALID_TO,
            self::COL_BRANCH_NAME,
        ]);

        $config->setSearchable([
            SpyBranchTableMap::COL_NAME,
            SpyMerchantTableMap::COL_COMPANY,
        ]);

        $config->setDefaultSortField(SpyDiscountTableMap::COL_VALID_FROM, 'DESC');

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $con = Propel::getConnection(SpyBranchTableMap::DATABASE_NAME);
        $sql = 'SELECT vouchers_amount, vouchers_redeemed, spy_discount.*, sb.name as branch_name, sm.company as merchant_name, (SELECT ' . SpyDiscountAmountTableMap::COL_GROSS_AMOUNT . ' as discount_amount FROM ' . SpyDiscountAmountTableMap::TABLE_NAME .
            ' WHERE ' . SpyDiscountAmountTableMap::COL_FK_DISCOUNT . ' = ' . SpyDiscountTableMap::COL_ID_DISCOUNT . ')
               FROM spy_discount
                JOIN (
                    SELECT
                      CASE when max_number_of_uses = 0 then 0 else SUM(max_number_of_uses) end AS vouchers_amount,
                      SUM(number_of_uses) as vouchers_redeemed, sd.id_discount
                    FROM spy_discount_voucher
                    JOIN spy_discount_voucher_pool sdv
                        ON sdv.id_discount_voucher_pool = spy_discount_voucher.fk_discount_voucher_pool
                    JOIN spy_discount sd
                        ON sdv.id_discount_voucher_pool = sd.fk_discount_voucher_pool
                    WHERE spy_discount_voucher.fk_discount_voucher_pool = sdv.id_discount_voucher_pool
                    AND sdv.id_discount_voucher_pool = sd.fk_discount_voucher_pool
                    GROUP BY sd.id_discount, max_number_of_uses
                ) AS b
            ON b.id_discount = spy_discount.id_discount
                JOIN spy_branch sb on spy_discount.fk_branch = sb.id_branch
                JOIN spy_merchant sm on sb.fk_merchant = sm.id_merchant
                WHERE spy_discount.discount_type = :p1;';

        $stmt = $con->prepare($sql);
        $voucher = 'voucher';
        $stmt->bindParam(':p1', $voucher);
        $results = [];
        if ($stmt->execute()) {
            $queryResults = $stmt->fetchAll();
            /** @var array $discountEntity */
            foreach ($queryResults as $discountEntity) {
                $results[] = [
                    self::COL_MERCHANT_NAME => $discountEntity[self::COL_MERCHANT_NAME],
                    self::COL_BRANCH_NAME => $discountEntity[self::COL_BRANCH_NAME],
                    SpyDiscountTableMap::COL_VALID_FROM => $discountEntity[self::COL_VALID_FROM],
                    SpyDiscountTableMap::COL_VALID_TO => $discountEntity[self::COL_VALID_TO],
                    self::COL_DISPLAY_NAME => $discountEntity[self::COL_DISPLAY_NAME],
                    self::COL_DISCOUNT_NAME => $discountEntity[self::COL_DISCOUNT_NAME],
                    self::COL_DESCRIPTION => $discountEntity[self::COL_DISCOUNT_NAME],
                    self::COL_VOUCHERS_AMOUNT => $discountEntity[self::COL_VOUCHERS_AMOUNT],
                    self::COL_VOUCHERS_REDEEMED => $discountEntity[self::COL_VOUCHERS_REDEEMED],
                    self::COL_DISCOUNT_AMOUNT => $discountEntity[self::COL_DISCOUNT_AMOUNT],
                    self::COL_SUB_TOTAL_CONDITION => strstr($discountEntity[self::COL_SUB_TOTAL_CONDITION], 'sub-total'),
                    self::COL_ACTIONS => $this->getActionButtons($discountEntity),
                ];
            }
        }

        return $results;
    }

    /**
     * @param array $discountEntity
     *
     * @return string
     */
    protected function getActionButtons(array $discountEntity)
    {
        $buttons = [];
        $buttons[] = $this->createEditButton($discountEntity);
        $buttons[] = $this->createViewButton($discountEntity);
        $buttons[] = $this->createAddVoucherCodeButton($discountEntity);
        $buttons[] = $this->createToggleDiscountVisibilityButton($discountEntity);

        return implode(' ', $buttons);
    }

    /**
     * @param array $discountEntity
     *
     * @return string
     */
    protected function createEditButton(array  $discountEntity)
    {
        $editDiscountUrl = Url::generate(
            '/discount/index/edit',
            [
                static::URL_DISCOUNT_ID => $discountEntity['id_discount'],
            ]
        );

        return $this->generateEditButton($editDiscountUrl, 'Edit');
    }

    /**
     * @param array $discountEntity
     *
     * @return string
     */
    protected function createViewButton(array $discountEntity)
    {
        $viewDiscountUrl = Url::generate(
            '/discount/index/view',
            [
                static::URL_DISCOUNT_ID => $discountEntity['id_discount'],
            ]
        );

        return $this->generateViewButton($viewDiscountUrl, 'View');
    }

    /**
     * @param array $discountEntity
     *
     * @return string
     */
    protected function createAddVoucherCodeButton(array $discountEntity)
    {
        if (!$discountEntity['fk_discount_voucher_pool']) {
            return '';
        }

        $addVoucherCodeDiscountUrl = Url::generate(
            '/discount/index/edit',
            [
                static::URL_DISCOUNT_ID => $discountEntity['id_discount'],
            ]
        );

        return $this->generateCreateButton($addVoucherCodeDiscountUrl, 'Add code');
    }

    /**
     * @param array $discountEntity
     *
     * @return string
     */
    protected function createToggleDiscountVisibilityButton(array $discountEntity)
    {
        $visibility = static::BUTTON_ACTIVATE;
        if ($discountEntity['is_active']) {
            $visibility = static::BUTTON_DEACTIVATE;
        }

        $viewDiscountUrl = Url::generate(
            '/discount/index/toggle-discount-visibility',
            [
                static::URL_DISCOUNT_ID => $discountEntity['id_discount'],
                static::URL_DISCOUNT_VISIBILITY => $visibility,
                static::URL_DISCOUNT_REDIRECT_URL => '/discount/index/list',
            ]
        );

        return $this->generateStatusButton($viewDiscountUrl, $visibility);
    }

    /**
     * @param \Spryker\Service\UtilText\Model\Url\Url $viewDiscountUrl
     * @param string $visibility
     *
     * @return string
     */
    protected function generateStatusButton(Url $viewDiscountUrl, string $visibility)
    {
        if ($visibility === static::BUTTON_ACTIVATE) {
            return $this->generateViewButton($viewDiscountUrl, $visibility);
        }

        return $this->generateRemoveButton($viewDiscountUrl, $visibility);
    }
}
