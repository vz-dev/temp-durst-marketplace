<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-03-14
 * Time: 13:32
 */

namespace Pyz\Zed\Discount\Business\Collector;


use Generated\Shared\Transfer\ClauseTransfer;
use Generated\Shared\Transfer\DiscountableItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Discount\Business\Collector\BaseCollector;
use Spryker\Zed\Discount\Business\Collector\CollectorInterface;
use Spryker\Zed\Discount\Business\QueryString\ComparatorOperatorsInterface;

class BranchCollector extends BaseCollector implements CollectorInterface
{
    /**
     * @var ComparatorOperatorsInterface
     */
    protected $comparators;

    /**
     * BranchCollector constructor.
     * @param ComparatorOperatorsInterface $comparators
     */
    public function __construct(ComparatorOperatorsInterface $comparators)
    {
        $this->comparators = $comparators;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param ClauseTransfer $clauseTransfer
     * @return DiscountableItemTransfer[]
     * @throws \Spryker\Zed\Discount\Business\Exception\ComparatorException
     */
    public function collect(QuoteTransfer $quoteTransfer, ClauseTransfer $clauseTransfer): array
    {
        $discountableItems = [];

        if ($this->comparators->compare($clauseTransfer, $quoteTransfer->getFkBranch()) === true) {
            foreach ($quoteTransfer->getItems() as $itemTransfer) {
                $discountableItems[] = $this
                    ->createDiscountableItemForItemTransfer($itemTransfer);
            }
        }

        return $discountableItems;
    }
}