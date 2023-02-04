<?php
/**
 * Durst - project - ProductAbstractAfterUpdateTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.11.18
 * Time: 12:16
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Product;


use Generated\Shared\Transfer\ProductAbstractTransfer;
use Pyz\Shared\Product\ProductConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Business\Product\Observer\ProductAbstractUpdateObserverInterface;
use Spryker\Zed\Touch\Business\TouchFacade;


/**
 * Class ProductAbstractAfterUpdateTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Product
 * @method TouchFacade getFacade()
 */
class ProductAbstractAfterUpdateTouchPlugin extends AbstractPlugin implements ProductAbstractUpdateObserverInterface
{
    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function update(ProductAbstractTransfer $productAbstractTransfer) : ProductAbstractTransfer
    {
        $this->getFacade()->touchActive(ProductConstants::RESOURCE_TYPE_PRODUCT.'_ABSTRACT', $productAbstractTransfer->getIdProductAbstract());

        return $productAbstractTransfer;
    }

}