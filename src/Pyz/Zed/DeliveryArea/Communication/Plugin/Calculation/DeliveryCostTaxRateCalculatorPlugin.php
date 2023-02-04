<?php
/**
 * Durst - project - DeliveryCostTaxRateCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 21.06.18
 * Time: 14:49
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class DeliveryCostTaxRateCalculatorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class DeliveryCostTaxRateCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this
            ->getFacade()
            ->calculateDeliveryCostTaxRate($calculableObjectTransfer);
    }
}