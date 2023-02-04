<?php
/**
 * Durst - project - PriceImportToProductBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:03
 */

namespace Pyz\Zed\PriceImport\Dependency\Facade;


use Generated\Shared\Transfer\ProductConcreteTransfer;
use Pyz\Zed\Product\Business\ProductFacadeInterface;

class PriceImportToProductBridge implements PriceImportToProductBridgeInterface
{
    /**
     * @var \Pyz\Zed\Product\Business\ProductFacadeInterface
     */
    protected $productFacade;

    /**
     * PriceImportToProductBridge constructor.
     * @param \Pyz\Zed\Product\Business\ProductFacadeInterface $productFacade
     */
    public function __construct(ProductFacadeInterface $productFacade)
    {
        $this->productFacade = $productFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $concreteSku
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     * @throws \Spryker\Zed\Product\Business\Exception\MissingProductException
     */
    public function getProductConcrete(string $concreteSku): ProductConcreteTransfer
    {
        return $this
            ->productFacade
            ->getProductConcrete(
                $concreteSku
            );
    }
}
