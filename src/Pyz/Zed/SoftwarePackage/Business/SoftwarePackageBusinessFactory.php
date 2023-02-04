<?php
/**
 * Durst - project - SoftwarePackageBusinessFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 12:35
 */

namespace Pyz\Zed\SoftwarePackage\Business;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\MerchantHydrator;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\MerchantHydratorInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\PaymentMethodHydrator;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\PaymentMethodHydratorInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\SoftwareFeatureHydrator;
use Pyz\Zed\SoftwarePackage\Business\Model\Hydrator\SoftwareFeatureHydratorInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\LicenseKey;
use Pyz\Zed\SoftwarePackage\Business\Model\LicenseKeyInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\MerchantSaver;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\MerchantSaverInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\PaymentMethodSaver;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\PaymentMethodSaverInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\SoftwareFeatureSaver;
use Pyz\Zed\SoftwarePackage\Business\Model\Saver\SoftwareFeatureSaverInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\SoftwareFeature;
use Pyz\Zed\SoftwarePackage\Business\Model\SoftwareFeatureInterface;
use Pyz\Zed\SoftwarePackage\Business\Model\SoftwarePackage;
use Pyz\Zed\SoftwarePackage\Business\Model\SoftwarePackageInterface;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Pyz\Zed\SoftwarePackage\SoftwarePackageConfig;
use Pyz\Zed\SoftwarePackage\SoftwarePackageDependencyProvider;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class SoftwarePackageBusinessFactory
 * @package Pyz\Zed\SoftwarePackage\Business
 * @method SoftwarePackageQueryContainerInterface getQueryContainer()
 * @method SoftwarePackageConfig getConfig()
 */
class SoftwarePackageBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return SoftwarePackageInterface
     */
    public function createSoftwarePackageModel() : SoftwarePackageInterface
    {
        return new SoftwarePackage(
            $this->getQueryContainer(),
            $this->createPaymentMethodHydrator(),
            $this->createPaymentMethodSaver(),
            $this->createSoftwareFeatureHydrator(),
            $this->createSoftwareFeatureSaver()
        );
    }

    /**
     * @return MerchantHydratorInterface
     */
    public function createMerchantHydrator() : MerchantHydratorInterface
    {
        return new MerchantHydrator(
            $this->createSoftwarePackageModel()
        );
    }

    /**
     * @return MerchantSaverInterface
     */
    public function createMerchantSaver() : MerchantSaverInterface
    {
        return new MerchantSaver(
            $this->getQueryContainer()
        );
    }

    /**
     * @return PaymentMethodHydratorInterface
     */
    protected function createPaymentMethodHydrator() : PaymentMethodHydratorInterface
    {
        return new PaymentMethodHydrator();
    }

    /**
     * @return PaymentMethodSaverInterface
     */
    protected function createPaymentMethodSaver() : PaymentMethodSaverInterface
    {
        return new PaymentMethodSaver(
            $this->getQueryContainer()
        );
    }

    /**
     * @return SoftwareFeatureHydratorInterface
     */
    protected function createSoftwareFeatureHydrator() : SoftwareFeatureHydratorInterface
    {
        return new SoftwareFeatureHydrator();
    }

    /**
     * @return SoftwareFeatureSaverInterface
     */
    protected function createSoftwareFeatureSaver() : SoftwareFeatureSaverInterface
    {
        return new SoftwareFeatureSaver(
            $this->getQueryContainer()
        );
    }

    /**
     * @return SoftwareFeatureInterface
     */
    public function createSoftwareFeatureModel() : SoftwareFeatureInterface
    {
         return new SoftwareFeature(
            $this->getQueryContainer()
        );
    }

    /**
     * @return LicenseKeyInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createLicenseKeyModel(): LicenseKeyInterface
    {
        return new LicenseKey(
            $this->getQueryContainer(),
            $this->getConfig(),
            $this->getMerchantFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(SoftwarePackageDependencyProvider::FACADE_MERCHANT);
    }
}
