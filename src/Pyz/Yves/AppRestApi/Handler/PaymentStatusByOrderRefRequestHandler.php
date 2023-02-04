<?php
/**
 * Durst - project - PaymentStatusByOrderRefRequestHandler.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-03-12
 * Time: 15:35
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer;
use Pyz\Client\HeidelpayRest\HeidelpayRestClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Request\PaymentStatusKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\PaymentStatusKeyResponseInterface as Response;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use stdClass;

class PaymentStatusByOrderRefRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;

    /**
     * @var \Pyz\Client\HeidelpayRest\HeidelpayRestClientInterface
     */
    protected $heidelpayRestClient;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * PaymentStatusByOrderRefRequestHandler constructor.
     *
     * @param \Pyz\Client\HeidelpayRest\HeidelpayRestClientInterface $heidelpayRestClient
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     */
    public function __construct(
        HeidelpayRestClientInterface $heidelpayRestClient,
        AppRestApiConfig $config
    ) {
        $this->heidelpayRestClient = $heidelpayRestClient;
        $this->config = $config;
    }

    /**
     * @param string $json
     *
     * @return \stdClass
     */
    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $this->validate($requestObject, $this->config->getPaymentStatusRequestSchemaPath());
        if ($this->isValid !== true) {
            return $this->errors;
        }

        $responseObject = $this->createStdClass();

        $this->hydrate($requestObject, $responseObject);

        $this->validate($responseObject, $this->config->getPaymentStatusResponseSchemaPath());
        if ($this->isValid !== true) {
            return $this->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    protected function hydrate(
        stdClass $requestObject,
        stdClass $responseObject
    ) {

        $authorizationTransfer = $this
            ->heidelpayRestClient
            ->getAuthorizationStatusBySalesOrderRef(
                $requestObject->{Request::KEY_ORDER_REF}
            );

        $this->hydrateResponse($responseObject, $authorizationTransfer);
    }

    /**
     * @param \stdClass $responseObject
     * @param \Generated\Shared\Transfer\HeidelpayRestAuthorizationTransfer $transfer
     *
     * @return void
     */
    protected function hydrateResponse(
        stdClass $responseObject,
        HeidelpayRestAuthorizationTransfer $transfer
    ): void {
        $responseObject->{Response::KEY_IS_SUCCESS} = $transfer->getIsSuccess();
        $responseObject->{Response::KEY_IS_PENDING} = $transfer->getIsPending();
        $responseObject->{Response::KEY_IS_ERROR} = $transfer->getIsError();
        $responseObject->{Response::KEY_ERROR_MESSAGE} = $transfer->getErrorMessage();
        $responseObject->{Response::KEY_RETURN_URL} = $transfer->getReturnUrl();
        $responseObject->{Response::KEY_REDIRECT_URL} = $transfer->getRedirectUrl();
        $responseObject->{Response::KEY_PAYMENT_ID} = $transfer->getPaymentId();
    }

    /**
     * @return \stdClass
     */
    protected function createStdClass(): stdClass
    {
        return new stdClass();
    }
}
