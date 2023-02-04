<?php
/**
 * Durst - project - SoftwarePackageTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:47
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Table;

use Orm\Zed\Sales\Persistence\DstSoftwarePackage;
use Orm\Zed\Sales\Persistence\Map\DstSoftwarePackageTableMap;
use Pyz\Zed\SoftwarePackage\Communication\Controller\EditController;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SoftwarePackageTable extends AbstractTable
{
    public const ACTION = 'Action';

    public const HEADER_NAME = 'Name';
    public const HEADER_STATUS = 'Status';
    public const HEADER_QUOTA_ORDER = 'Bestellungen';
    public const HEADER_QUOTA_BRANCH = 'Filialen';
    public const HEADER_QUOTA_DELIVER_AREA = 'Liefergebiete';
    public const HEADER_QUOTA_PRODUCT_CONCRETE = 'Produkte';

    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * SoftwarePackageTable constructor.
     * @param SoftwarePackageQueryContainerInterface $queryContainer
     */
    public function __construct(SoftwarePackageQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstSoftwarePackageTableMap::COL_NAME => static::HEADER_NAME,
            DstSoftwarePackageTableMap::COL_STATUS => static::HEADER_STATUS,
            DstSoftwarePackageTableMap::COL_QUOTA_ORDER => static::HEADER_QUOTA_ORDER,
            DstSoftwarePackageTableMap::COL_QUOTA_BRANCH => static::HEADER_QUOTA_BRANCH,
            DstSoftwarePackageTableMap::COL_QUOTA_DELIVERY_AREA => static::HEADER_QUOTA_DELIVER_AREA,
            DstSoftwarePackageTableMap::COL_QUOTA_PRODUCT_CONCRETE => static::HEADER_QUOTA_PRODUCT_CONCRETE,
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([
            self::ACTION,
            DstSoftwarePackageTableMap::COL_STATUS,
        ]);

        $config->setSortable([
            DstSoftwarePackageTableMap::COL_NAME,
            DstSoftwarePackageTableMap::COL_STATUS,
            DstSoftwarePackageTableMap::COL_QUOTA_ORDER,
            DstSoftwarePackageTableMap::COL_QUOTA_BRANCH,
            DstSoftwarePackageTableMap::COL_QUOTA_DELIVERY_AREA,
            DstSoftwarePackageTableMap::COL_QUOTA_PRODUCT_CONCRETE,
        ]);

        $config->setSearchable([
            DstSoftwarePackageTableMap::COL_NAME,
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
        $queryResults = $this
            ->getSoftwarePackages($config);

        $results = [];
        foreach ($queryResults as $softwarePackageEntity) {
            $results[] = [
                DstSoftwarePackageTableMap::COL_NAME => $softwarePackageEntity->getName(),
                DstSoftwarePackageTableMap::COL_STATUS => $this->createStatusLabel($softwarePackageEntity),
                DstSoftwarePackageTableMap::COL_QUOTA_ORDER => $softwarePackageEntity->getQuotaOrder(),
                DstSoftwarePackageTableMap::COL_QUOTA_BRANCH => $softwarePackageEntity->getQuotaBranch(),
                DstSoftwarePackageTableMap::COL_QUOTA_DELIVERY_AREA => $softwarePackageEntity->getQuotaDeliveryArea(),
                DstSoftwarePackageTableMap::COL_QUOTA_PRODUCT_CONCRETE => $softwarePackageEntity->getQuotaProductConcrete(),
                self::ACTION => implode(' ', $this->createActionButtons($softwarePackageEntity)),
            ];
        }

        return $results;
    }

    /**
     * @param TableConfiguration $configuration
     * @return DstSoftwarePackage[]
     */
    protected function getSoftwarePackages(TableConfiguration $configuration)
    {
        $query = $this->queryContainer->querySoftwarePackage();
        return $this->runQuery($query, $configuration, true);
    }

    /**
     * @param DstSoftwarePackage $entity
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function createStatusLabel(DstSoftwarePackage $entity)
    {
        $statusLabel = '';
        switch ($entity->getStatus()) {
            case DstSoftwarePackageTableMap::COL_STATUS_ACTIVE:
                $statusLabel = '<span class="label label-success" title="Active">Active</span>';
                break;
            case DstSoftwarePackageTableMap::COL_STATUS_INACTIVE:
                $statusLabel = '<span class="label label-danger" title="Inactive">Inactive</span>';
                break;
            case DstSoftwarePackageTableMap::COL_STATUS_DELETED:
                $statusLabel = '<span class="label label-default" title="Deleted">Deleted</span>';
                break;
        }

        return $statusLabel;
    }

    /**
     * @param DstSoftwarePackage $entity
     *
     * @return array
     */
    public function createActionButtons(DstSoftwarePackage $entity)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(EditController::URL_UPDATE, [
                EditController::PARAM_ID_SOFTWARE_PACKAGE => $entity->getIdSoftwarePackage(),
            ]),
            'Edit'
        );

        $urls[] = $this->generateRemoveButton(
            Url::generate(EditController::URL_DELETE, [
                EditController::PARAM_ID_SOFTWARE_PACKAGE => $entity->getIdSoftwarePackage(),
            ]),
            'Delete'
        );

        $urls[] = $this->generateViewButton(
            Url::generate(EditController::URL_ACTIVATE, [
                EditController::PARAM_ID_SOFTWARE_PACKAGE => $entity->getIdSoftwarePackage(),
            ]),
            'Activate'
        );

        $urls[] = $this->generateViewButton(
            Url::generate(EditController::URL_DEACTIVATE, [
                EditController::PARAM_ID_SOFTWARE_PACKAGE => $entity->getIdSoftwarePackage(),
            ]),
            'Deactivate'
        );

        return $urls;
    }
}