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
use Spryker\Zed\Product\Business\ProductFacade as SprykerProductFacade;


/**
 * @method \Pyz\Zed\Product\Business\ProductBusinessFactory getFactory()
 */
class ProductFacade extends SprykerProductFacade implements ProductFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param OrderTransfer $orderTransfer
     * @return OrderTransfer
     */
    public function hydrateProductName(OrderTransfer $orderTransfer)
    {
        return $this
                ->getFactory()
                ->createProductNameHydrator()
                ->hydrateProductName($orderTransfer);
    }

    /**
     * @return ProductExporterMapper
     */
    public function getProductExporterMapper() : ProductExporterMapper
    {
        return $this
            ->getFactory()
            ->createProductExporterMapper();
    }

    /**
     * {@inheritdoc}
     *
     * @return GtinTransfer[]
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getGtins() : array
    {
        return $this
            ->getFactory()
            ->createGtinManager()
            ->getGtins();
    }

    /**
     * @param array $productSkus
     * @return mixed
     */
    public function deactivateProductConcreteBySkus(array $productSkus){

    }
}
