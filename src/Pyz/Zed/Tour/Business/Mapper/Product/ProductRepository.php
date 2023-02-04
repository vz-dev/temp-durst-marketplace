<?php
/**
 * Durst - project - ProductRepository.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.06.20
 * Time: 16:16
 */

namespace Pyz\Zed\Tour\Business\Mapper\Product;

use Orm\Zed\Product\Persistence\SpyProduct;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class ProductRepository implements ProductRepositoryInterface
{
    protected const KEY_GTINS = 'KEY_GTINS';
    protected const KEY_NAME = 'KEY_NAME';
    protected const KEY_UNIT = 'KEY_UNIT';

    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var array
     */
    protected $productData;

    /**
     * ProductRepository constructor.
     *
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer
     */
    public function __construct(TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $sku
     *
     * @return string
     */
    public function getProductNameBySku(string $sku): string
    {
        return $this
            ->getProductInformationBySku($sku, self::KEY_NAME);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $sku
     *
     * @return array
     */
    public function getGtinsBySku(string $sku): array
    {
        $gtins = $this
            ->getProductInformationBySku($sku, self::KEY_GTINS);
        if ($gtins === '') {
            return [];
        }

        return $gtins;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $sku
     *
     * @return string
     */
    public function getProductUnitBySku(string $sku): string
    {
        return $this
            ->getProductInformationBySku($sku, self::KEY_UNIT);
    }

    /**
     * @param array $skus
     *
     * @return void
     */
    public function loadProductsBySkus(array $skus): void
    {
        $productEntities = $this
            ->queryContainer
            ->queryProductsWithAttributesBySkus($skus)
            ->find();

        foreach ($productEntities as $productEntity) {
            $attributes = json_decode($productEntity->getAttributes(), true);

            $gtins = $this->getLocalizedGtins($productEntity);
            if (array_key_exists('gtin', $attributes)) {
                $gtins = $gtins + $attributes['gtin'];
            }

            $productName = '';
            if (array_key_exists('name', $attributes)) {
                $productName = $attributes['name'];
            }

            $unit = '';
            if (array_key_exists('unit', $attributes)) {
                $unit = $attributes['unit'];
            }

            $this->productData[$productEntity->getSku()] = [
                self::KEY_NAME => $productName,
                self::KEY_UNIT => $unit,
                self::KEY_GTINS => $gtins,
            ];
        }
    }

    /**
     * @param string $sku
     * @param string $key
     *
     * @return mixed|string
     */
    protected function getProductInformationBySku(
        string $sku,
        string $key
    ) {
        if ($this->productData === null ||
            array_key_exists($sku, $this->productData) !== true ||
            array_key_exists($key, $this->productData[$sku]) !== true) {
            return '';
        }

        return $this
            ->productData[$sku][$key];
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProduct $product
     *
     * @return array
     */
    protected function getLocalizedGtins(SpyProduct $product): array
    {
        $gtins = [];
        foreach ($product->getSpyProductLocalizedAttributess() as $localizedAttributes) {
            $attributes = json_decode($localizedAttributes->getAttributes(), true);

            if (array_key_exists('gtin', $attributes)) {
                $gtins[] = $attributes['gtin'];
            }
        }

        return $gtins;
    }
}
