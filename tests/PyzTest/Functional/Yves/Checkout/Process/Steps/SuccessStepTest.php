<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Functional\Yves\Checkout\Process\Steps;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use Pyz\Client\Customer\CustomerClientInterface;
use Pyz\Yves\Checkout\Process\Steps\SuccessStep;
use Spryker\Client\Cart\CartClientInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 * @group PyzTest
 * @group Yves
 * @group Checkout
 * @group Process
 * @group Steps
 * @group SuccessStepTest
 * Add your own group annotations below this line
 */
class SuccessStepTest extends Unit
{
    /**
     * @return void
     */
    public function testExecuteShouldEmptyQuoteTransfer()
    {
        $customerClientMock = $this->createCustomerClientMock();
        $customerClientMock->expects($this->once())->method('markCustomerAsDirty');

        $successStep = $this->createSuccessStep($customerClientMock);

        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->addItem(new ItemTransfer());

        $this->assertTrue($successStep->preCondition($quoteTransfer));
        $quoteTransfer = $successStep->execute($this->createRequest(), $quoteTransfer);

        $this->assertFalse($successStep->preCondition($quoteTransfer));
    }

    /**
     * @return void
     */
    public function testPostConditionsWhenOrderReferenceIsSetShouldReturnTrue()
    {
        $successStep = $this->createSuccessStep();

        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->setOrderReference('#12');

        $this->assertTrue($successStep->postCondition($quoteTransfer));
    }

    /**
     * @return void
     */
    public function testPostConditionsWhenOrderReferenceIsMissingShouldReturnFalse()
    {
        $successStep = $this->createSuccessStep();
        $quoteTransfer = new QuoteTransfer();

        $this->assertFalse($successStep->postCondition($quoteTransfer));
    }

    /**
     * @param \Pyz\Client\Customer\CustomerClientInterface|null $customerClientMock
     *
     * @return \Pyz\Yves\Checkout\Process\Steps\SuccessStep
     */
    protected function createSuccessStep($customerClientMock = null)
    {
        if ($customerClientMock === null) {
            $customerClientMock = $this->createCustomerClientMock();
        }

        $cartClientMock = $this->createCartClientMock();

        return new SuccessStep(
            $customerClientMock,
            $cartClientMock,
            'success_route',
            'escape_route'
        );
    }

    /**
     * @return MockObject|\Spryker\Client\Cart\CartClientInterface
     */
    protected function createCartClientMock()
    {
        return $this->getMockBuilder(CartClientInterface::class)->getMock();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * @return MockObject|\Pyz\Client\Customer\CustomerClientInterface
     */
    protected function createCustomerClientMock()
    {
        return $this->getMockBuilder(CustomerClientInterface::class)->getMock();
    }
}
