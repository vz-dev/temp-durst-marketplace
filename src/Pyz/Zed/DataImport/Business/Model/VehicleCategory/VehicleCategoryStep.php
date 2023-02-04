<?php
/**
 * Durst - project - VehicleCategory.php.
 *
 * Initial version by:
 * User: Zhaklina Basha, <zhaklina.basha@durst.shop>
 * Date: 2021-08-17
 * Time: 13:40
 */

namespace Pyz\Zed\DataImport\Business\Model\VehicleCategory;


use Orm\Zed\Tour\Persistence\DstVehicleCategoryQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class VehicleCategoryStep implements DataImportStepInterface
{
    public const COL_NAME = 'name';
    public const COL_PROFILE = 'profile';
    public const COL_SPEED_FACTOR = 'speed_factor';
    public const COL_STATUS = 'status';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $vehicleCategoryEntity = DstVehicleCategoryQuery::create()
            ->filterByName($dataSet[static::COL_NAME])
            ->findOneOrCreate();

        $vehicleCategoryEntity->fromArray($dataSet->getArrayCopy());

        if($vehicleCategoryEntity->isModified() || $vehicleCategoryEntity->isNew()){
            $vehicleCategoryEntity->save();
        }
    }
}
