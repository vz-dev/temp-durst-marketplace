<?php
/**
 * Durst - project - VehicleTypeStep.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2019-01-30
 * Time: 16:13
 */

namespace Pyz\Zed\DataImport\Business\Model\VehicleType;


use Orm\Zed\Tour\Persistence\DstVehicleTypeQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class VehicleTypeStep implements DataImportStepInterface
{
    public const COL_FK_BRANCH = 'fk_branch';
    public const COL_NAME = 'name';
    public const COL_PAYLOAD_KG = 'payload_kg';
    public const COL_STATUS = 'status';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $vehicleTypeEntity = DstVehicleTypeQuery::create()
            ->filterByFkBranch($dataSet[static::COL_FK_BRANCH])
            ->filterByName($dataSet[static::COL_NAME])
            ->findOneOrCreate();

        $vehicleTypeEntity->fromArray($dataSet->getArrayCopy());

        if($vehicleTypeEntity->isModified() || $vehicleTypeEntity->isNew()){
            $vehicleTypeEntity->save();
        }
    }
}