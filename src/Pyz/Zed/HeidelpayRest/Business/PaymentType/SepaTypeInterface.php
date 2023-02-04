<?php
/**
 * Durst - project - SepaTypeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-11
 * Time: 09:54
 */

namespace Pyz\Zed\HeidelpayRest\Business\PaymentType;


interface SepaTypeInterface
{
    /**
     * @return string
     * @throws \RuntimeException
     * @throws \heidelpayPHP\Exceptions\HeidelpayApiException
     */
    public function generateSepaSandboxPaymentTypeId(): string;
}