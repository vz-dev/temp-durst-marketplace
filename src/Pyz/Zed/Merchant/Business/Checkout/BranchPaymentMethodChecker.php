<?php
/**
 * Durst - project - BranchPaymentMethodChecker.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 04.04.19
 * Time: 14:47
 */

namespace Pyz\Zed\Merchant\Business\Checkout;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class BranchPaymentMethodChecker implements BranchPaymentMethodCheckerInterface
{
    protected const ERROR_MESSAGE = 'The selected branch does not support the selected payment method';
    protected const ERROR_CODE = 'M0000001';

    protected const ERROR_MESSAGE_CUSTOMER_TYPE = 'The selected branch does not support the selected payment method for customer typ(b2b/b2c)';
    protected const ERROR_CODE_CUSTOMER_TYPE = 'M0000002';

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * BranchPaymentMethodChecker constructor.
     *
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     */
    public function __construct(MerchantQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkBranchSupportsPaymentMethod(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {

        $this->assertRequirements($quoteTransfer);

        foreach ($quoteTransfer->getPayments() as $payment) {

            if($quoteTransfer->getCustomer()->getIsPrivate() !== null){
                if ($this->checkPaymentByCustomerType($payment, $quoteTransfer) !== true) {
                    $this->prepareCheckoutResponseTransfer($checkoutResponseTransfer, true);

                    return false;
                }
                continue;
            }

            if ($this->checkPayment($payment, $quoteTransfer->getFkBranch()) !== true) {
                $this->prepareCheckoutResponseTransfer($checkoutResponseTransfer);

                return false;
            }
        }

        return true;
    }

    /**
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @param bool $customerType
     *
     * @return CheckoutResponseTransfer
     */
    protected function prepareCheckoutResponseTransfer(CheckoutResponseTransfer $checkoutResponseTransfer, bool $customerType=false): CheckoutResponseTransfer
    {
        $checkoutResponseTransfer->setIsSuccess(false);
        $errorTransfer = new CheckoutErrorTransfer();

        if($customerType === true){
            $errorTransfer
                ->setErrorCode(static::ERROR_CODE_CUSTOMER_TYPE)
                ->setMessage(static::ERROR_MESSAGE_CUSTOMER_TYPE);
        }else{
            $errorTransfer
                ->setErrorCode(static::ERROR_CODE)
                ->setMessage(static::ERROR_MESSAGE);
        }

        return $checkoutResponseTransfer->addError($errorTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentTransfer $paymentTransfer
     * @param int $idBranch
     *
     * @return bool
     */
    protected function checkPayment(PaymentTransfer $paymentTransfer, int $idBranch): bool
    {
        $branchToPaymentEntity = $this
            ->queryContainer
            ->queryBranchToPaymentMethod()
            ->useSpyPaymentMethodQuery()
                ->filterByCode($paymentTransfer->getPaymentSelection())
            ->endUse()
            ->filterByFkBranch($idBranch)
            ->count();

        return ($branchToPaymentEntity > 0);
    }

    /**
     * @param PaymentTransfer $paymentTransfer
     * @param QuoteTransfer $quoteTransfer
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function checkPaymentByCustomerType(PaymentTransfer $paymentTransfer, QuoteTransfer $quoteTransfer): bool
    {
        $branchToPaymentEntity = $this
            ->queryContainer
            ->queryBranchToPaymentMethod()
            ->useSpyPaymentMethodQuery()
            ->filterByCode($paymentTransfer->getPaymentSelection())
            ->endUse()
            ->filterByFkBranch($quoteTransfer->getFkBranch());

        if($this->getIsB2CFromQuoteTransfer($quoteTransfer) === true){
            $branchToPaymentEntity
                ->filterByB2c(true);
        }else{
            $branchToPaymentEntity
                ->filterByB2b(true);
        }

        $result = $branchToPaymentEntity
            ->count();

        return ($result > 0);
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return bool|null
     */
    protected function getIsB2CFromQuoteTransfer(QuoteTransfer $quoteTransfer): ?bool
    {
        return $quoteTransfer
            ->getCustomer()
            ->getIsPrivate();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function assertRequirements(QuoteTransfer $quoteTransfer): void
    {
        $quoteTransfer
            ->requireFkBranch()
            ->requirePayments();
    }
}
