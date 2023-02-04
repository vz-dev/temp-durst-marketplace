<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - RetailPaymentPreCheckPlugin.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.03.18
 * Time: 11:40
 */

namespace Pyz\Zed\RetailPayment\Communication\Plugin\Checkout;


use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\RetailPayment\Communication\RetailPaymentCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface;

/**
 * Class RetailPaymentPreCheckPlugin
 * @package Pyz\Zed\RetailPayment\Communication\Plugin\Checkout
 * @method RetailPaymentCommunicationFactory getFactory()
 */
class RetailPaymentPreCheckPlugin extends AbstractPlugin implements CheckoutPreCheckPluginInterface
{
    const ERROR_CODE_BRANCH_PAYMENT_METHOD = 1000;
    const ERROR_MESSAGE_BRANCH_PAYMENT_METHOD = 'The branch does not support the chosen payment method';

    /**
     * Specification:
     * - Executes a pre-condition for checkout
     * - Returns `false` if a pre-condition is not passed
     * - Check could pass even if CheckoutResponse errors are filled – in that case execution will continue
     * - Deprecated: Notifies about failed condition by filling CheckoutResponse errors, when output is `null`
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        if($this->checkPaymentMethod($quoteTransfer) !== true){
            $checkoutResponse->setIsSuccess(false);
            $error = (new CheckoutErrorTransfer())
                ->setErrorCode(self::ERROR_CODE_BRANCH_PAYMENT_METHOD)
                ->setMessage(self::ERROR_MESSAGE_BRANCH_PAYMENT_METHOD);
            $checkoutResponse->addError($error);

            return false;
        }
        return true;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return bool
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function checkPaymentMethod(QuoteTransfer $quoteTransfer) : bool
    {
        $idBranch = $quoteTransfer->getFkBranch();
        $validPaymentMethod = false;
        foreach ($quoteTransfer->getPayments() as $payment) {
            $valid = $this
                ->getFactory()
                ->getMerchantFacade()
                ->hasBranchPaymentMethod($idBranch, $payment->getPaymentMethod());

            if($valid === false){
                return false;
            }

            $validPaymentMethod = $valid;
        }

        return $validPaymentMethod;
    }
}