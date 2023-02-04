<?php
/**
 * Durst - project - PaymentProviderHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 16:19
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Branch;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Shared\RetailPayment\RetailPaymentConfig;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\BranchKeyResponseInterface as Response;
use stdClass;

class PaymentProviderHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\AppRestApi\AppRestApiClientInterface
     */
    protected $client;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * PaymentProviderHydrator constructor.
     *
     * @param \Pyz\Client\AppRestApi\AppRestApiClientInterface $client
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     */
    public function __construct(
        AppRestApiClientInterface $client,
        AppRestApiConfig $config
    ) {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return mixed|void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        if ($responseObject->{Response::KEY_ZIP_CODE_MERCHANTS_FOUND} !== true) {
            return;
        }
        $responseObject->{Response::KEY_PAYMENT_PROVIDER} = $this->hydratePaymentProvider();
    }

    /**
     * @return \stdClass[]
     */
    protected function hydratePaymentProvider(): array
    {
        $returnArray = [];

        $paymentProvider = new stdClass();

        $paymentProvider->{Response::KEY_PAYMENT_PROVIDER_NAME} = RetailPaymentConfig::PROVIDER_NAME;
        $paymentProvider->{Response::KEY_PAYMENT_PROVIDER_SEPA_MANDATE_URL} = $this->config->getSepaMandateUrl();
        $paymentProvider->{Response::KEY_MERCHANTS_PAYMENT_METHODS} = $this->hydratePaymentMethods();

        $returnArray[] = $paymentProvider;

        return $returnArray;
    }

    /**
     * @return \stdClass[]
     */
    protected function hydratePaymentMethods(): array
    {
        $responseTransfer = $this
            ->client
            ->getPaymentMethods(new AppApiRequestTransfer());

        $returnArray = [];
        foreach ($responseTransfer->getPaymentMethods() as $paymentMethod) {
            $returnArray[] = $this->hydratePaymentMethod($paymentMethod);
        }

        return $returnArray;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $methodTransfer
     *
     * @return \stdClass
     */
    protected function hydratePaymentMethod(PaymentMethodTransfer $methodTransfer): stdClass
    {
        $paymentMethodObject = new stdClass();
        $paymentMethodObject->{Response::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_KEY} = $methodTransfer->getCode();
        $paymentMethodObject->{Response::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_NAME} = $methodTransfer->getName();
        $paymentMethodObject->{Response::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_IMG_URL} = '';
        $paymentMethodObject->{Response::KEY_PAYMENT_PROVIDER_PAYMENT_METHOD_SHOW_DEBIT_SCREEN} = false;
        $paymentMethodObject->schow_debit_screen = false; // this should be removed eventually only temporary because ios app expects typo...

        return $paymentMethodObject;
    }
}
