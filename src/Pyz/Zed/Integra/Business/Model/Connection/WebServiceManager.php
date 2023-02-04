<?php
/**
 * Durst - project - WebServiceManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.11.20
 * Time: 10:07
 */

namespace Pyz\Zed\Integra\Business\Model\Connection;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;
use Pyz\Service\SoapRequest\SoapRequestServiceInterface;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Zed\Integra\Business\Exception\ConnectionException;

class WebServiceManager implements WebServiceManagerInterface
{
    protected const FUNCTION_NAME_REQUEST_QUERY = 'RequestQuery';
    protected const FUNCTION_NAME_EXECUTE_QUERY = 'ExecuteQuery';
    protected const FUNCTION_NAME_LOGIN_SYNC = 'RequestLoginSync';
    protected const FUNCTION_NAME_CLOSE_LOGIN = 'CloseLogin';
    protected const FUNCTION_NAME_GET_KUNDE = 'GetKunde';

    protected const ARG_QUERY_ID_TOURS = 200200;
    protected const ARG_QUERY_ID_EXCHANGE_TABLE = 200205;
    protected const ARG_QUERY_DELIVERY_TIME_CUSTOMER = 200250;
    protected const ARG_QUERY_ID_PRODUCT_TO_DEPOSIT = 200610;
    protected const ARG_QUERY_ID_MAIN_UNITS_TO_SUB_UNITS = 200650;

    protected const ARG_QUERY_LIMIT = 100;
    protected const ARG_QUERY_LIMIT_HIGH = 10000;
    protected const ARG_RTF_CONVERT = 'NONE';

    protected const HTTP_OK = 200;
    protected const WSDL_PROTOCOL = 'http://';

    /**
     * @var SoapRequestServiceInterface
     */
    protected $soapRequestService;

    /**
     * WebServiceManager constructor.
     *
     * @param SoapRequestServiceInterface $soapRequestService
     */
    public function __construct(SoapRequestServiceInterface $soapRequestService)
    {
        $this->soapRequestService = $soapRequestService;
    }

    /**
     * {@inheritDoc}
     *
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return array
     */
    public function importTours(IntegraCredentialsTransfer $credentialsTransfer): array
    {
        $sessionId = $this->login($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createTourRequestTransfer($sessionId, $credentialsTransfer));

        return $this->handleResponse($response);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param string $idCustomer
     *
     * @return array
     */
    public function getCustomer(IntegraCredentialsTransfer $credentialsTransfer, string $idCustomer): array
    {
        $sessionId = $this->login($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createGetCustomerRequestTransfer($sessionId, $idCustomer, $credentialsTransfer));

        return $this->handleResponse($response);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return string
     */
    public function login(IntegraCredentialsTransfer $credentialsTransfer): string
    {
        $this->checkRequirements($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createLoginRequestTransfer($credentialsTransfer));

        return $this->handleResponse($response)['AuthToken']['SessionID'];
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param string $sessionId
     *
     * @return void
     */
    public function closeLogin(IntegraCredentialsTransfer $credentialsTransfer, string $sessionId): void
    {
        $this->checkRequirements($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createCloseLoginRequestTransfer($sessionId, $credentialsTransfer));

        $this->handleResponse($response);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param int $nrTourDelivery
     *
     * @return array
     */
    public function setImportedStatusForNrTourDelivery(IntegraCredentialsTransfer $credentialsTransfer, int $nrTourDelivery): array
    {
        $sessionId = $this->login($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createImportedStatusForNrTourDeliveryTransfer($sessionId, $nrTourDelivery, $credentialsTransfer));

        return $this->handleResponse($response);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @param string $nrKunde
     * @param string $deliveryStart
     * @param string $deliveryEnd
     * @param int $dayOfWeek
     * @return array
     */
    public function addDeliveryTimesToCustomer(IntegraCredentialsTransfer $credentialsTransfer, string $nrKunde, string $deliveryStart, string $deliveryEnd, int $dayOfWeek): array
    {
        $sessionId = $this->login($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest(
                $this
                    ->createDeliveryTimeCustomerUpdateTransfer(
                        $sessionId,
                        $nrKunde,
                        $deliveryStart,
                        $deliveryEnd,
                        $dayOfWeek,
                        $credentialsTransfer)
            );

        return $this->handleResponse($response);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return array
     */
    public function getProductToDeposit(IntegraCredentialsTransfer $credentialsTransfer): array
    {
        $sessionId = $this->login($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createProductToDepositRequestTransfer($sessionId, $credentialsTransfer));

        return $this->handleResponse($response);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return array
     */
    public function getProductMainUnitToSubUnit(IntegraCredentialsTransfer $credentialsTransfer): array
    {
        $sessionId = $this->login($credentialsTransfer);
        $response = $this
            ->soapRequestService
            ->sendRequest($this->createProductUnitToSubUnitRequestTransfer($sessionId, $credentialsTransfer));

        return $this->handleResponse($response);
    }

    /**
     * @param SoapResponseTransfer $response
     *
     * @return array
     */
    protected function handleResponse(SoapResponseTransfer $response): array
    {
        if ($response->getError() !== null) {
            throw ConnectionException::responseError(
                $response->getError()->getMessage(),
                $response->getError()->getCode()
            );
        }

        return $response->getData();
    }

    /**
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return void
     */
    protected function checkRequirements(IntegraCredentialsTransfer $credentials): void
    {
        $missingCred = [];
        if ($credentials->getIntegraIpAddress() === null) {
            $missingCred[] = 'host';
        }
        if ($credentials->getSoapAuthUser() === null) {
            $missingCred[] = 'user';
        }
        if ($credentials->getSoapAuthPassword() === null) {
            $missingCred[] = 'password';
        }
        if ($credentials->getSoapAuthMandant() === null) {
            $missingCred[] = 'mandant';
        }
        if ($credentials->getSoapAuthBetrStr() === null) {
            $missingCred[] = 'betrStr';
        }

        if (count($missingCred) > 0) {
            throw ConnectionException::missingCredentials($missingCred);
        }
    }

    /**
     * @param string $sessionId
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return SoapRequestTransfer
     */
    protected function createTourRequestTransfer(string $sessionId, IntegraCredentialsTransfer $credentials): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY, $credentials))
            ->setFunction(static::FUNCTION_NAME_REQUEST_QUERY)
            ->setArgs([
                'Token' => [
                    'SessionID' => $sessionId,
                ],
                'QueryRequest' => [
                    'QID' => static::ARG_QUERY_ID_TOURS,
                    'Parameters' => [
                    ],
                    'Offset' => 0,
                    'Count' => static::ARG_QUERY_LIMIT_HIGH,
                    'RTFConvert' => static::ARG_RTF_CONVERT,
                ],
            ]);
    }

    /**
     * @param string $sessionId
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return SoapRequestTransfer
     */
    protected function createCloseLoginRequestTransfer(
        string $sessionId,
        IntegraCredentialsTransfer $credentialsTransfer
    ): SoapRequestTransfer {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY, $credentialsTransfer))
            ->setFunction(static::FUNCTION_NAME_CLOSE_LOGIN)
            ->setArgs([
               'Token' => [
                   'SessionID' => $sessionId,
               ],
            ]);
    }

    /**
     * @param string $sessionId
     * @param string $idCustomer
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return SoapRequestTransfer
     */
    protected function createGetCustomerRequestTransfer(string $sessionId, string $idCustomer, IntegraCredentialsTransfer $credentials): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY, $credentials))
            ->setFunction(static::FUNCTION_NAME_GET_KUNDE)
            ->setArgs([
                'Token' => [
                    'SessionID' => $sessionId,
                ],
                'NrKd' => $idCustomer,
            ]);
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     *
     * @return SoapRequestTransfer
     */
    protected function createLoginRequestTransfer(IntegraCredentialsTransfer $credentialsTransfer): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY, $credentialsTransfer))
            ->setFunction(static::FUNCTION_NAME_LOGIN_SYNC)
            ->setArgs([
                'User' => $credentialsTransfer->getSoapAuthUser(),
                'Password' => $credentialsTransfer->getSoapAuthPassword(),
                'Mandant' => $credentialsTransfer->getSoapAuthMandant(),
                'BetrStr' => $credentialsTransfer->getSoapAuthBetrStr(),
            ]);
    }

    /**
     * @param string $service
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return string
     */
    protected function getWsdlUrl(string $service, IntegraCredentialsTransfer $credentials): string
    {
        if (array_key_exists($service, IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICES) !== true) {
            throw ConnectionException::unkownService($service);
        }

        return sprintf(
            '%s%s%s',
            static::WSDL_PROTOCOL,
            $credentials->getIntegraIpAddress(),
            IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICES[$service]
        );
    }

    /**
     * @param string $sessionId
     * @param int $nrTourDelivery
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return SoapRequestTransfer setImportedStatusForNrTourDelivery
     */
    protected function createImportedStatusForNrTourDeliveryTransfer(string $sessionId, int $nrTourDelivery, IntegraCredentialsTransfer $credentials): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY, $credentials))
            ->setFunction(static::FUNCTION_NAME_EXECUTE_QUERY)
            ->setArgs([
                'Token' => [
                    'SessionID' => $sessionId,
                ],
                'QueryRequest' => [
                    'QID' => static::ARG_QUERY_ID_EXCHANGE_TABLE,
                    'Parameters' => [
                        'NameValue' => [
                            'Name' => 'NrTourLief',
                            'Value' => $nrTourDelivery,
                        ],
                    ],
                    'Offset' => 0,
                    'Count' => static::ARG_QUERY_LIMIT,
                    'RTFConvert' => static::ARG_RTF_CONVERT,
                ],
            ]);
    }

    /**
     * @param string $sessionId
     * @param string $nrKunde
     * @param string $deliveryStart
     * @param string $deliveryEnd
     * @param int $dayOfWeek
     * @param IntegraCredentialsTransfer $credentials
     * @return SoapRequestTransfer
     */
    protected function createDeliveryTimeCustomerUpdateTransfer(string $sessionId, string $nrKunde, string $deliveryStart, string $deliveryEnd, int $dayOfWeek, IntegraCredentialsTransfer $credentials): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY, $credentials))
            ->setFunction(static::FUNCTION_NAME_REQUEST_QUERY)
            ->setArgs([
                'Token' => [
                    'SessionID' => $sessionId,
                ],
                'QueryRequest' => [
                    'QID' => static::ARG_QUERY_DELIVERY_TIME_CUSTOMER,
                    'Parameters' => [
                        'NameValue' => [
                            'Name' => 'Kundennummer',
                            'Value' => $nrKunde,
                        ],
                        'NameValue' => [
                            'Name' => 'VonUhr1',
                            'Value' => $deliveryStart,
                        ],
                        'NameValue' => [
                            'Name' => 'BisUhr1',
                            'Value' => $deliveryEnd,
                        ],
                        'NameValue' => [
                            'Name' => 'NrTag',
                            'Value' => $dayOfWeek,
                        ]
                    ],
                    'Offset' => 0,
                    'Count' => static::ARG_QUERY_LIMIT,
                    'RTFConvert' => static::ARG_RTF_CONVERT,
                ],
            ]);
    }

    /**
     * @param string $sessionId
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return SoapRequestTransfer
     */
    protected function createProductToDepositRequestTransfer(string $sessionId, IntegraCredentialsTransfer $credentials): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY, $credentials))
            ->setFunction(static::FUNCTION_NAME_REQUEST_QUERY)
            ->setArgs([
                'Token' => [
                    'SessionID' => $sessionId,
                ],
                'QueryRequest' => [
                    'QID' => static::ARG_QUERY_ID_PRODUCT_TO_DEPOSIT,
                    'Parameters' => [
                    ],
                    'Offset' => 0,
                    'Count' => static::ARG_QUERY_LIMIT_HIGH,
                    'RTFConvert' => static::ARG_RTF_CONVERT,
                ],
            ]);
    }

    /**
     * @param string $sessionId
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return SoapRequestTransfer
     */
    protected function createProductUnitToSubUnitRequestTransfer(string $sessionId, IntegraCredentialsTransfer $credentials): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY)
            ->setWsdlUrl($this->getWsdlUrl(IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY, $credentials))
            ->setFunction(static::FUNCTION_NAME_REQUEST_QUERY)
            ->setArgs([
                'Token' => [
                    'SessionID' => $sessionId,
                ],
                'QueryRequest' => [
                    'QID' => static::ARG_QUERY_ID_MAIN_UNITS_TO_SUB_UNITS,
                    'Parameters' => [
                    ],
                    'Offset' => 0,
                    'Count' => 35000,
                    'RTFConvert' => static::ARG_RTF_CONVERT,
                ],
            ]);
    }
}
