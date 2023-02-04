<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 26.10.17
 * Time: 10:59
 */

namespace Pyz\Zed\MerchantManagement\Communication\Table;


use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\MerchantManagement\Communication\AbstractMerchantManagementCommunicationFactory;
use Pyz\Zed\MerchantManagement\MerchantManagementDependencyProvider;
use Pyz\Zed\Product\Persistence\ProductQueryContainerInterface;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class TableFactory extends AbstractMerchantManagementCommunicationFactory
{
    /**
     * @return DepositTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDepositTable()
    {
        return new DepositTable(
            $this->getDepositQueryContainer(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return MerchantsTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantTable()
    {
        return new MerchantsTable(
            $this->getMerchantQueryContainer(),
            $this->getDateFormatterService()
        );
    }

    /**
     * @return BranchesTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchTable()
    {
        return new BranchesTable(
            $this->getMerchantQueryContainer(),
            $this->getMoneyFacade(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return DeliveryAreasTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createDeliveryAreaTable()
    {
        return new DeliveryAreasTable(
            $this->getDeliveryAreaFacade()
        );
    }

    /**
     * @return PaymentMethodsTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createPaymentMethodsTable()
    {
        return new PaymentMethodsTable(
            $this->getMerchantQueryContainer()
        );
    }

    /**
     * @return SalutationTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createSalutationTable()
    {
        return new SalutationTable(
            $this->getMerchantQueryContainer()
        );
    }

    /**
     * @return TermsOfServiceTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createTermsOfServiceTable()
    {
        return new TermsOfServiceTable(
            $this->getTermsOfServiceQueryContainer()
        );
    }

    /**
     * @return ManufacturerTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createManufacturerTable() : ManufacturerTable
    {
        return new ManufacturerTable(
            $this->getProductQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\MerchantManagement\Communication\Table\TourTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createTourTable(): TourTable
    {
        return new TourTable(
            $this->getTourQueryContainer(),
            $this->getStateMachineFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTourQueryContainer(): TourQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                MerchantManagementDependencyProvider::QUERY_CONTAINER_TOUR
            );
    }

    /**
     * @return ProductQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getProductQueryContainer() : ProductQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(MerchantManagementDependencyProvider::QUERY_CONTAINER_PRODUCT);
    }

    /**
     * @return DepositQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDepositQueryContainer() : DepositQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(MerchantManagementDependencyProvider::QUERY_CONTAINER_DEPOSIT);
    }

    /**
     * @return \Pyz\Zed\MerchantManagement\Communication\Table\BranchUsersTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createBranchUserTable(): BranchUsersTable
    {
        return new BranchUsersTable(
            $this->getMerchantQueryContainer(),
            $this->getDateFormatterService()
        );
    }

    /**
     * @return \Pyz\Zed\MerchantManagement\Communication\Table\MerchantUsersTable
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMerchantUserTable(): MerchantUsersTable
    {
        return new MerchantUsersTable(
            $this->getMerchantQueryContainer(),
            $this->getDateFormatterService()
        );
    }
}
