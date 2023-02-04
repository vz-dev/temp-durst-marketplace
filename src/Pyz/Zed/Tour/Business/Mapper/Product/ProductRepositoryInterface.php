<?php
/**
 * Durst - project - ProductRepositoryInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 15.06.20
 * Time: 16:16
 */

namespace Pyz\Zed\Tour\Business\Mapper\Product;


interface ProductRepositoryInterface
{
    /**
     * @param string $sku
     *
     * @return string
     */
    public function getProductNameBySku(string $sku): string;

    /**
     * @param string $sku
     *
     * @return array
     */
    public function getGtinsBySku(string $sku): array;

    /**
     * @param string $sku
     *
     * @return string
     */
    public function getProductUnitBySku(string $sku): string;

    /**
     * {@inheritDoc}
     *
     * @param array $skus
     *
     * @return void
     */
    public function loadProductsBySkus(array $skus): void;
}
