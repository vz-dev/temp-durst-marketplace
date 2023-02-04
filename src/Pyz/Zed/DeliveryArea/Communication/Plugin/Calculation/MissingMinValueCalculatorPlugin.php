<?php
/**
 * Durst - project - MissingMinValueCalculatorPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 23.07.18
 * Time: 11:08
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class MissingMinValueCalculatorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class MissingMinValueCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
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
            ->calculateMissingMinValueTotal($calculableObjectTransfer);
    }
}