<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-14
 * Time: 13:04
 */

namespace Pyz\Zed\Discount\Business\DecisionRule;


use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Discount\Business\DecisionRule\DecisionRuleInterface;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperatorsInterface;

class BranchDecisionRule implements DecisionRuleInterface
{
    /**
     * @var ComparatorOperatorsInterface
     */
    protected $comparators;

    /**
     * BranchDecisionRule constructor.
     * @param ComparatorOperatorsInterface $comparators
     */
    public function __construct(ComparatorOperatorsInterface $comparators)
    {
        $this->comparators = $comparators;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $currentItemTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @throws \Spryker\Zed\Discount\Business\Exception\ComparatorException
     *
     * @return bool
     */
    public function isSatisfiedBy(QuoteTransfer $quoteTransfer, ItemTransfer $currentItemTransfer, ClauseTransfer $clauseTransfer)
    {
        return $this
            ->comparators
            ->compare(
                $clauseTransfer,
                $quoteTransfer->getFkBranch()
            );
    }
}