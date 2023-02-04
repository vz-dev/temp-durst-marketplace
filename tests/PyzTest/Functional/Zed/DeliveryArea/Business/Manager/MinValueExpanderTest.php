<?php
namespace PyzTest\Functional\Zed\DeliveryArea\Business\Manager;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\DeliveryArea\Business\Manager\MinValueExpander;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

class MinValueExpanderTest extends Unit
{
    protected const MIV_VALUE = 5;

    /**
     * @var \PyzTest\Functional\Zed\DeliveryArea\DeliveryAreaBusinessTester
     */
    protected $tester;

    /**
     * @var \Pyz\Zed\DeliveryArea\Business\Manager\MinValueExpander
     */
    protected $minValueExpander;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->minValueExpander = new MinValueExpander();
    }

    /**
     * @return void
     */
    protected function _after(): void
    {
    }

    /**
     * @return void
     */
    public function testSuccessfullySetMinValueOnQuote(): void
    {
        $cartTransfer = $this
            ->createCartChangeTransfer();

        $this
            ->minValueExpander
            ->expandItemsByMinValue($cartTransfer);

        $this
            ->assertEquals(
                self::MIV_VALUE,
                $cartTransfer->getQuote()->getMinValue()
            );
    }

    /**
     * @return void
     */
    public function testNullQuoteThrowsRequiredTransferPropertyException(): void
    {
        $cartTransfer = $this
            ->createCartChangeTransfer();

        $cartTransfer
            ->setQuote(null);

        $this
            ->expectException(RequiredTransferPropertyException::class);

        $this
            ->minValueExpander
            ->expandItemsByMinValue($cartTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\CartChangeTransfer
     */
    protected function createCartChangeTransfer(): CartChangeTransfer
    {
        $transfer = new CartChangeTransfer();

        $items = new ArrayObject([]);

        $quote = new QuoteTransfer();

        $concreteTimeSlot = (new ConcreteTimeSlotTransfer())
            ->setMinValue(self::MIV_VALUE);

        $quote
            ->addConcreteTimeSlots($concreteTimeSlot);

        $branch = new BranchTransfer();

        $transfer
            ->setBranch($branch)
            ->setConcreteTimeSlot($concreteTimeSlot)
            ->setItems($items)
            ->setQuote($quote);

        return $transfer;
    }
}
