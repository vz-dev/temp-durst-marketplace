<?php
/**
 * Durst - project - MerchantUsersTable.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.04.21
 * Time: 14:06
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;

use Orm\Zed\Merchant\Persistence\Base\DstMerchantUser;
use Orm\Zed\Merchant\Persistence\Map\DstMerchantUserTableMap;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\Controller\MerchantUserController;
use Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class MerchantUsersTable extends AbstractTable
{
    protected const ACTION = 'Action';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $merchantQueryContainer;

    /**
     * @var \Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface
     */
    protected $utilDateTimeService;

    /**
     * MerchantUsersTable constructor.
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $merchantQueryContainer
     * @param \Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface $utilDateTimeService
     */
    public function __construct(
        MerchantQueryContainerInterface $merchantQueryContainer,
        UtilDateTimeServiceInterface    $utilDateTimeService
    )
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
            ->setHeader(
                [
                    DstMerchantUserTableMap::COL_FIRST_NAME => 'Vorname',
                    DstMerchantUserTableMap::COL_LAST_NAME => 'Nachname',
                    DstMerchantUserTableMap::COL_EMAIL => 'Email',
                    DstMerchantUserTableMap::COL_FK_MERCHANT => 'Händler',
                    DstMerchantUserTableMap::COL_STATUS => 'Status',
                    DstMerchantUserTableMap::COL_FK_ACL_GROUP => 'ACL',
                    DstMerchantUserTableMap::COL_LAST_LOGIN => 'Last Login',
                    static::ACTION => static::ACTION
                ]
            );

        $config
            ->setRawColumns(
                [
                    DstMerchantUserTableMap::COL_STATUS,
                    static::ACTION
                ]
            );

        $config
            ->setSortable(
                [
                    DstMerchantUserTableMap::COL_FIRST_NAME,
                    DstMerchantUserTableMap::COL_LAST_NAME,
                    DstMerchantUserTableMap::COL_EMAIL,
                    DstMerchantUserTableMap::COL_STATUS,
                    DstMerchantUserTableMap::COL_FK_MERCHANT
                ]
            );

        $config
            ->setSearchable(
                [
                    DstMerchantUserTableMap::COL_FK_MERCHANT,
                    DstMerchantUserTableMap::COL_EMAIL
                ]
            );

        return $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $merchantUserQuery = $this
            ->merchantQueryContainer
            ->queryMerchantUser()
            ->joinWithSpyMerchant()
            ->joinWithSpyAclGroup();

        $queryResults = $this
            ->runQuery(
                $merchantUserQuery,
                $config,
                true
            );

        $results = [];

        /* @var $queryResult DstMerchantUser */
        foreach ($queryResults as $queryResult) {
            $results[] = [
                DstMerchantUserTableMap::COL_FIRST_NAME => $queryResult->getFirstName(),
                DstMerchantUserTableMap::COL_LAST_NAME => $queryResult->getLastName(),
                DstMerchantUserTableMap::COL_EMAIL => $queryResult->getEmail(),
                DstMerchantUserTableMap::COL_FK_MERCHANT => $queryResult->getSpyMerchant()->getCompany(),
                DstMerchantUserTableMap::COL_STATUS => $this->createStatusLabel($queryResult),
                DstMerchantUserTableMap::COL_FK_ACL_GROUP => $queryResult->getSpyAclGroup()->getName(),
                DstMerchantUserTableMap::COL_LAST_LOGIN => $this->utilDateTimeService->formatDateTime($queryResult->getLastLogin()),
                static::ACTION => implode(
                    ' ',
                    $this->createActionButtons($queryResult)
                )
            ];
        }

        return $results;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\Base\DstMerchantUser $merchantUser
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createStatusLabel(DstMerchantUser $merchantUser): string
    {
        $statusLabel = '';

        switch ($merchantUser->getStatus()) {
            case DstMerchantUserTableMap::COL_STATUS_ACTIVE:
                $statusLabel = '<span class="label label-success" title="Active">Active</span>';
                break;
            case DstMerchantUserTableMap::COL_STATUS_BLOCKED:
                $statusLabel = '<span class="label label-danger" title="Deactivated">Deactivated</span>';
                break;
            case DstMerchantUserTableMap::COL_STATUS_DELETED:
                $statusLabel = '<span class="label label-default" title="Deleted">Deleted</span>';
                break;
        }

        return $statusLabel;
    }

    /**
     * @param \Orm\Zed\Merchant\Persistence\Base\DstMerchantUser $merchantUser
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function createActionButtons(DstMerchantUser $merchantUser): array
    {
        $urls = [];

        $urls[] = $this
            ->generateEditButton(
                Url::generate(
                    MerchantUserController::UPDATE_MERCHANT_USER_URL,
                    [
                        MerchantUserController::PARAM_ID_MERCHANT_USER => $merchantUser->getIdMerchantUser()
                    ]
                ),
                'Edit'
            );

        if ($merchantUser->getStatus() === DstMerchantUserTableMap::COL_STATUS_ACTIVE) {
            $urls[] = $this
                ->generateRemoveButton(
                    Url::generate(
                        MerchantUserController::DELETE_MERCHANT_USER_URL,
                        [
                            MerchantUserController::PARAM_ID_MERCHANT_USER => $merchantUser->getIdMerchantUser()
                        ]
                    ),
                    'Delete'
                );
        }

        if (
            $merchantUser->getStatus() === DstMerchantUserTableMap::COL_STATUS_DELETED ||
            $merchantUser->getStatus() === DstMerchantUserTableMap::COL_STATUS_BLOCKED
        ) {
            $urls[] = $this
                ->generateViewButton(
                    Url::generate(
                        MerchantUserController::RESTORE_MERCHANT_USER_URL,
                        [
                            MerchantUserController::PARAM_ID_MERCHANT_USER => $merchantUser->getIdMerchantUser()
                        ]
                    ),
                    'Restore'
                );
        }

        return $urls;
    }
}
