<?php
/**
 * Durst - project - PaymentProviderHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-18
 * Time: 13:19
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\City;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Shared\HeidelpayRest\HeidelpayRestConstants;
use Pyz\Shared\RetailPayment\RetailPaymentConfig;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\CityMerchantKeyResponseInterface;
use stdClass;

class PaymentProviderHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * PaymentProviderHydrator constructor.
     * @param AppRestApiClientInterface $client
     * @param AppRestApiConfig $config
     */
    public function __construct(
        AppRestApiClientInterface $client,
        AppRestApiConfig $config
    )
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1'): void
    {
        $city = $responseObject
            ->{CityMerchantKeyResponseInterface::KEY_CITY};

        if ($city === null) {
            return;
        }

        $responseObject
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER} = $this->hydratePaymentProvider();
    }

    /**
     * @return stdClass[]
     */
    protected function hydratePaymentProvider(): array
    {
        $returnArray = [];

        $paymentProvider = new stdClass();

        $paymentProvider
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER_NAME} = RetailPaymentConfig::PROVIDER_NAME;
        $paymentProvider
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER_SEPA_MANDATE_URL} = $this
                ->config
                ->getSepaMandateUrl();
        $paymentProvider
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER_PAYMENT_METHODS} = $this->hydratePaymentMethods();

        $returnArray[] = $paymentProvider;

        return $returnArray;
    }

    /**
     * @return stdClass[]
     */
    protected function hydratePaymentMethods(): array
    {
        $responseTransfer = $this
            ->client
            ->getPaymentMethods(new AppApiRequestTransfer());

        $returnArray = [];

        foreach ($responseTransfer->getPaymentMethods() as $paymentMethod) {
            $returnArray[] = $this
                ->hydratePaymentMethod(
                    $paymentMethod
                );
        }

        return $returnArray;
    }

    /**
     * @param PaymentMethodTransfer $methodTransfer
     * @return stdClass
     */
    protected function hydratePaymentMethod(PaymentMethodTransfer $methodTransfer): stdClass
    {
        $paymentMethodObject = new stdClass();

        $paymentMethodObject
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_KEY} = $methodTransfer->getCode();
        $paymentMethodObject
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_NAME} = $methodTransfer->getName();
        $paymentMethodObject
            ->{CityMerchantKeyResponseInterface::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_IMG_URL} = $this->getPaymentImgUrl($methodTransfer);

        return $paymentMethodObject;
    }

    /**
     * @param PaymentMethodTransfer $methodTransfer
     * @return string|null
     */
    protected function getPaymentImgUrl(PaymentMethodTransfer $methodTransfer): ?string
    {
        switch ($methodTransfer->getCode()) {
            case RetailPaymentConfig::PAYMENT_METHOD_CASH:
                $imageName = 'cash_on_delivery.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_EC:
                $imageName = 'ec_on_delivery.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_CREDIT_CARD:
                $imageName = 'credit_card_on_delivery.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_DIRECT_DEBIT:
                $imageName = 'direct_debit.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_WHOLESALE_CASH:
                $imageName = 'cash_on_delivery_wholesale.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_WHOLESALE_EC:
                $imageName = 'ec_on_delivery_wholesale.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_WHOLESALE_CREDIT_CARD:
                $imageName = 'credit_card_on_delivery_wholesale.png';
                break;
            case RetailPaymentConfig::PAYMENT_METHOD_INVOICE_B2B:
                $imageName = 'invoice_b2b.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_PAY_PAL_AUTHORIZE:
                $imageName = 'HeidelpayRestPayPalAuthorize.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_AUTHORIZE:
                $imageName = 'HeidelpayRestCreditCardAuthorize.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT:
                $imageName = 'HeidelpayRestSepaDirectDebit.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_B2B:
                $imageName = 'HeidelpayRestSepaDirectDebitB2B.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE:
                $imageName = 'HeidelpayRestInvoice.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_INVOICE_GUARANTEED:
                $imageName = 'HeidelpayRestInvoiceGuaranteed.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_SEPA_DIRECT_DEBIT_GUARANTEED:
                $imageName = 'HeidelpayRestSepaDirectDebitGuaranteed.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CASH_ON_DELIVERY:
                $imageName = 'HeidelpayRestCashOnDelivery.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_EC_CARD_ON_DELIVERY:
                $imageName = 'HeidelpayRestEcCardOnDelivery.png';
                break;
            case HeidelpayRestConstants::HEIDELPAY_REST_PAYMENT_METHOD_CREDIT_CARD_ON_DELIVERY:
                $imageName = 'HeidelpayRestCreditCardOnDelivery.png';
                break;
            default:
                $imageName = null;
        }

        if ($imageName === null) {
            return null;
        }

        return sprintf(
            '%s%s',
            $this
                ->config
                ->getPaymentUploadPath(),
            $imageName
        );
    }
}
