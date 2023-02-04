<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 28.02.18
 * Time: 10:18
 */

namespace Pyz\Yves\AppRestApi\Handler;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\DummyPaymentTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Client\Cart\CartClientInterface;
use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Exception\JsonMalformedException;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Client\Checkout\CheckoutClientInterface;
use Spryker\Client\Price\PriceClientInterface;
use Spryker\Client\Shipment\ShipmentClientInterface;
use Spryker\Shared\Shipment\ShipmentConstants;


class OrderRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;

    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var HydratorInterface[]
     */
    protected $hydrators = [];

    /**
     * OrderRequestHandler constructor.
     * @param AppRestApiConfig $config
     * @param HydratorInterface[] $hydrators
     */
    public function __construct(AppRestApiConfig $config, array $hydrators)
    {
        $this->config = $config;
        $this->hydrators = $hydrators;
    }

    /**
     * @param string $json
     * @return \stdClass
     */
    public function handleJson(string $json, string $version = 'v1') : \stdClass
    {
        $requestObject = json_decode($json);

        $this->validate($requestObject, $this->config->getOrderRequestSchemaPath());
        if($this->isValid !== true){
            return $this->errors;
        }

        $responseObject = $this->createStdClass();

        $this->hydrate($requestObject, $responseObject);

        $this->validate($responseObject, $this->config->getOrderResponseSchemaPath());
        if($this->isValid !== true){
            return $this->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return mixed|void
     */
    public function hydrate(\stdClass $requestObject, \stdClass $responseObject, string $version = 'v1')
    {
        foreach ($this->hydrators as $hydrator) {
            $hydrator->hydrate($requestObject, $responseObject, $version);
        }
    }
}
