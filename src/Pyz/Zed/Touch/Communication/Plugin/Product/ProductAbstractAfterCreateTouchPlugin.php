<?php
/**
 * Durst - project - ProductAbstractAfterCreateTouchPlugin.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 19.11.18
 * Time: 14:02
 */

namespace Pyz\Zed\Touch\Communication\Plugin\Product;


use Generated\Shared\Transfer\ProductAbstractTransfer;
use Pyz\Shared\Product\ProductConstants;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Business\Product\Observer\ProductAbstractCreateObserverInterface;
use Spryker\Zed\Touch\Business\TouchFacade;

/**
 * Class ProductAbstractAfterCreateTouchPlugin
 * @package Pyz\Zed\Touch\Communication\Plugin\Product
 * @method TouchFacade getFacade()
 */
class ProductAbstractAfterCreateTouchPlugin extends AbstractPlugin implements ProductAbstractCreateObserverInterface
{
    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function create(ProductAbstractTransfer $productAbstractTransfer) : ProductAbstractTransfer
    {
        $this->getFacade()->touchActive(ProductConstants::RESOURCE_TYPE_PRODUCT.'_ABSTRACT', $productAbstractTransfer->getIdProductAbstract());
        return $productAbstractTransfer;
    }

}