<?php
/**
 * Durst - project - CustomerWriter.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 14:18
 */

namespace Pyz\Glue\CustomersRestApi\Processor\Customer;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RestCustomersAttributesTransfer;
use Spryker\Glue\CustomersRestApi\CustomersRestApiConfig;
use Spryker\Glue\CustomersRestApi\Dependency\Client\CustomersRestApiToCustomerClientInterface;
use Spryker\Glue\CustomersRestApi\Processor\Customer\CustomerReaderInterface;
use Spryker\Glue\CustomersRestApi\Processor\Customer\CustomerWriter as SprykerCustomerWriter;
use Spryker\Glue\CustomersRestApi\Processor\Mapper\CustomerResourceMapperInterface;
use Spryker\Glue\CustomersRestApi\Processor\Validation\RestApiErrorInterface;
use Spryker\Glue\CustomersRestApi\Processor\Validation\RestApiValidatorInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class CustomerWriter extends SprykerCustomerWriter
{
    /**
     * @var array|\Pyz\Glue\CustomersRestApi\Validator\Registration\CustomerValidationInterface[]
     */
    protected $validators;

    /**
     * @param \Spryker\Glue\CustomersRestApi\Dependency\Client\CustomersRestApiToCustomerClientInterface $customerClient
     * @param \Spryker\Glue\CustomersRestApi\Processor\Customer\CustomerReaderInterface $customerReader
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \Spryker\Glue\CustomersRestApi\Processor\Mapper\CustomerResourceMapperInterface $customerResourceMapper
     * @param \Spryker\Glue\CustomersRestApi\Processor\Validation\RestApiErrorInterface $restApiError
     * @param \Spryker\Glue\CustomersRestApi\Processor\Validation\RestApiValidatorInterface $restApiValidator
     * @param array|\Spryker\Glue\CustomersRestApiExtension\Dependency\Plugin\CustomerPostRegisterPluginInterface[] $customerPostRegisterPlugins
     * @param array|\Pyz\Glue\CustomersRestApi\Validator\Registration\CustomerValidationInterface[] $customerValidators
     */
    public function __construct(
        CustomersRestApiToCustomerClientInterface $customerClient,
        CustomerReaderInterface $customerReader,
        RestResourceBuilderInterface $restResourceBuilder,
        CustomerResourceMapperInterface $customerResourceMapper,
        RestApiErrorInterface $restApiError,
        RestApiValidatorInterface $restApiValidator,
        array $customerPostRegisterPlugins,
        array $customerValidators
    )
    {
        $this->validators = $customerValidators;

        parent::__construct(
            $customerClient,
            $customerReader,
            $restResourceBuilder,
            $customerResourceMapper,
            $restApiError,
            $restApiValidator,
            $customerPostRegisterPlugins
        );
    }

    /**
     * @param \Generated\Shared\Transfer\RestCustomersAttributesTransfer $restCustomersAttributesTransfer
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function registerCustomer(
        RestCustomersAttributesTransfer $restCustomersAttributesTransfer
    ): RestResponseInterface
    {
        $restResponse = $this
            ->restResourceBuilder
            ->createRestResponse();

        if (!$restCustomersAttributesTransfer->getAcceptedTerms()) {
            return $this
                ->restApiError
                ->addNotAcceptedTermsError(
                    $restResponse
                );
        }

        foreach ($this->validators as $validator) {
            $restResponse = $validator
                ->isValid(
                    $restCustomersAttributesTransfer,
                    $restResponse
                );

            if (
                is_array($restResponse->getErrors()) &&
                count($restResponse->getErrors()) > 0
            ) {
                return $restResponse;
            }
        }

        $customerTransfer = (new CustomerTransfer())
            ->fromArray(
                $restCustomersAttributesTransfer
                    ->toArray(),
                true
            );

        $customerResponseTransfer = $this
            ->customerClient
            ->registerCustomer(
                $customerTransfer
            );

        if (!$customerResponseTransfer->getIsSuccess()) {
            foreach ($customerResponseTransfer->getErrors() as $error) {
                if ($error->getMessage() === static::ERROR_MESSAGE_CUSTOMER_EMAIL_ALREADY_USED) {
                    return $this
                        ->restApiError
                        ->addCustomerAlreadyExistsError(
                            $restResponse
                        );
                }

                return $this
                    ->restApiError
                    ->addCustomerCantRegisterMessageError(
                        $restResponse,
                        $error
                            ->getMessage()
                    );
            }
        }

        $customerTransfer = $customerResponseTransfer
            ->getCustomerTransfer();

        $customerTransfer = $this
            ->executeCustomerPostRegisterPlugins(
                $customerTransfer
            );

        $restCustomersResponseAttributesTransfer = $this
            ->customerResourceMapper
            ->mapCustomerTransferToRestCustomersResponseAttributesTransfer(
                $customerTransfer
            );

        $restResource = $this
            ->restResourceBuilder
            ->createRestResource(
                CustomersRestApiConfig::RESOURCE_CUSTOMERS,
                $customerResponseTransfer->getCustomerTransfer()->getCustomerReference(),
                $restCustomersResponseAttributesTransfer
            );

        return $restResponse
            ->addResource(
                $restResource
            );
    }
}
