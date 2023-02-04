<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.01.18
 * Time: 14:15
 */

namespace Pyz\Yves\Checkout\Process\Steps;


use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Step\StepWithBreadcrumbInterface;

class ConcreteTimeSlotStep extends AbstractBaseStep implements StepWithBreadcrumbInterface
{
    /**
     * BranchSelectionStep constructor.
     * @param $stepRoute
     * @param $escapeRoute
     */
    public function __construct(
        $stepRoute,
        $escapeRoute
    ) {
        parent::__construct($stepRoute, $escapeRoute);
    }

    /**
     * Require input, should we render view with form or just skip step after calling execute.
     *
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function requireInput(AbstractTransfer $dataTransfer)
    {
        return true;
    }

    /**
     * Conditions that should be met for this step to be marked as completed. returns true when satisfied.
     *
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|QuoteTransfer $dataTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $dataTransfer)
    {
        if(false === $this->isConcreteTimeSlotInQuote($dataTransfer)){
            return false;
        }

        return true;
    }

    protected function isConcreteTimeSlotInQuote(QuoteTransfer $quoteTransfer)
    {
        return ($quoteTransfer->getStartTime() !== null && $quoteTransfer->getEndTime() !== null);
    }

    /**
     * @return string
     */
    public function getBreadcrumbItemTitle()
    {
        return 'checkout.step.payment.title';
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function isBreadcrumbItemEnabled(AbstractTransfer $dataTransfer)
    {
        return true;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function isBreadcrumbItemHidden(AbstractTransfer $dataTransfer)
    {
        return false;
    }
}