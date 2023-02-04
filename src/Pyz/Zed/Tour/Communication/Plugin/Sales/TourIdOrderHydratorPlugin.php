<?php
/**
 * Durst - project - TourIdOrderHydratorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 22.10.19
 * Time: 11:36
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Sales;


use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * Class TourIdOrderHydratorPlugin
 * @package Pyz\Zed\Tour\Communication\Plugin\Sales
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 */
class TourIdOrderHydratorPlugin extends AbstractPlugin implements HydrateOrderPluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     * @api
     *
     */
    public function hydrate(OrderTransfer $orderTransfer)
    {
        return $this
            ->getFacade()
            ->hydrateOrderByTourId($orderTransfer);
    }
}
