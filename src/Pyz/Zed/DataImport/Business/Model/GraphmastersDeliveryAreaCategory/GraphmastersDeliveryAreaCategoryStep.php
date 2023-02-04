<?php

namespace Pyz\Zed\DataImport\Business\Model\GraphmastersDeliveryAreaCategory;

use Orm\Zed\DeliveryArea\Persistence\SpyDeliveryAreaQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategory;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryQuery;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersDeliveryAreaCategoryToDeliveryArea;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class GraphmastersDeliveryAreaCategoryStep implements DataImportStepInterface
{
    protected const COL_FK_BRANCH = 'fk_branch';
    protected const COL_CATEGORY_NAME = 'category_name';
    protected const COL_FK_DELIVERY_AREAS = 'fk_delivery_areas';

    /**
     * @param DataSetInterface $dataSet
     *
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $deliveryAreaCategoryEntity = DstGraphmastersDeliveryAreaCategoryQuery::create()
            ->filterByFkBranch($dataSet[self::COL_FK_BRANCH])
            ->filterByCategoryName($dataSet[self::COL_CATEGORY_NAME])
            ->findOneOrCreate();

        $deliveryAreaCategoryEntity->fromArray($dataSet->getArrayCopy());
        $deliveryAreaCategoryEntity->setIsActive(true);

        if ($deliveryAreaCategoryEntity->isNew()) {
            $deliveryAreaCategoryEntity->setIdDeliveryAreaCategory(null);
        }

        if ($deliveryAreaCategoryEntity->isNew() || $deliveryAreaCategoryEntity->isModified()) {
            $deliveryAreaCategoryEntity->save();
        }

        $this->removeExistingCrossReferences($deliveryAreaCategoryEntity);
        $this->addNewCrossReferences($deliveryAreaCategoryEntity, $dataSet);
    }

    /**
     * @param DstGraphmastersDeliveryAreaCategory $deliveryAreaCategoryEntity
     *
     * @throws PropelException
     */
    protected function removeExistingCrossReferences(
        DstGraphmastersDeliveryAreaCategory $deliveryAreaCategoryEntity
    ): void {
        foreach ($deliveryAreaCategoryEntity->getDstGraphmastersDeliveryAreaCategoryToDeliveryAreas() as $existingCrossReference) {
            $existingCrossReference->delete();
        }
    }

    /**
     * @param DstGraphmastersDeliveryAreaCategory $deliveryAreaCategoryEntity
     * @param DataSetInterface $dataSet
     *
     * @throws PropelException
     */
    protected function addNewCrossReferences(
        DstGraphmastersDeliveryAreaCategory $deliveryAreaCategoryEntity,
        DataSetInterface $dataSet
    ): void {
        $fkDeliveryAreas = explode(',', $dataSet[self::COL_FK_DELIVERY_AREAS]);

        $newDeliveryAreas = SpyDeliveryAreaQuery::create()
            ->filterByIdDeliveryArea_In($fkDeliveryAreas);

        foreach ($newDeliveryAreas as $newDeliveryArea) {
            $newCrossReference = new DstGraphmastersDeliveryAreaCategoryToDeliveryArea();
            $newCrossReference->setFkDeliveryAreaCategory($deliveryAreaCategoryEntity->getIdDeliveryAreaCategory());
            $newCrossReference->setFkDeliveryArea($newDeliveryArea->getIdDeliveryArea());
            $newCrossReference->save();
        }
    }
}
