<?php
/**
 * Durst - project - DeliveryCostTotalCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.05.18
 * Time: 10:03
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DeliveryCostTotalCalculatorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class DeliveryCostTotalCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateDeliveryCostTotal($calculableObjectTransfer);
    }
}