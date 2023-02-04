<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 05.02.18
 * Time: 16:39
 */

namespace Pyz\Zed\DataImport\Business\Model\Price;


use Orm\Zed\MerchantPrice\Persistence\MerchantPrice;
use Orm\Zed\MerchantPrice\Persistence\MerchantPriceQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class PriceStep implements DataImportStepInterface
{
    public const COL_FK_PRODUCT = 'fk_product';
    public const COL_FK_BRANCH = 'fk_branch';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $priceEntity = MerchantPriceQuery::create()
            ->filterByFkBranch($dataSet[self::COL_FK_BRANCH])
            ->filterByFkProduct($dataSet[self::COL_FK_PRODUCT])
            ->findOneOrCreate();

        $priceEntity->fromArray($dataSet->getArrayCopy());
        $this->setGrossPrice($priceEntity);
        $priceEntity->setIsActive(true);

        if($priceEntity->isNew() || $priceEntity->isModified()){
            $priceEntity->save();
        }
    }

    /**
     * @param MerchantPrice $entity
     */
    protected function setGrossPrice(MerchantPrice $entity)
    {
        if($entity->getPrice() !== null){
            $entity->setGrossPrice($entity->getPrice() * 1.19);
        }
    }
}