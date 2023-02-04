<?php
/**
 * Durst - project - ProductConcreteAfterUpdateTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.11.18
 * Time: 10:07
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Product;


use Generated\Shared\Transfer\ProductConcreteTransfer;
use Pyz\Shared\Product\ProductConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Business\Product\Observer\ProductConcreteUpdateObserverInterface;
use Spryker\Zed\Touch\Business\TouchFacade;

/**
 * Class ProductConcreteAfterUpdateTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Product
 * @method TouchFacade getFacade()
 */
class ProductConcreteAfterUpdateTouchPlugin extends AbstractPlugin implements ProductConcreteUpdateObserverInterface
{
    /**
     * @param \Generated\Shared\Transfer\ProductConcreteTransfer $productConcreteTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function update(ProductConcreteTransfer $productConcreteTransfer) : ProductConcreteTransfer
    {
        $this->getFacade()->touchActive(ProductConstants::RESOURCE_TYPE_PRODUCT, $productConcreteTransfer->getIdProductConcrete());

        return $productConcreteTransfer;
    }

}