<?php
/**
 * Durst - project - ProductExportToProductBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 13:28
 */

namespace Pyz\Zed\ProductExport\Dependency\Facade;


use Generated\Shared\Transfer\ProductConcreteTransfer;
use Pyz\Zed\Product\Business\ProductFacadeInterface;

class ProductExportToProductBridge implements ProductExportToProductBridgeInterface
{
    /**
     * @var \Pyz\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * ProductExportToProductBridge constructor.
     * @param \Pyz\Zed\Product\Business\ProductFacadeInterface $productFacade
     */
    public function __construct(
        ProductFacadeInterface $productFacade
    )
    {
        $this->productFacade = $productFacade;
    }

    /**
     * @param int $idProduct
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function findProductConcreteById(int $idProduct): ProductConcreteTransfer
    {
        return $this
            ->productFacade
            ->findProductConcreteById(
                $idProduct
            );
    }
}
