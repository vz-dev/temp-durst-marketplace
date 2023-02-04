<?php
/**
 * Created by PhpStorm.
 * User: ikesimmons
 * Date: 20.06.18
 * Time: 11:09
 */

namespace Pyz\Zed\Product\Communication\Plugins\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class ProductNameHydrationPlugin
 * @package Pyz\Zed\Product\Communication\Plugin\Sales
 * @method ProductFacadeInterface getFacade()
 */
class ProductNameHydrationPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{
    public function hydrate(OrderTransfer $orderTransfer)
    {
        return $this->getFacade()->hydrateProductName($orderTransfer);
    }


}