<?php
/**
 * Durst - project - SepaType.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-11
 * Time: 09:41
 */

namespace Pyz\Zed\HeidelpayRest\Business\PaymentType;

use heidelpayPHP\Heidelpay;
use heidelpayPHP\Resources\PaymentTypes\SepaDirectDebit;

class SepaType implements SepaTypeInterface
{
    protected const SANDBOX_IBAN = 'DE89370400440532013000';

    /**
     * @var \heidelpayPHP\Heidelpay
     */
    protected $heidelpayClient;

    /**
     * SepaType constructor.
     *
     * @param \heidelpayPHP\Heidelpay $heidelpayClient
     */
    public function __construct(Heidelpay $heidelpayClient)
    {
        $this->heidelpayClient = $heidelpayClient;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     * @throws \RuntimeException
     * @throws \heidelpayPHP\Exceptions\HeidelpayApiException
     */
    public function generateSepaSandboxPaymentTypeId(): string
    {
        $paymentType = new SepaDirectDebit(self::SANDBOX_IBAN);

        $sepaPaymentType = $this
            ->heidelpayClient
            ->createPaymentType($paymentType);

        return $sepaPaymentType->getId();
    }
}
