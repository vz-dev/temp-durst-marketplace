<?php
/**
 * Created by PhpStorm.
 * User: ikesimmons
 * Date: 20.06.18
 * Time: 11:16
 */

namespace Pyz\Zed\Product\Business;


use Generated\Shared\Transfer\GtinTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Product\Business\Mapper\ProductExporterMapper;
use Spryker\Zed\Product\Business\ProductFacadeInterface as SprykerProductFacadeInterface;

interface ProductFacadeInterface extends SprykerProductFacadeInterface
{
    /**
     * returns a OrderTransfer that is hydrate with the combination of the abstract product name and
     * the deposit name e.g. Cola Zero - 20 x 0.5L
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateProductName(OrderTransfer $orderTransfer);

    /**
     * @return ProductExporterMapper
     */
    public function getProductExporterMapper() : ProductExporterMapper;

    /**
     * Returns GtinTransfers for gtins being part of Gtin Attribute of an active Product.
     * Each Gtin Transfer is hydrated with Product Name and SkuTransfers for each Product
     * that is reffering to the given Gtin.
     * Each Sku Transfer is hydrated with Sku and Deposit id.
     *
     * @return GtinTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getGtins() : array;
}
