<?php
/**
 * Durst - project - FilteredCalculator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.02.21
 * Time: 11:58
 */

namespace Pyz\Zed\Discount\Business\Calculator;


use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface;
use Spryker\Zed\Discount\Business\Distributor\DistributorInterface;
use Spryker\Zed\Discount\Business\Filter\DiscountableItemFilterInterface;
use Spryker\Zed\Discount\Business\QueryString\SpecificationBuilderInterface;
use Spryker\Zed\Discount\Dependency\Facade\DiscountToMessengerInterface;

class FilteredCalculator extends Calculator implements CalculatorInterface
{
    /**
     * @var \Spryker\Zed\Discount\Business\Filter\DiscountableItemFilterInterface
     */
    protected $discountableItemFilter;

    /**
     * @var \Pyz\Zed\Discount\Dependency\Facade\DiscountToTaxBridgeInterface
     */
    protected $taxFacade;

    /**
     * FilteredCalculator constructor.
     * @param \Spryker\Zed\Discount\Business\QueryString\SpecificationBuilderInterface $collectorBuilder
     * @param \Spryker\Zed\Discount\Dependency\Facade\DiscountToMessengerInterface $messengerFacade
     * @param \Spryker\Zed\Discount\Business\Distributor\DistributorInterface $distributor
     * @param \Spryker\Zed\Discount\Dependency\Plugin\DiscountAmountCalculatorPluginInterface[] $calculatorPlugins
     * @param \Spryker\Zed\Discount\Business\Filter\DiscountableItemFilterInterface $discountableItemFilter
     * @param DiscountToTaxBridgeInterface $taxFacade
     */
    public function __construct(
        SpecificationBuilderInterface $collectorBuilder,
        DiscountToMessengerInterface $messengerFacade,
        DistributorInterface $distributor,
        array $calculatorPlugins,
        DiscountableItemFilterInterface $discountableItemFilter,
        DiscountToTaxBridgeInterface $taxFacade
    ) {
        parent::__construct(
            $collectorBuilder,
            $messengerFacade,
            $distributor,
            $calculatorPlugins,
            $taxFacade
        );

        $this->discountableItemFilter = $discountableItemFilter;

        $this->taxFacade = $taxFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\DiscountTransfer $discountTransfer
     *
     * @return \Generated\Shared\Transfer\DiscountableItemTransfer[]
     */
    protected function collectItems(QuoteTransfer $quoteTransfer, DiscountTransfer $discountTransfer)
    {
        $collectedItems = parent::collectItems(
            $quoteTransfer,
            $discountTransfer
        );

        $collectedDiscountTransfer = $this->createCollectedDiscountTransfer($discountTransfer, $collectedItems);

        $filteredDiscountTransfer = $this->discountableItemFilter->filter($collectedDiscountTransfer);

        return (array)$filteredDiscountTransfer->getDiscountableItems();
    }
}
