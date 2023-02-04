<?php
/**
 * Durst - project - CustomersRestApiFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 14:23
 */

namespace Pyz\Glue\CustomersRestApi;

use Pyz\Glue\CustomersRestApi\Processor\Customer\CustomerWriter;
use Spryker\Glue\CustomersRestApi\CustomersRestApiFactory as SprykerCustomersRestApiFactory;
use Spryker\Glue\CustomersRestApi\Processor\Customer\CustomerWriterInterface;

class CustomersRestApiFactory extends SprykerCustomersRestApiFactory
{
    /**
     * @return \Spryker\Glue\CustomersRestApi\Processor\Customer\CustomerWriterInterface
     * @throws \Spryker\Glue\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createCustomerWriter(): CustomerWriterInterface
    {
        return new CustomerWriter(
            $this->getCustomerClient(),
            $this->createCustomerReader(),
            $this->getResourceBuilder(),
            $this->createCustomerResourceMapper(),
            $this->createRestApiError(),
            $this->createRestApiValidator(),
            $this->getCustomerPostRegisterPlugins(),
            $this->getCustomerValidators()
        );
    }

    /**
     * @return array|\Pyz\Glue\CustomersRestApi\Validator\Registration\CustomerValidationInterface[]
     * @throws \Spryker\Glue\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCustomerValidators(): array
    {
        return $this
            ->getProvidedDependency(
                CustomersRestApiDependencyProvider::VALIDATORS_CUSTOMER
            );
    }
}
