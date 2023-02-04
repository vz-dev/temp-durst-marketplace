<?php
/**
 * Durst - project - CustomerBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 09.11.20
 * Time: 16:43
 */

namespace Pyz\Zed\Customer\Business;

use Pyz\Service\SoapRequest\SoapRequestServiceInterface;
use Pyz\Zed\Customer\Business\Checkout\DurstCustomerReferenceOrderSaver;
use Pyz\Zed\Customer\Business\Checkout\DurstCustomerReferenceOrderSaverInterface;
use Pyz\Zed\Customer\Business\Checkout\IntegraCustomerOrderSaver;
use Pyz\Zed\Customer\Business\Checkout\IntegraCustomerOrderSaverInterface;
use Pyz\Zed\Customer\Business\Customer\Customer;
use Pyz\Zed\Customer\Business\Model\IntegraCustomer;
use Pyz\Zed\Customer\Business\Model\IntegraCustomerInterface;
use Pyz\Zed\Customer\Business\ReferenceGenerator\DurstCustomerReferenceGenerator;
use Pyz\Zed\Customer\Business\ReferenceGenerator\DurstCustomerReferenceGeneratorInterface;
use Pyz\Zed\Customer\CustomerConfig;
use Pyz\Zed\Customer\CustomerDependencyProvider;
use Pyz\Zed\Customer\Dependency\Facade\CustomerToSoapRequestInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;
use Pyz\Zed\Sales\Persistence\SalesQueryContainerInterface;
use Spryker\Zed\Customer\Business\CustomerBusinessFactory as SprykerCustomerBusinessFactory;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;

/**
 * Class CustomerBusinessFactory
 * @package Pyz\Zed\Customer\Business
 *
 * @method CustomerConfig getConfig()
 */
class CustomerBusinessFactory extends SprykerCustomerBusinessFactory
{
    /**
     * @return IntegraCustomerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createIntegraCustomerModel(): IntegraCustomerInterface
    {
        return new IntegraCustomer(
            $this->getServiceSoapRequest(),
            $this->getSoapRequestFacade()
        );
    }

    /**
     * @return IntegraCustomerOrderSaverInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createIntegraCustomerOrderSaver(): IntegraCustomerOrderSaverInterface
    {
        return new IntegraCustomerOrderSaver(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return DurstCustomerReferenceGeneratorInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDurstCustomerReferenceGenerator(): DurstCustomerReferenceGeneratorInterface
    {
        return new DurstCustomerReferenceGenerator(
            $this->getSequenceNumberFacade(),
            $this->getConfig()->getCustomerReferenceDefaults(),
            $this->getConfig(),
            $this->getSalesFacade(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return DurstCustomerReferenceOrderSaverInterface
     * @throws ContainerKeyNotFoundException
     */
    public function createDurstCustomerReferenceOrderSaver(): DurstCustomerReferenceOrderSaverInterface
    {
        return new DurstCustomerReferenceOrderSaver(
            $this->getSalesQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Customer\Business\Customer\Customer
     */
    public function createCustomer(): Customer
    {
        return new Customer(
            $this->getQueryContainer(),
            $this->createCustomerReferenceGenerator(),
            $this->getConfig(),
            $this->createEmailValidator(),
            $this->getMailFacade(),
            $this->getLocaleQueryContainer(),
            $this->getStore(),
            $this->createCustomerExpander(),
            $this->getPostCustomerRegistrationPlugins(),
            $this->getOmsFacade()
        );
    }

    /**
     * @return SoapRequestServiceInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getServiceSoapRequest(): SoapRequestServiceInterface
    {
        return $this
            ->getProvidedDependency(
                CustomerDependencyProvider::SERVICE_SOAP_REQUEST
            );
    }

    /**
     * @return SalesQueryContainerInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSalesQueryContainer(): SalesQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                CustomerDependencyProvider::QUERY_CONTAINER_SALES
            );
    }

    /**
     * @return CustomerToSoapRequestInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSoapRequestFacade(): CustomerToSoapRequestInterface
    {
        return $this
            ->getProvidedDependency(
                CustomerDependencyProvider::FACADE_SOAP_REQUEST
            );
    }

    /**
     * @return SalesFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getSalesFacade(): SalesFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CustomerDependencyProvider::SALES_FACADE
            );
    }

    /**
     * @return MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CustomerDependencyProvider::MERCHANT_FACADE
            );
    }

    /**
     * @return \Pyz\Zed\Oms\Business\OmsFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getOmsFacade(): OmsFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                CustomerDependencyProvider::FACADE_OMS
            );
    }
}
