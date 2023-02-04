<?php
/**
 * Durst - project - Card.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.01.19
 * Time: 13:49
 */

namespace Pyz\Zed\HeidelpayRest\Business\PaymentType;

use heidelpayPHP\Resources\Customer;
use heidelpayPHP\Resources\Metadata;
use heidelpayPHP\Resources\PaymentTypes\Card;
use heidelpayPHP\Resources\TransactionTypes\Authorization;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Zed\HeidelpayRest\HeidelpayRestConfig;

class CardType implements CardTypeInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig
     */
    protected $config;

    /**
     * CardType constructor.
     *
     * @param \Pyz\Zed\HeidelpayRest\HeidelpayRestConfig $config
     */
    public function __construct(HeidelpayRestConfig $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @param \heidelpayPHP\Resources\PaymentTypes\Card $paymentType
     * @param float $amount
     * @param null|string $returnUrl
     *
     * @return \heidelpayPHP\Resources\TransactionTypes\Authorization
     */
    public function authorize(
        Card $paymentType,
        float $amount,
        ?string $returnUrl = null,
        ?Customer $customer = null,
        ?Metadata $metadata = null
    ): Authorization {

        if($returnUrl === null) {
            $returnUrl = $this->config->getReturnUrl();
        }
        return $paymentType
        ->authorize(
            $amount,
            HeidelpayRestConstants::HEIDELPAY_REST_CURRENCY_EUR,
            $returnUrl,
            $customer,
            null,
            $metadata
        );
    }
}
