<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-14
 * Time: 13:23
 */

namespace Pyz\Zed\Discount\Communication\Plugin\Collector;


use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Business\DiscountFacadeInterface;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperators;
use Spryker\Zed\Discount\Dependency\Plugin\CollectorPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * Class ItemByBranchCollectorPlugin
 * @package Pyz\Zed\Discount\Communication\Plugin\Collector
 * @method DiscountFacadeInterface getFacade()
 */
class ItemByBranchCollectorPlugin extends AbstractPlugin implements CollectorPluginInterface
{
    protected const FIELD_NAME = 'branch';

    /**
     * Specification:
     *  - Collects items to which discount have to be applied, ClauseTransfer holds query string parameters,
     *  - Uses Spryker\Zed\Discount\Business\QueryString\ComparatorOperatorsInterface to compare item value with ClauseTransfer.
     *  - Returns array of discountable items with reference to original CalculatedDiscountTransfer, which is modified by reference by distributor.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ClauseTransfer $clauseTransfer
     *
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer[]
     */
    public function collect(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): array
    {
        return $this
            ->getFacade()
            ->collectByBranch(
                $quoteTransfer,
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
     * @return string[]
     */
    public function acceptedDataTypes(): array
    {
        return [
            ComparatorOperators::TYPE_NUMBER
        ];
    }
}