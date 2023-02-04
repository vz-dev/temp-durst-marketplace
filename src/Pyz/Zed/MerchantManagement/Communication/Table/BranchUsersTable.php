<?php
/**
 * Durst - project - BranchUsersTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.02.20
 * Time: 15:21
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Orm\Zed\Merchant\Persistence\DstBranchUser;
use Orm\Zed\Merchant\Persistence\Map\DstBranchUserTableMap;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\Controller\BranchUserController;
use Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class BranchUsersTable extends AbstractTable
{
    protected const ACTION = 'Action';
    protected const MERCHANT = 'Merchant';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var \Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface
     */
    protected $utilDateTimeService;

    /**
     * BranchUsersTable constructor.
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     * @param \Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface $utilDateTimeService
     */
    public function __construct(
        MerchantQueryContainerInterface $merchantQueryContainer,
        UtilDateTimeServiceInterface    $utilDateTimeService)
    {
        $this->merchantQueryContainer = $merchantQueryContainer;
        $this->utilDateTimeService = $utilDateTimeService;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config
            ->setHeader([
                DstBranchUserTableMap::COL_FIRST_NAME => 'Vorname',
                DstBranchUserTableMap::COL_LAST_NAME => 'Nachname',
                DstBranchUserTableMap::COL_EMAIL => 'Email',
                static::MERCHANT => static::MERCHANT,
                DstBranchUserTableMap::COL_FK_BRANCH => 'Branch',
                DstBranchUserTableMap::COL_STATUS => 'Status',
                DstBranchUserTableMap::COL_FK_ACL_GROUP => 'ACL',
                DstBranchUserTableMap::COL_LAST_LOGIN => 'Last Login',
                static::ACTION => static::ACTION
            ]);

        $config
            ->setRawColumns([
                DstBranchUserTableMap::COL_STATUS,
                static::ACTION
            ]);

        $config
            ->setSortable([
                DstBranchUserTableMap::COL_FIRST_NAME,
                DstBranchUserTableMap::COL_LAST_NAME,
                DstBranchUserTableMap::COL_EMAIL,
                DstBranchUserTableMap::COL_STATUS,
                DstBranchUserTableMap::COL_FK_BRANCH
            ]);

        $config
            ->setSearchable([
                DstBranchUserTableMap::COL_FK_BRANCH,
                DstBranchUserTableMap::COL_EMAIL
            ]);

        return $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function prepareData(TableConfiguration $config)
    {
        $branchUserQuery = $this
            ->merchantQueryContainer
            ->queryBranchUser()
            ->joinWithSpyBranch()
            ->joinWithSpyAclGroup();

        $queryResults = $this
            ->runQuery(
                $branchUserQuery,
                $config,
                true
            );

        $results = [];

        /* @var $queryResult DstBranchUser */
        foreach ($queryResults as $queryResult) {
            $results[] = [
                DstBranchUserTableMap::COL_FIRST_NAME => $queryResult->getFirstName(),
                DstBranchUserTableMap::COL_LAST_NAME => $queryResult->getLastName(),
                DstBranchUserTableMap::COL_EMAIL => $queryResult->getEmail(),
                static::MERCHANT => $queryResult->getSpyBranch()->getSpyMerchant()->getCompany(),
                DstBranchUserTableMap::COL_FK_BRANCH => $queryResult->getSpyBranch()->getName(),
                DstBranchUserTableMap::COL_STATUS => $this->createStatusLabel($queryResult),
                DstBranchUserTableMap::COL_FK_ACL_GROUP => $queryResult->getSpyAclGroup()->getName(),
                DstBranchUserTableMap::COL_LAST_LOGIN => $this->utilDateTimeService->formatDateTime($queryResult->getLastLogin()),
                static::ACTION => implode(' ', $this->createActionButtons($queryResult))
            ];
        }

        return $results;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createStatusLabel(DstBranchUser $branchUser): string
    {
        $statusLabel = '';

        switch ($branchUser->getStatus()) {
            case DstBranchUserTableMap::COL_STATUS_ACTIVE:
                $statusLabel = '<span class="label label-success" title="Active">Active</span>';
                break;
            case DstBranchUserTableMap::COL_STATUS_BLOCKED:
                $statusLabel = '<span class="label label-danger" title="Deactivated">Deactivated</span>';
                break;
            case DstBranchUserTableMap::COL_STATUS_DELETED:
                $statusLabel = '<span class="label label-default" title="Deleted">Deleted</span>';
                break;
        }

        return $statusLabel;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\DstBranchUser $branchUser
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createActionButtons(DstBranchUser $branchUser): array
    {
        $urls = [];

        $urls[] = $this
            ->generateEditButton(
                Url::generate(
                    BranchUserController::UPDATE_BRANCH_USER_URL,
                    [
                        BranchUserController::PARAM_ID_BRANCH_USER => $branchUser->getIdBranchUser()
                    ]
                ),
                'Edit'
            );

        if ($branchUser->getStatus() === DstBranchUserTableMap::COL_STATUS_ACTIVE) {
            $urls[] = $this
                ->generateRemoveButton(
                    Url::generate(
                        BranchUserController::DELETE_BRANCH_USER_URL,
                        [
                            BranchUserController::PARAM_ID_BRANCH_USER => $branchUser->getIdBranchUser()
                        ]
                    ),
                    'Delete'
                );
        }

        if (
            $branchUser->getStatus() === DstBranchUserTableMap::COL_STATUS_DELETED ||
            $branchUser->getStatus() === DstBranchUserTableMap::COL_STATUS_BLOCKED
        ) {
            $urls[] = $this
                ->generateViewButton(
                    Url::generate(
                        BranchUserController::RESTORE_BRANCH_USER_URL,
                        [
                            BranchUserController::PARAM_ID_BRANCH_USER => $branchUser->getIdBranchUser()
                        ]
                    ),
                    'Restore'
                );
        }

        return $urls;
    }
}
