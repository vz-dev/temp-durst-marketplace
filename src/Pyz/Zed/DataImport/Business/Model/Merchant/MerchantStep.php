<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 15:31
 */

namespace Pyz\Zed\DataImport\Business\Model\Merchant;


use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Orm\Zed\Sales\Persistence\DstSoftwarePackageQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class MerchantStep implements DataImportStepInterface
{
    public const COL_MERCHANT_NAME = 'merchantname';
    public const COL_SOFTWARE_PACKAGE = 'software_package';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $merchantEntity = SpyMerchantQuery::create()
            ->filterByMerchantname($dataSet[self::COL_MERCHANT_NAME])
            ->findOneOrCreate();

        $softwarePackageEntity = DstSoftwarePackageQuery::create()
            ->findOneByCode($dataSet[static::COL_SOFTWARE_PACKAGE]);

        $merchantEntity->fromArray($dataSet->getArrayCopy());
        $merchantEntity->setDstSoftwarePackage($softwarePackageEntity);


        if($merchantEntity->isNew() || $merchantEntity->isModified()){
            $merchantEntity->save();
        }
    }
}