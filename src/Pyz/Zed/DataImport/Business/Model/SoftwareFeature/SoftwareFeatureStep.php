<?php
/**
 * Durst - project - SoftwareFeatureStep.phpp.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 24.10.18
 * Time: 14:35
 */

namespace Pyz\Zed\DataImport\Business\Model\SoftwareFeature;


use Orm\Zed\Sales\Persistence\DstSoftwareFeatureQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class SoftwareFeatureStep implements DataImportStepInterface
{
    public const COL_NAME = 'name';
    public const COL_CODE = 'code';
    public const COL_DESCRIPTION = 'description';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {

        $softwareFeatureEntity = DstSoftwareFeatureQuery::create()
            ->filterByCode($dataSet[self::COL_CODE])
            ->findOneOrCreate();

        $softwareFeatureEntity->fromArray($dataSet->getArrayCopy());

        if($softwareFeatureEntity->isModified() || $softwareFeatureEntity->isNew()){
            $softwareFeatureEntity->save();
        }
    }
}