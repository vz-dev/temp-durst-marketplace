<?php
/**
 * Durst - project - DeliveryAreaCategoryTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.06.21
 * Time: 21:21
 */

namespace Pyz\Zed\GraphMasters\Communication\Table;


use Orm\Zed\GraphMasters\Persistence\Map\DstGraphmastersDeliveryAreaCategoryTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Communication\Controller\CategoryController;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class DeliveryAreaCategoryTable extends AbstractTable
{
    protected const COL_ZIPCODES = 'zipcodes';

    protected const HEADER_ID = '#';
    protected const HEADER_ACTIVE = 'aktiv?';
    protected const HEADER_BRANCH = 'Branch';
    protected const HEADER_CATEGORY_NAME = 'Kategoriename';
    protected const HEADER_SLOT_SIZE = 'Zeitfenster Größe(h)';
    protected const HEADER_MIN_VALUE = 'MBW';
    protected const HEADER_ACTION = 'Action';
    protected const HEADER_ZIP_CODE = 'PLZ';

    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $facade;

    /**
     * SettingsTable constructor.
     * @param GraphMastersQueryContainerInterface $queryContainer
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        GraphMastersFacadeInterface $facade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_ID_DELIVERY_AREA_CATEGORY => static::HEADER_ID,
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_FK_BRANCH => static::HEADER_BRANCH,
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_CATEGORY_NAME => static::HEADER_CATEGORY_NAME,
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_SLOT_SIZE => static::HEADER_SLOT_SIZE,
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_MIN_VALUE => static::HEADER_MIN_VALUE,
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_IS_ACTIVE => static::HEADER_ACTIVE,
            static::COL_ZIPCODES => static::COL_ZIPCODES,
            static::HEADER_ACTION => static::HEADER_ACTION,
        ]);

        $config->setRawColumns([
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_IS_ACTIVE,
            static::COL_ZIPCODES,
            static::HEADER_ACTION,
        ]);

        $config->setSearchable([
        ]);

        $config->setSortable([
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_ID_DELIVERY_AREA_CATEGORY,
            DstGraphmastersDeliveryAreaCategoryTableMap::COL_FK_BRANCH,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->queryGraphmastersDeliveryAreaCategory()
            ->useSpyBranchQuery()
            ->endUse()
            ->withColumn(SpyBranchTableMap::COL_NAME, static::HEADER_BRANCH);


        $categories = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($categories as $category) {



            $results[] = [
                DstGraphmastersDeliveryAreaCategoryTableMap::COL_ID_DELIVERY_AREA_CATEGORY => $category->getIdDeliveryAreaCategory(),
                DstGraphmastersDeliveryAreaCategoryTableMap::COL_FK_BRANCH => $category->getVirtualColumn(self::HEADER_BRANCH),
                DstGraphmastersDeliveryAreaCategoryTableMap::COL_CATEGORY_NAME => $category->getCategoryName(),
                DstGraphmastersDeliveryAreaCategoryTableMap::COL_SLOT_SIZE => $category->getSlotSize(),
                DstGraphmastersDeliveryAreaCategoryTableMap::COL_MIN_VALUE => $category->getMinValue(),
                DstGraphmastersDeliveryAreaCategoryTableMap::COL_IS_ACTIVE => $this->formatBool($category->getIsActive()),
                static::COL_ZIPCODES => $this->createDeliveryAreasOutput($category->getIdDeliveryAreaCategory()),
                static::HEADER_ACTION => $this->formatActionButtons($category->getIdDeliveryAreaCategory()),
            ];
        }

        return $results;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function formatBool($value): string
    {
        if ($value) {
            return '<i style="color: #108548" class="fa fa-check"></i>';
        }

        return '<i style="color: #ed5565" class="fa fa-times red"></i>';
    }

    /**
     * @param int $idCategory
     *
     * @return string
     */
    protected function formatActionButtons(int $idCategory): string
    {
        $buttons = [];
        $buttons[] = $this
            ->generateEditButton(
                sprintf(
                    '%s?%s=%d',
                    CategoryController::URL_EDIT,
                    CategoryController::PARAM_ID_CATEGORY,
                    $idCategory
                ),
                'Edit'
            );

        $buttons[] = $this
            ->generateRemoveButton(
                sprintf(
                    '%s?%s=%d',
                    CategoryController::URL_REMOVE,
                    CategoryController::PARAM_ID_CATEGORY,
                    $idCategory
                ),
                'Delete'
            );

        return implode('', $buttons);
    }

    /**
     * @param int $idCat
     * @return string
     */
    protected function createDeliveryAreasOutput(int $idCat) : string
    {
        $html = '';

        $deliveryAreas =  $this
            ->facade
            ->getCategoryDeliveryAreasByCategoryId($idCat);

        foreach($deliveryAreas as $deliveryArea)
        {
            $html .= sprintf('<span class="btn btn-s btn-default">%s</span>', $deliveryArea->getZipCode());
        }

        return $html;
    }
}
