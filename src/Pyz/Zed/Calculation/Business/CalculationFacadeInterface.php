<?php
/**
 * Durst - project - CalculationFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 12.08.20
 * Time: 10:34
 */

namespace Pyz\Zed\Calculation\Business;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Calculation\Business\CalculationFacadeInterface as SprykerCalculationFacadeInterface;


interface CalculationFacadeInterface extends SprykerCalculationFacadeInterface
{
    /**
     * Specification:
     *  - creates transfers for each tax rate that is used in items and expenses
     *  - each transfer contains the tax rate and the sum amount of taxes calculated by this rate
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function calculateTaxRateTotal(CalculableObjectTransfer $calculableObjectTransfer): void;

    /**
     * Specification:
     *  - creates transfers for each tax rate that is used in items and expenses
     *  - each transfer contains the tax rate and the sum amount of taxes calculated by this rate
     *  - takes cancellations into account
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     * @return void
     */
    public function calculateOrderTaxRateTotal(CalculableObjectTransfer $calculableObjectTransfer): void;
}
