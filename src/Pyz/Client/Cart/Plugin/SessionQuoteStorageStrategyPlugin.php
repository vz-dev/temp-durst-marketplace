<?php
/**
 * Durst - project - SessionQuoteStorageStrategyPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.09.18
 * Time: 16:05
 */

namespace Pyz\Client\Cart\Plugin;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Client\CartExtension\Dependency\Plugin\QuoteStorageStrategyPluginInterface;
use Spryker\Client\Cart\Plugin\SessionQuoteStorageStrategyPlugin as SprykerSessionQuoteStorageStrategyPlugin;
use stdClass;

class SessionQuoteStorageStrategyPlugin extends SprykerSessionQuoteStorageStrategyPlugin implements QuoteStorageStrategyPluginInterface
{
    protected const KEY_VOUCHER_CODE = 'voucher_code';
    protected const KEY_SHIPPING_ADDRESS = 'shippingAddress';
    protected const KEY_SHIPPING_ADDRESS_ZIP_CODE = 'zipCode';

    /**
     * {@inheritdoc}
     *
     * @param ItemTransfer[]|array $itemTransfers
     * @param BranchTransfer $branchTransfer
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @param stdClass|null $requestObject
     * @return QuoteTransfer
     */
    public function addItemsForBranchAndConcreteTimeSlot(
        array $itemTransfers,
        BranchTransfer $branchTransfer,
        ConcreteTimeSlotTransfer $concreteTimeSlotTransfer,
        stdClass $requestObject = null
    ): QuoteTransfer
    {
        $cartChangeTransfer = $this->createCartChangeTransfer();
        $cartChangeTransfer->setBranch($branchTransfer);
        $cartChangeTransfer->setConcreteTimeSlot($concreteTimeSlotTransfer);
        $cartChangeTransfer->getQuote()->setFkBranch($branchTransfer->getIdBranch());
        $cartChangeTransfer->getQuote()->setFkConcreteTimeSlot($concreteTimeSlotTransfer->getIdConcreteTimeSlot());

        foreach ($itemTransfers as $itemTransfer) {
            $cartChangeTransfer->addItem($itemTransfer);
        }

        $cartChangeTransfer
            ->getQuote()
            ->addConcreteTimeSlots($concreteTimeSlotTransfer);

        if ($requestObject !== null) {
            $cartChangeTransfer
                ->getQuote()
                ->setShippingAddress(
                    $this
                        ->createShippingAddress(
                            $requestObject
                        )
                );

            $cartChangeTransfer
                ->getQuote()
                ->addVoucherDiscount(
                    $this
                        ->createDiscountTransfer(
                            $requestObject
                        )
                );
        }

        return $this->getCartZedStub()->addItem($cartChangeTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @param ItemTransfer[]|array $itemTransfers
     * @param BranchTransfer $branchTransfer
     * @param ConcreteTimeSlotTransfer[]|array $concreteTimeSlotTransfers
     * @param stdClass|null $requestObject
     * @return QuoteTransfer
     */
    public function addItemsForBranchAndConcreteTimeSlots(
        array $itemTransfers,
        BranchTransfer $branchTransfer,
        array $concreteTimeSlotTransfers,
        stdClass $requestObject = null
    ): QuoteTransfer
    {
        $cartChangeTransfer = $this
            ->createCartChangeTransfer()
            ->setBranch($branchTransfer);

        $cartChangeTransfer
            ->getQuote()
            ->setFkBranch($branchTransfer->getIdBranch());

        foreach ($itemTransfers as $itemTransfer) {
            $cartChangeTransfer->addItem($itemTransfer);
        }

        foreach ($concreteTimeSlotTransfers as $concreteTimeSlotTransfer) {
            $cartChangeTransfer
                ->getQuote()
                ->addConcreteTimeSlots($concreteTimeSlotTransfer);
        }

        if ($requestObject !== null) {
            $cartChangeTransfer
                ->getQuote()
                ->setShippingAddress(
                    $this
                        ->createShippingAddress(
                            $requestObject
                        )
                );

            $cartChangeTransfer
                ->getQuote()
                ->addVoucherDiscount(
                    $this
                        ->createDiscountTransfer(
                            $requestObject
                        )
                );
        }

        return $this
            ->getCartZedStub()
            ->addItem($cartChangeTransfer);
    }

    /**
     * @param stdClass $requestObject
     * @return DiscountTransfer
     */
    protected function createDiscountTransfer(stdClass $requestObject): DiscountTransfer
    {
        $discountTransfer = new DiscountTransfer();

        if (isset($requestObject->{static::KEY_VOUCHER_CODE}) === true) {
            $discountTransfer
                ->setVoucherCode(
                    $requestObject
                        ->{static::KEY_VOUCHER_CODE}
                );
        }

        return $discountTransfer;
    }

    /**
     * @param stdClass $requestObject
     * @return AddressTransfer
     */
    protected function createShippingAddress(stdClass $requestObject): AddressTransfer
    {
        $shippingAddress = new AddressTransfer();

        if (isset($requestObject->{static::KEY_SHIPPING_ADDRESS}) === true) {
            $shipping = $requestObject
                ->{static::KEY_SHIPPING_ADDRESS};

            $shippingAddress
                ->setZipCode(
                    $shipping
                        ->{static::KEY_SHIPPING_ADDRESS_ZIP_CODE}
                );
        }

        return $shippingAddress;
    }

    /**
     * @param array $itemTransfers
     * @param BranchTransfer $branchTransfer
     * @param stdClass|null $requestObject
     * @return QuoteTransfer
     */
    public function addItemsForBranchFlexTimeSlots(
        array $itemTransfers,
        BranchTransfer $branchTransfer,
        stdClass $requestObject = null
    ): QuoteTransfer
    {
        $cartChangeTransfer = $this->createCartChangeTransfer();
        $cartChangeTransfer->setBranch($branchTransfer);
        $cartChangeTransfer->getQuote()->setFkBranch($branchTransfer->getIdBranch());

        $cartChangeTransfer->getQuote()->setUseFlexibleTimeSlots(true);

        foreach ($itemTransfers as $itemTransfer) {
            $cartChangeTransfer->addItem($itemTransfer);
        }

        if ($requestObject !== null) {
            $cartChangeTransfer
                ->getQuote()
                ->setShippingAddress(
                    $this
                        ->createShippingAddress(
                            $requestObject
                        )
                );

            $cartChangeTransfer
                ->getQuote()
                ->addVoucherDiscount(
                    $this
                        ->createDiscountTransfer(
                            $requestObject
                        )
                );
        }

        return $this->getCartZedStub()->addItem($cartChangeTransfer);
    }
}
