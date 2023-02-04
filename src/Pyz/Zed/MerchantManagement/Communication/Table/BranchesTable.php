<?php

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use DateTime;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Orm\Zed\Sales\Persistence\Map\DstSoftwarePackageTableMap;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;

class BranchesTable extends AbstractTable
{
    public const NAME_SOFTWARE_PACKAGE_DIGITALER_HEIMSERVICE = 'Digitaler Heimservice';

    public const ACTION = 'Action';
    public const UPDATE_BRANCH_URL = '/merchant-management/branch/update';
    public const DELETE_BRANCH_URL = '/merchant-management/branch/delete';
    public const RESTORE_BRANCH_URL = '/merchant-management/branch/restore';
    public const PARAM_ID_BRANCH = 'id-branch';

    public const COL_HASH = 'hash';
    public const COL_UNITS_ORDERED_LICENSED = 'unitsOrderedLicensed';
    public const COL_COUNT_BRANCH_USERS = 'branchUsers';

    public const LABEL_HASH = 'Hash';
    public const LABEL_UNITS_ORDERED_LICENSED = 'Gebinde lizenziert';
    public const LABEL_COUNT_BRANCH_USERS = 'Anzahl Nutzer';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var MoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * BranchesTable constructor.
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     * @param MoneyFacadeInterface $moneyFacade
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(MerchantQueryContainerInterface $merchantQueryContainer, MoneyFacadeInterface $moneyFacade, MerchantFacadeInterface $merchantFacade)
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->moneyFacade = $moneyFacade;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyBranchTableMap::COL_FK_MERCHANT => 'Merchant',
            SpyBranchTableMap::COL_NAME => 'Name',
            DstSoftwarePackageTableMap::COL_NAME => 'Software-Paket',
            SpyBranchTableMap::COL_STATUS => 'Status',
            static::COL_HASH => static::LABEL_HASH,
            static::COL_UNITS_ORDERED_LICENSED => static::LABEL_UNITS_ORDERED_LICENSED,
            static::COL_COUNT_BRANCH_USERS => static::LABEL_COUNT_BRANCH_USERS,
            SpyBranchTableMap::COL_UPDATED_AT => 'geändert am',
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([
            self::ACTION,
            SpyBranchTableMap::COL_STATUS,
            static::COL_UNITS_ORDERED_LICENSED,
        ]);

        $config->setSortable([
            SpyBranchTableMap::COL_FK_MERCHANT,
            SpyBranchTableMap::COL_NAME,
            SpyBranchTableMap::COL_STATUS,
            SpyBranchTableMap::COL_UPDATED_AT,
        ]);

        $config->setSearchable([
            SpyBranchTableMap::COL_FK_MERCHANT,
            SpyBranchTableMap::COL_NAME,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function prepareData(TableConfiguration $config)
    {
        $branchQuery = $this
            ->merchantQueryContainer
            ->queryBranch()
            ->joinDstBranchUser()
            ->groupByIdBranch();

        $queryResults = $this
            ->runQuery($branchQuery, $config, true);

        $results = [];
        /* @var $branchEntity SpyBranch */
        foreach ($queryResults as $branchEntity) {
            $results[] = [
                SpyBranchTableMap::COL_FK_MERCHANT => $branchEntity->getSpyMerchant()->getCompany(),
                SpyBranchTableMap::COL_NAME => $branchEntity->getName(),
                DstSoftwarePackageTableMap::COL_NAME => $branchEntity->getSpyMerchant()->getDstSoftwarePackage()->getName(),
                SpyBranchTableMap::COL_STATUS => $this->createStatusLabel($branchEntity),
                static::COL_HASH => $this->getHashForBranch($branchEntity),
                static::COL_UNITS_ORDERED_LICENSED => $this->getUnitsOrderedLicensedForBranch($branchEntity),
                static::COL_COUNT_BRANCH_USERS => $branchEntity->getDstBranchUsers()->count(),
                SpyBranchTableMap::COL_UPDATED_AT => $branchEntity->getUpdatedAt()->format(DateTime::RFC1036),
                self::ACTION => implode(' ', $this->createActionButtons($branchEntity)),
            ];
        }

        return $results;
    }

    /**
     * @param SpyBranch $branch
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createStatusLabel(SpyBranch $branch)
    {
        $statusLabel = '';
        switch ($branch->getStatus()) {
            case SpyBranchTableMap::COL_STATUS_ACTIVE:
                $statusLabel = '<span class="label label-success" title="Active">Active</span>';
                break;
            case SpyBranchTableMap::COL_STATUS_BLOCKED:
                $statusLabel = '<span class="label label-danger" title="Deactivated">Deactivated</span>';
                break;
            case SpyBranchTableMap::COL_STATUS_DELETED:
                $statusLabel = '<span class="label label-default" title="Deleted">Deleted</span>';
                break;
        }

        return $statusLabel;
    }

    /**
     * @param array $branchEntity
     *
     * @return array
     */
    public function createActionButtons(SpyBranch $branchEntity)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(self::UPDATE_BRANCH_URL, [
                self::PARAM_ID_BRANCH => $branchEntity->getIdBranch(),
            ]),
            'Edit'
        );

        $urls[] = $this->generateRemoveButton(
            Url::generate(self::DELETE_BRANCH_URL, [
            self::PARAM_ID_BRANCH => $branchEntity->getIdBranch(),
            ]),
            'Delete'
        );

        $urls[] = $this->generateViewButton(
            Url::generate(self::RESTORE_BRANCH_URL, [
                self::PARAM_ID_BRANCH => $branchEntity->getIdBranch(),
            ]),
            'Restore'
        );

        return $urls;
    }

    /**
     * @param $price
     * @return string
     */
    protected function formatPrice($price)
    {
        if($price === null)
            return '';
        $moneyTransfer = $this->moneyFacade->fromInteger($price);
        return $this->moneyFacade->formatWithSymbol($moneyTransfer);
    }

    /**
     * @param SpyBranch $branch
     * @return string
     */
    protected function getHashForBranch(SpyBranch $branch) : string
    {
        $branchTransfer =  $this->merchantFacade->getBranchById($branch->getIdBranch());

        return $this->merchantFacade->getHashForBranch($branchTransfer);
    }

    /**
     * @param SpyBranch $branch
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function getUnitsOrderedLicensedForBranch(SpyBranch $branch) : string
    {
        if($branch->getSpyMerchant()->getDstSoftwarePackage()->getName() === static::NAME_SOFTWARE_PACKAGE_DIGITALER_HEIMSERVICE)
        {
            $statColor = 'green';
            if($branch->getUnitsOrderedCount() > $branch->getUnitsLicenseCount())
            {
                $statColor = 'red';
            }

            return sprintf(
                '<span style="color: %s">%d / %d<span>',
                $statColor,
                $branch->getUnitsOrderedCount(),
                $branch->getUnitsLicenseCount()
            );
        }

        return '-';
    }
}
