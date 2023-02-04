<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 11.10.18
 * Time: 15:43
 */

namespace Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation;



use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Spryker\Zed\Calculation\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class MissingMinUnitsCalculatorPlugin
 * @package Pyz\Zed\DeliveryArea\Communication\Plugin\Calculation
 * @method DeliveryAreaFacadeInterface getFacade()
 */
class MissingMinUnitsCalculatorPlugin extends AbstractPlugin implements CalculationPluginInterface
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
            ->calculateMissingMinUnits($calculableObjectTransfer);
    }
}