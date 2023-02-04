<?php
/**
 * Durst - project - ProductRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 16:53
 */

namespace Pyz\Zed\Integra\Business\Model\Quote\Product;

use Orm\Zed\MerchantPrice\Persistence\Map\MerchantPriceTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Pyz\Zed\Integra\Business\Exception\EntityNotFoundException;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;

class ProductRepository implements ProductRepositoryInterface
{
    protected const SKU_UNIT_STRING_FORMAT = '%s_%s';
    protected const NON_EXISTENT_INTEGRA_SKU_FORMAT = 'integra_%d_%s';
    protected const MERCHANT_SKU_W_TYPE_FORMAT = '%s_%s';

    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array
     */
    protected $skus = [];

    /**
     * ProductRepository constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     */
    public function __construct(IntegraQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param string $merchantSku
     * @param string $unitType
     * @param int $idBranch
     *
     * @return string
     */
    public function getSkuForMerchantSku(string $merchantSku, string $unitType, int $idBranch): string
    {
        $skuWithType = sprintf(self::SKU_UNIT_STRING_FORMAT, $merchantSku, $unitType);
        if(array_key_exists($skuWithType, $this->skus) === true)
        {
            return $this->skus[$skuWithType];
        }

        if(array_key_exists($merchantSku, $this->skus) === true){
            $sku = $this
                ->skus[$merchantSku];
        }else{
            $sku = sprintf(static::NON_EXISTENT_INTEGRA_SKU_FORMAT, $idBranch, $merchantSku);
            return $this->addTypeToMerchantSku($sku, $unitType);
        }


        if ($sku === null) {
            throw EntityNotFoundException::product($merchantSku);
        }

        return $sku;
    }

    /**
     * @param int $idBranch
     * @param array $merchantSkus
     *
     * @return array
     */
    public function loadSkus(
        int $idBranch,
        array $merchantSkus
    ): array {
        $skuMap = $this
            ->queryContainer
            ->querySkusForMerchantSkus($idBranch, $merchantSkus)
            ->find()
            ->toArray();

        $skus = [];
        foreach ($skuMap as $sku) {
            $this->skus[$sku[MerchantPriceTableMap::COL_MERCHANT_SKU]] = $sku[SpyProductTableMap::COL_SKU];
            $skus[] = $sku[SpyProductTableMap::COL_SKU];
        }

        return $skus;
    }


    /**
     * @param string $sku
     * @param string $type
     * @return string
     */
    protected function addTypeToMerchantSku(string $sku, string $type) : string
    {
        if($type !== 'KA')
        {
            return sprintf(self::MERCHANT_SKU_W_TYPE_FORMAT, $sku, $type);
        }

        return $sku;
    }
}
