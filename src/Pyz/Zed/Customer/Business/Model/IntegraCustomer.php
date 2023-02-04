<?php
/**
 * Durst - project - IntegraCustomer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.11.20
 * Time: 16:46
 */

namespace Pyz\Zed\Customer\Business\Model;


use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SoapRequestTransfer;
use Generated\Shared\Transfer\SoapResponseTransfer;
use Pyz\Service\SoapRequest\SoapRequestServiceInterface;
use Pyz\Shared\Integra\IntegraConstants;
use Pyz\Zed\Customer\Dependency\Facade\CustomerToSoapRequestInterface;
use Pyz\Zed\Integra\Business\Exception\ConnectionException;

class IntegraCustomer implements IntegraCustomerInterface
{
    protected const INTEGRA_WSDL_REQUEST_LOGIN_SYNC_FUNCTION = 'RequestLoginSync';
    protected const INTEGRA_WSDL_REQUEST_QUERY_FUNCTION = 'RequestQuery';
    protected const INTEGRA_WSDL_CREATE_KUNDE_FUNCTION = 'CreateKunde';

    protected const INTEGRA_REQUEST_QUERY_QID = 200000;
    protected const INTEGRA_REQUEST_QUERY_NAME_VALUE = 'PARAM1';
    protected const INTEGRA_REQUEST_QUERY_OFFSET = 0;
    protected const INTEGRA_REQUEST_QUERY_COUNT = 1;
    protected const INTEGRA_REQUEST_QUERY_RTF_CONVERT = 'NONE';

    protected const INTEGRA_CREATE_KUNDE_WHRG = 'EUR';
    protected const INTEGRA_CREATE_KUNDE_SPRACHE = 1;
    protected const INTEGRA_CREATE_KUNDE_STEUERSCHL = 1;
    protected const INTEGRA_CREATE_KUNDE_KDGRP = '08';
    protected const INTEGRA_CREATE_KUNDE_STATUS = '2';
    protected const INTEGRA_CREATE_KUNDE_KZTEILLIEF = '1';
    protected const INTEGRA_CREATE_KUNDE_NRKDART = '2';
    protected const INTEGRA_CREATE_KUNDE_PREISGRP = '05';
    protected const INTEGRA_CREATE_KUNDE_ABDEB = '069999';
    protected const INTEGRA_CREATE_KUNDE_ZAHLUNGSKONDITION = '01';
    protected const INTEGRA_CREATE_KUNDE_ZAHLUNGSART = 6;
    protected const INTEGRA_CREATE_KUNDE_NRKDSCHEME = '20*';

    protected const RESPONSE_REQUEST_LOGIN_SYNC_AUTHTOKEN = 'AuthToken';
    protected const RESPONSE_REQUEST_LOGIN_SYNC_SESSIONID = 'SessionID';

    protected const RESPONSE_REQUEST_QUERY_COLUMNDESCRIPTION_KEY = 'ColumnDescription';
    protected const RESPONSE_REQUEST_QUERY_CELLS_KEY = 'Cells';
    protected const RESPONSE_REQUEST_QUERY_NRKD_KEY = 'NRKD';
    protected const RESPONSE_REQUEST_QUERY_ROWS_KEY = 'Rows';

    protected const RESPONSE_CREATE_KUNDE_NRKD = 'NrKd';

    protected const WSDL_PROTOCOL = 'http://';

    protected const SALUTATION_ANREDE_TRANSLATION_MAP = [
        'Mr' => 'Herr',
        'Mrs' => 'Frau',
    ];

    /**
     * @var SoapRequestServiceInterface
     */
    protected $serviceSoapRequest;

    /**
     * @var CustomerToSoapRequestInterface
     */
    protected $soapRequestFacade;

    /**
     * @var string
     */
    protected $sessionId = '';

    /**
     * IntegraCustomer constructor.
     * @param SoapRequestServiceInterface $serviceSoapRequest
     * @param CustomerToSoapRequestInterface $soapRequestFacade
     */
    public function __construct(
        SoapRequestServiceInterface $serviceSoapRequest,
        CustomerToSoapRequestInterface $soapRequestFacade
    )
    {
        $this->serviceSoapRequest = $serviceSoapRequest;
        $this->soapRequestFacade = $soapRequestFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string|null
     */
    public function getIntegraCustomerId(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): ?string
    {
        $this->sessionId = $this
            ->login(
                $credentialsTransfer
            );

        $existingCustomer = $this
            ->findExistingIntegraCustomer(
                $quoteTransfer,
                $credentialsTransfer
            );

        if ($existingCustomer === null) {
            $existingCustomer = $this
                ->createIntegraCustomer(
                    $quoteTransfer,
                    $credentialsTransfer
                );
        }

        return $existingCustomer;
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string
     */
    protected function login(IntegraCredentialsTransfer $credentialsTransfer): string
    {
        $soapRequest = $this
            ->createSoapRequestTransfer(
                IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_SESSION_MANAGER_KEY,
                $credentialsTransfer
            )
            ->setFunction(
                static::INTEGRA_WSDL_REQUEST_LOGIN_SYNC_FUNCTION
            )
            ->setArgs(
                $this
                    ->getLoginParamsArray(
                        $credentialsTransfer
                    )
            );

        $response = $this
            ->serviceSoapRequest
            ->sendRequest(
                $soapRequest
            );

        $this
            ->logSoapRequest(
                $soapRequest,
                $response
            );

        return $this
            ->handleResponse(
                $response
            )[static::RESPONSE_REQUEST_LOGIN_SYNC_AUTHTOKEN][static::RESPONSE_REQUEST_LOGIN_SYNC_SESSIONID];
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string|null
     */
    protected function findExistingIntegraCustomer(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): ?string
    {
        $soapRequest = $this
            ->createSoapRequestTransfer(
                IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY,
                $credentialsTransfer
            )
            ->setFunction(
                static::INTEGRA_WSDL_REQUEST_QUERY_FUNCTION
            )
            ->setArgs(
                $this
                    ->getRequestQueryParamsArray(
                        $quoteTransfer
                    )
            );

        $response = $this
            ->serviceSoapRequest
            ->sendRequest(
                $soapRequest
            );

        $this
            ->logSoapRequest(
                $soapRequest,
                $response
            );

        return $this
            ->findExistingCustomerId(
                $response
            );
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return string|null
     */
    protected function createIntegraCustomer(
        QuoteTransfer $quoteTransfer,
        IntegraCredentialsTransfer $credentialsTransfer
    ): ?string
    {
        $soapRequest = $this
            ->createSoapRequestTransfer(
                IntegraConstants::INTEGRA_ORGASOFT_WEBSERVICE_WWS_KEY,
                $credentialsTransfer
            )
            ->setFunction(
                static::INTEGRA_WSDL_CREATE_KUNDE_FUNCTION
            )
            ->setArgs(
                $this
                    ->getCreateKundeParamsArray(
                        $quoteTransfer
                    )
            );

        $response = $this
            ->serviceSoapRequest
            ->sendRequest(
                $soapRequest
            );

        $this
            ->logSoapRequest(
                $soapRequest,
                $response
            );

        return $this
            ->getCreatedCustomerId(
                $response
            );
    }

    /**
     * @param string $service
     * @param IntegraCredentialsTransfer $credentials
     * @return SoapRequestTransfer
     */
    protected function createSoapRequestTransfer(
        string $service,
        IntegraCredentialsTransfer $credentials
    ): SoapRequestTransfer
    {
        return (new SoapRequestTransfer())
            ->setService(
                $service
            )
            ->setWsdlUrl(
                $this
                    ->getWsdlUrl(
                        $service,
                        $credentials
                    )
            );
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return AddressTransfer
     */
    protected function getCustomerFromOrderTransfer(QuoteTransfer $quoteTransfer): AddressTransfer
    {
        return $quoteTransfer
            ->getShippingAddress();
    }

    /**
     * @param IntegraCredentialsTransfer $credentialsTransfer
     * @return array
     */
    protected function getLoginParamsArray(IntegraCredentialsTransfer $credentialsTransfer): array
    {
        return [
            'User' => $credentialsTransfer->getSoapAuthUser(),
            'Password' => $credentialsTransfer->getSoapAuthPassword(),
            'Mandant' => $credentialsTransfer->getSoapAuthMandant(),
            'BetrStr' => $credentialsTransfer->getSoapAuthBetrStr()
        ];
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return array
     */
    protected function getCreateKundeParamsArray(QuoteTransfer $quoteTransfer): array
    {
        return [
            'Token' => [
                'SessionID' => $this->sessionId
            ],
            'StammdatenKunde' => [
                'Anrede' => $this->getTranslatedSalutation($quoteTransfer->getShippingAddress()->getSalutation()),
                'Name1' => sprintf(
                    '%s %s',
                    $quoteTransfer->getShippingAddress()->getFirstName(),
                    $quoteTransfer->getShippingAddress()->getLastName()
                ),
                'StrasseNr' => $quoteTransfer->getShippingAddress()->getAddress1(),
                'Plz' => $quoteTransfer->getShippingAddress()->getZipCode(),
                'Ort' => $quoteTransfer->getShippingAddress()->getCity(),
                'Vorname' => $quoteTransfer->getShippingAddress()->getFirstName(),
                'Familienname' => $quoteTransfer->getShippingAddress()->getLastName(),
                'Adresszusatz' => sprintf(
                    '%s %s',
                    $quoteTransfer->getShippingAddress()->getAddress2(),
                    $quoteTransfer->getShippingAddress()->getAddress3()
                ),
                'Telefon' => $quoteTransfer->getShippingAddress()->getPhone(),
                'Mail' => $quoteTransfer->getCustomer()->getEmail(),
                'Whrg' => static::INTEGRA_CREATE_KUNDE_WHRG,
                'Sprache' => static::INTEGRA_CREATE_KUNDE_SPRACHE,
                'SteuerSchl' => static::INTEGRA_CREATE_KUNDE_STEUERSCHL,
                'KdGrp' => static::INTEGRA_CREATE_KUNDE_KDGRP,
                'Status' => static::INTEGRA_CREATE_KUNDE_STATUS,
                'KZTeilLief' => static::INTEGRA_CREATE_KUNDE_KZTEILLIEF,
                'NrKdArt' => static::INTEGRA_CREATE_KUNDE_NRKDART,
                'PreisGrp' => static::INTEGRA_CREATE_KUNDE_PREISGRP,
                'AbDeb' => static::INTEGRA_CREATE_KUNDE_ABDEB,
                'ZahlungsKondition' => static::INTEGRA_CREATE_KUNDE_ZAHLUNGSKONDITION,
                'ZahlungsArt' => static::INTEGRA_CREATE_KUNDE_ZAHLUNGSART
            ],
            'NrKdScheme' => static::INTEGRA_CREATE_KUNDE_NRKDSCHEME
        ];
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return array
     */
    protected function getRequestQueryParamsArray(QuoteTransfer $quoteTransfer): array
    {
        return [
            'Token' => [
                'SessionID' => $this->sessionId
            ],
            'QueryRequest' => [
                'QID' => static::INTEGRA_REQUEST_QUERY_QID,
                'Parameters' => [
                    'NameValue' => [
                        'Name' => static::INTEGRA_REQUEST_QUERY_NAME_VALUE,
                        'Value' => $quoteTransfer->getCustomer()->getEmail()
                    ]
                ],
                'Offset' => static::INTEGRA_REQUEST_QUERY_OFFSET,
                'Count' => static::INTEGRA_REQUEST_QUERY_COUNT,
                'RTFConvert' => static::INTEGRA_REQUEST_QUERY_RTF_CONVERT
            ]
        ];
    }

    /**
     * @param SoapResponseTransfer $responseTransfer
     * @return string|null
     */
    protected function findExistingCustomerId(SoapResponseTransfer $responseTransfer): ?string
    {
        $columnDescriptions = $this
            ->handleResponse(
                $responseTransfer
            )[static::RESPONSE_REQUEST_QUERY_COLUMNDESCRIPTION_KEY][static::RESPONSE_REQUEST_QUERY_CELLS_KEY];

        $indexCustomerId = array_search(
            static::RESPONSE_REQUEST_QUERY_NRKD_KEY,
            $columnDescriptions
        );

        $customerId = null;

        if ($indexCustomerId !== false) {
            $cellResults = $this
                ->handleResponse(
                    $responseTransfer
                )[static::RESPONSE_REQUEST_QUERY_ROWS_KEY];

            if (
                is_array($cellResults) === true &&
                count($cellResults) > 0
            ) {
                $firstCellResult = reset($cellResults);

                if (isset($firstCellResult[static::RESPONSE_REQUEST_QUERY_CELLS_KEY][$indexCustomerId]) === true) {
                    $customerId = $firstCellResult[static::RESPONSE_REQUEST_QUERY_CELLS_KEY][$indexCustomerId];
                }
            }
        }

        return $customerId;
    }

    /**
     * @param SoapResponseTransfer $responseTransfer
     * @return string|null
     */
    protected function getCreatedCustomerId(SoapResponseTransfer $responseTransfer): ?string
    {
        $customer = $this
            ->handleResponse(
                $responseTransfer
            );

        if (isset($customer[static::RESPONSE_CREATE_KUNDE_NRKD]) === true) {
            return $customer[static::RESPONSE_CREATE_KUNDE_NRKD];
        }

        return null;
    }

    /**
     * @param string $service
     * @param IntegraCredentialsTransfer $credentials
     * @return string
     */
    protected function getWsdlUrl(
        string $service,
        IntegraCredentialsTransfer $credentials
    ): string
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
     * @param SoapResponseTransfer $response
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

        return $response
            ->getData();
    }

    /**
     * @param SoapRequestTransfer $requestTransfer
     * @param SoapResponseTransfer $responseTransfer
     * @return void
     */
    protected function logSoapRequest(
        SoapRequestTransfer $requestTransfer,
        SoapResponseTransfer $responseTransfer
    ): void
    {
        $this
            ->soapRequestFacade
            ->createSoapRequestLogEntry(
                $requestTransfer,
                $responseTransfer
            );
    }

    /**
     * @param string $salutation
     * @return string
     */
    protected function getTranslatedSalutation(string $salutation) : string
    {
        if(in_array($salutation, self::SALUTATION_ANREDE_TRANSLATION_MAP) === true){
            return self::SALUTATION_ANREDE_TRANSLATION_MAP[$salutation];
        }

        return '';
    }
}
