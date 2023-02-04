<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 15.05.19
 * Time: 10:20
 */

namespace Pyz\Zed\Product\Business\Manager;


use ArrayObject;
use Generated\Shared\Transfer\GtinTransfer;
use Generated\Shared\Transfer\SkuTransfer;
use Orm\Zed\Product\Persistence\SpyProduct;
use Pyz\Zed\Product\Persistence\ProductQueryContainer;

class GtinManager
{

    const KEY_ATTR_NAME = 'name';
    const KEY_ATTR_GTIN = 'gtin';

    const GTIN_DELIMITER = ',';

    /**
     * @var ProductQueryContainer
     */
    protected $productQueryContainer;

    public function __construct($productQueryContainer)
    {
        $this->productQueryContainer =$productQueryContainer;
    }

    /**
     * @return GtinTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getGtins() : array
    {
        $productEntities = $this
            ->productQueryContainer
            ->queryProduct()
            ->useSpyProductLocalizedAttributesQuery('localizedAttributes')
                ->filterByAttributes_Like('%gtin%')
            ->endUse()
            ->addAsColumn('localized_attributes', 'localizedAttributes.attributes')
            ->filterByIsActive(true)
            ->find();

        $gtinDataArray = [];

        foreach ($productEntities as $productEntity) {

            $skuTransfer = $this->productEntityToSkuTransfer($productEntity);

            $gtins = $skuTransfer->getGtins();

            foreach ($gtins as $gtin) {
                $gtinDataArray[] = array(
                    "gtin" => $gtin,
                    "name" => $skuTransfer->getProductName(),
                    "sku" => $skuTransfer->getSku(),
                    "fkDeposit" => $skuTransfer->getFkDeposit(),
                );
            }
        }

        $gtinArray = [];
        $skuArray = [];
        $fkDepositArray = [];

        foreach ($gtinDataArray as $key => $row) {
            $gtinArray[$key] = $row['gtin'];
            $skuArray[$key] = $row['sku'];
            $fkDepositArray[$key] = $row['fkDeposit'];
        }

        array_multisort(
            $gtinArray, SORT_ASC, SORT_STRING, $gtinDataArray
        );

        $gtinTransfers = [];
        $length = count($gtinDataArray);
        $gtinTransfer = new GtinTransfer();

        foreach ($gtinDataArray as $index => $row) {

            if ($row['gtin'] === null){
                continue;
            }
            if ($row['gtin'] == ''){
                continue;
            }

            if ($index == 0) {
                $currentGtin = $row['gtin'];
                $gtinTransfer->setGtin($currentGtin);
                $gtinTransfer->setProductName($row['name']);
                $skuTransfers = [];
            }

            if ($row['gtin'] !== $currentGtin) {
                $gtinTransfer->setSkus(new ArrayObject($skuTransfers));
                $gtinTransfers[] = $gtinTransfer;
                $currentGtin = $row['gtin'];
                $gtinTransfer = new GtinTransfer();
                $gtinTransfer->setGtin($currentGtin);
                $gtinTransfer->setProductName($row['name']);
                $skuTransfers = [];
            }

            $skuTransfer = new SkuTransfer();
            $skuTransfer->setSku($row['sku']);
            $skuTransfer->setFkDeposit($row['fkDeposit']);
            $skuTransfers[] = $skuTransfer;

            if ($index == $length - 1) {
                $gtinTransfer->setSkus(new ArrayObject($skuTransfers));
                $gtinTransfers[] = $gtinTransfer;
            }
        }

        return $gtinTransfers;
    }

    /**
     * @param SpyProduct $productEntity
     * @return SkuTransfer
     */
    protected function productEntityToSkuTransfer(SpyProduct $productEntity) : SkuTransfer
    {
        $skuTransfer = new SkuTransfer();
        $skuTransfer->setSku($productEntity->getSku());
        $skuTransfer->setFkDeposit($productEntity->getFkDeposit());
        $productAttributes = json_decode($productEntity->getAttributes());
        $localizedAttributes = json_decode($productEntity->getVirtualColumn('localized_attributes'));
        $skuTransfer->setProductName($productAttributes->{self::KEY_ATTR_NAME});

        $gtins = [];

        if (isset($localizedAttributes->{self::KEY_ATTR_GTIN})) {
            $gtinString = $localizedAttributes->{self::KEY_ATTR_GTIN};
            $gtins = explode(self::GTIN_DELIMITER, $gtinString);
        }

        $skuTransfer->setGtins($gtins);
        return $skuTransfer;
    }

}
