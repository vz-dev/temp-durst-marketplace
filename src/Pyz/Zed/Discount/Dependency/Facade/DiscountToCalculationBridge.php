<?php
/**
 * Durst - project - DiscountToCalculationBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.09.20
 * Time: 08:46
 */

namespace Pyz\Zed\Discount\Dependency\Facade;


use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Calculation\Business\CalculationFacadeInterface;

class DiscountToCalculationBridge implements DiscountToCalculationBridgeInterface
{
    /**
     * @var \Pyz\Zed\Calculation\Business\CalculationFacadeInterface
     */
    protected $calculationFacade;

    /**
     * DiscountToCalculationBridge constructor.
     * @param \Pyz\Zed\Calculation\Business\CalculationFacadeInterface $calculationFacade
     */
    public function __construct(
        CalculationFacadeInterface $calculationFacade
    )
    {
        $this->calculationFacade = $calculationFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function recalculateQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this
            ->calculationFacade
            ->recalculateQuote(
                $quoteTransfer
            );
    }
}
