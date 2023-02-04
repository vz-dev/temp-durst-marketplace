<?php
/**
 * Durst - project - ProductExportToProductQueryContainerBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 11:19
 */

namespace Pyz\Zed\ProductExport\Dependency\Persistence;


use Orm\Zed\Product\Persistence\SpyProductQuery;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;

class ProductExportToProductQueryContainerBridge implements ProductExportToProductQueryContainerBridgeInterface
{
    /**
     * @var \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * ProductExportToProductQueryContainerBridge constructor.
     * @param \Pyz\Zed\Product\Persistence\ProductQueryContainerInterface $productQueryContainer
     */
    public function __construct(
        ProductQueryContainerInterface $productQueryContainer
    )
    {
        $this->productQueryContainer = $productQueryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\Product\Persistence\SpyProductQuery
     */
    public function queryProduct(): SpyProductQuery
    {
        return $this
            ->productQueryContainer
            ->queryProduct();
    }
}
