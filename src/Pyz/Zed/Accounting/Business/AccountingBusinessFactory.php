<?php
/**
 * Durst - project - AccountingBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:11
 */

namespace Pyz\Zed\Accounting\Business;


use Pyz\Zed\Accounting\AccountingConfig;
use Pyz\Zed\Accounting\AccountingDependencyProvider;
use Pyz\Zed\Accounting\Business\Mapper\RealaxExportMapper;
use Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGenerator;
use Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGeneratorInterface;
use Pyz\Zed\Accounting\Business\Model\RealaxInvoice;
use Pyz\Zed\Accounting\Business\Model\RealaxInvoiceFixed;
use Pyz\Zed\Accounting\Business\Model\RealaxInvoiceFixedInterface;
use Pyz\Zed\Accounting\Business\Model\RealaxInvoiceInterface;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToSalesBridgeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface;
use Spryker\Zed\SequenceNumber\Persistence\SequenceNumberQueryContainerInterface;

/**
 * Class AccountingBusinessFactory
 * @package Pyz\Zed\Accounting\Business
 * @method AccountingConfig getConfig()
 */
class AccountingBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Pyz\Zed\Accounting\Business\Model\RealaxInvoiceInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createRealaxInvoice(): RealaxInvoiceInterface
    {
        return new RealaxInvoice(
            $this->getLogFacade(),
            $this->getMerchantFacade(),
            $this->getSalesFacade(),
            $this->createLicenseInvoiceReferenceGenerator(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Accounting\Business\Model\RealaxInvoiceFixedInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createRealaxInvoiceFixed(): RealaxInvoiceFixedInterface
    {
        return new RealaxInvoiceFixed(
            $this->getLogFacade(),
            $this->getMerchantFacade(),
            $this->createLicenseInvoiceFixedReferenceGenerator(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGeneratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createLicenseInvoiceReferenceGenerator(): LicenseInvoiceReferenceGeneratorInterface
    {
        $sequenceNumberSettings = $this
            ->getConfig()
            ->getLicenseInvoiceReferenceDefaults();

        return new LicenseInvoiceReferenceGenerator(
            $this->getSequenceFacade(),
            $sequenceNumberSettings,
            $this->getSequenceNumberQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Accounting\Business\Model\LicenseInvoiceReferenceGeneratorInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createLicenseInvoiceFixedReferenceGenerator(): LicenseInvoiceReferenceGeneratorInterface
    {
        $sequenceNumberSettings = $this
            ->getConfig()
            ->getLicenseInvoiceFixedReferenceDefaults();

        return new LicenseInvoiceReferenceGenerator(
            $this->getSequenceFacade(),
            $sequenceNumberSettings,
            $this->getSequenceNumberQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Accounting\Business\Mapper\RealaxExportMapper
     */
    public function createRealaxExportMapper(): RealaxExportMapper
    {
        return new RealaxExportMapper();
    }

    /**
     * @return \Pyz\Zed\Accounting\Dependency\Facade\AccountingToLogBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getLogFacade(): AccountingToLogBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                AccountingDependencyProvider::FACADE_LOG
            );
    }

    /**
     * @return \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMerchantBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantFacade(): AccountingToMerchantBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                AccountingDependencyProvider::FACADE_MERCHANT
            );
    }

    /**
     * @return \Pyz\Zed\Accounting\Dependency\Facade\AccountingToSalesBridgeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSalesFacade(): AccountingToSalesBridgeInterface
    {
        return $this
            ->getProvidedDependency(
                AccountingDependencyProvider::FACADE_SALES
            );
    }

    /**
     * @return \Spryker\Zed\SequenceNumber\Business\SequenceNumberFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSequenceFacade(): SequenceNumberFacadeInterface
    {
        return $this
            ->getProvidedDependency(
                AccountingDependencyProvider::FACADE_SEQUENCE_NUMBER
            );
    }

    /**
     * @return \Spryker\Zed\SequenceNumber\Persistence\SequenceNumberQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getSequenceNumberQueryContainer(): SequenceNumberQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(
                AccountingDependencyProvider::QUERY_CONTAINER_SEQUENCE_NUMBER
            );
    }
}
