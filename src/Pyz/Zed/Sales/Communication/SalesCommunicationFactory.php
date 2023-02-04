<?php
/**
 * Durst - project - SalesCommunicationFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 04.10.18
 * Time: 07:01
 */

namespace Pyz\Zed\Sales\Communication;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Sales\Communication\Form\AddressForm;
use Pyz\Zed\Sales\Communication\Form\DataProvider\AddressFormDataProvider;
use Pyz\Zed\Sales\Communication\Table\OrdersTable;
use Pyz\Zed\Sales\Dependency\Facade\SalesToCustomerInterface;
use Pyz\Zed\Sales\Dependency\Facade\SalesToIntegraInterface;
use Pyz\Zed\Sales\Dependency\Facade\SalesToInvoiceInterface;
use Pyz\Zed\Sales\Dependency\Facade\SalesToOmsInterface;
use Pyz\Zed\Sales\SalesDependencyProvider;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Sales\Communication\SalesCommunicationFactory as SprykerSalesCommunicationFactory;
use Symfony\Component\Form\FormInterface;

class SalesCommunicationFactory extends SprykerSalesCommunicationFactory
{
    /**
     * @return OrdersTable|\Spryker\Zed\Sales\Communication\Table\OrdersTable
     * @throws ContainerKeyNotFoundException
     */
    public function createOrdersTable()
    {
        return new OrdersTable(
            $this->createOrdersTableQueryBuilder(),
            $this->getProvidedDependency(SalesDependencyProvider::FACADE_MONEY),
            $this->getProvidedDependency(SalesDependencyProvider::SERVICE_UTIL_SANITIZE),
            $this->getProvidedDependency(SalesDependencyProvider::SERVICE_DATE_FORMATTER),
            $this->getProvidedDependency(SalesDependencyProvider::FACADE_CUSTOMER),
            $this->getSalesTablePlugins()
        );
    }

    /**
     * @return SalesToOmsInterface
     */
    public function getOmsFacade()
    {
        return $this->getProvidedDependency(SalesDependencyProvider::FACADE_OMS);
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this->getProvidedDependency(SalesDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return SalesToIntegraInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getIntegraFacade(): SalesToIntegraInterface
    {
        return $this
            ->getProvidedDependency(
                SalesDependencyProvider::FACADE_INTEGRA
            );
    }

    /**
     * @return SalesToCustomerInterface
     * @throws ContainerKeyNotFoundException
     */
    public function getCustomerFacade(): SalesToCustomerInterface
    {
        return $this
            ->getProvidedDependency(
                SalesDependencyProvider::FACADE_CUSTOMER
            );
    }

    /**
     * @param array $data
     * @param array $options
     * @return FormInterface
     */
    public function getAddressForm(array $data = [], array $options = [])
    {
        return $this
            ->getFormFactory()
            ->create(AddressForm::class, $data, $options);
    }

    /**
     * @return AddressFormDataProvider
     */
    public function createAddressFormDataProvider()
    {
        return new AddressFormDataProvider(
            $this->getQueryContainer(),
            $this->getCountryFacade()
        );
    }

    /**
     * @return SalesToInvoiceInterface
     */
    public function getInvoiceFacade()
    {
        return $this->getProvidedDependency(SalesDependencyProvider::FACADE_INVOICE);
    }
}
