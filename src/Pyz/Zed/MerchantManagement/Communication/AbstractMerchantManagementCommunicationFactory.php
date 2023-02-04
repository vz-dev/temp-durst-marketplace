<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 26.10.17
 * Time: 11:06
 */

namespace Pyz\Zed\MerchantManagement\Communication;


use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\MerchantManagement\MerchantManagementDependencyProvider;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Pyz\Zed\TermsOfService\Persistence\TermsOfServiceQueryContainerInterface;
use Spryker\Service\UtilDateTime\UtilDateTimeServiceInterface;
use Spryker\Zed\Acl\Business\AclFacadeInterface;
use Spryker\Zed\Currency\Business\CurrencyFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Money\Business\MoneyFacadeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;

class AbstractMerchantManagementCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantFacade() : MerchantFacadeInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_MERCHANT);
    }

    /**
     * @return MerchantQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantQueryContainer() : MerchantQueryContainerInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::QUERY_CONTAINER_MERCHANT);
    }

    /**
     * @return UtilDateTimeServiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDateFormatterService() : UtilDateTimeServiceInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::SERVICE_DATE_FORMATTER);
    }

    /**
     * @return MoneyFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMoneyFacade() : MoneyFacadeInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return DeliveryAreaFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDeliveryAreaFacade() : DeliveryAreaFacadeInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_DELIVERY_AREA);
    }

    /**
     * @return DepositQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getDepositFacade() : DepositQueryContainerInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_DEPOSIT);
    }

    /**
     * @return CurrencyFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getCurrencyFacade() : CurrencyFacadeInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_CURRENCY);
    }

    /**
     * @return TermsOfServiceQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getTermsOfServiceQueryContainer() : TermsOfServiceQueryContainerInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::QUERY_CONTAINER_TERMS_OF_SERVICE);
    }

    /**
     * @return TermsOfServiceFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getTermsOfServiceFacade() : TermsOfServiceFacadeInterface
    {
        return $this->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_TERMS_OF_SERVICE);
    }

    /**
     * @return SoftwarePackageQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSoftwarePackageQueryContainer() : SoftwarePackageQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(MerchantManagementDependencyProvider::QUERY_CONTAINER_SOFTWARE_PACKAGE);
    }

    /**
     * @return StateMachineFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getStateMachineFacade() : StateMachineFacadeInterface
    {
        return $this
            ->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_STATE_MACHINE);
    }

    /**
     * @return \Spryker\Zed\Acl\Business\AclFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAclFacade(): AclFacadeInterface
    {
        return $this
            ->getProvidedDependency(MerchantManagementDependencyProvider::FACADE_ACL);
    }
}
