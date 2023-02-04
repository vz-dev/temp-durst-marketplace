<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-14
 * Time: 12:00
 */

namespace Pyz\Zed\Discount\Communication\Plugin\DecisionRule;


use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperators;
use Spryker\Zed\Discount\Dependency\Plugin\DecisionRulePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class BranchDecisionRulePlugin
 * @package Pyz\Zed\Discount\Communication\Plugin\DecisionRule
 * @method DiscountFacadeInterface getFacade()
 */
class BranchDecisionRulePlugin extends AbstractPlugin implements DecisionRulePluginInterface
{
    protected const FIELD_NAME = 'branch';

    /**
     * Specification:
     *
     * - Makes decision on given Quote or Item transfer.
     * - Uses Spryker\Zed\Discount\Business\QueryString\ComparatorOperatorsInterface to compare item value with ClauseTransfer.
     * - Returns false when not matching.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return bool
     */
    public function isSatisfiedBy(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer, ClauseTransfer $clauseTransfer): bool
    {
        return $this
            ->getFacade()
            ->isBranchSatisfiedBy(
                $quoteTransfer,
                $itemTransfer,
                $clauseTransfer
            );
    }

    /**
     * Name of field as used in query string
     *
     * @api
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return static::FIELD_NAME;
    }

    /**
     * Data types used by this field. (string, integer, list)
     *
     * @api
     *
     * @return array
     */
    public function acceptedDataTypes(): array
    {
        return [
            ComparatorOperators::TYPE_NUMBER
        ];
    }
}