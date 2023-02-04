<?php
/**
 * Durst - project - CustomersRestApiDependencyProvider.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 14:25
 */

namespace Pyz\Glue\CustomersRestApi;

use Pyz\Glue\CustomersRestApi\Validator\Registration\ConfirmedPasswordEqualsPasswordValidator;
use Pyz\Glue\CustomersRestApi\Validator\Registration\FirstnameNotNullValidator;
use Pyz\Glue\CustomersRestApi\Validator\Registration\LastnameNotNullValidator;
use Pyz\Glue\CustomersRestApi\Validator\Registration\PasswordNotNullValidator;
use Pyz\Glue\CustomersRestApi\Validator\Registration\PhoneNotNullValidator;
use Spryker\Glue\CustomersRestApi\CustomersRestApiDependencyProvider as SprykerCustomersRestApiDependencyProvider;
use Spryker\Glue\Kernel\Container;

class CustomersRestApiDependencyProvider extends SprykerCustomersRestApiDependencyProvider
{
    public const VALIDATORS_CUSTOMER = 'VALIDATORS_CUSTOMER';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCustomerValidators($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCustomerValidators(Container $container): Container
    {
        $container[static::VALIDATORS_CUSTOMER] = function (Container $container) {
            return $this
                ->getCustomerValidators();
        };

        return $container;
    }

    /**
     * @return array|\Pyz\Glue\CustomersRestApi\Validator\Registration\CustomerValidationInterface[]
     */
    protected function getCustomerValidators(): array
    {
        return [
            new FirstnameNotNullValidator(),
            new LastnameNotNullValidator(),
            new PhoneNotNullValidator(),
            new PasswordNotNullValidator(),
            new ConfirmedPasswordEqualsPasswordValidator(),
        ];
    }
}
