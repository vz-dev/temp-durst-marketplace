<?php
/**
 * Durst - project - SoftwarePackageCommunicationFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 26.07.18
 * Time: 14:47
 */

namespace Pyz\Zed\SoftwarePackage\Communication;

use Generated\Shared\Transfer\SoftwareFeatureTransfer;
use Generated\Shared\Transfer\SoftwarePackageTransfer;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\SoftwarePackage\Communication\Form\DataProvider\SoftwareFeatureFormDataProvider;
use Pyz\Zed\SoftwarePackage\Communication\Form\DataProvider\SoftwarePackageFormDataProvider;
use Pyz\Zed\SoftwarePackage\Communication\Form\SoftwareFeatureForm;
use Pyz\Zed\SoftwarePackage\Communication\Form\SoftwarePackageForm;
use Pyz\Zed\SoftwarePackage\Communication\Table\SoftwareFeatureTable;
use Pyz\Zed\SoftwarePackage\Communication\Table\SoftwarePackageTable;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Pyz\Zed\SoftwarePackage\SoftwarePackageDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * Class SoftwarePackageCommunicationFactory
 * @package Pyz\Zed\SoftwarePackage\Communication
 * @method SoftwarePackageQueryContainerInterface getQueryContainer()
 * @method SoftwarePackageFacadeInterface getFacade()
 */
class SoftwarePackageCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return SoftwarePackageTable
     */
    public function createSoftwarePackageTable() : SoftwarePackageTable
    {
        return new SoftwarePackageTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return SoftwareFeatureTable
     */
    public function createSoftwareFeatureTable() : SoftwareFeatureTable
    {
        return new SoftwareFeatureTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @param SoftwarePackageTransfer $data
     * @param array $options
     * @return FormInterface
     */
    public function createSoftwarePackageForm(
        SoftwarePackageTransfer $data,
        array $options
    ) : FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(SoftwarePackageForm::class, $data, $options);
    }

    /**
     * @return SoftwarePackageFormDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createSoftwarePackageFormDataProvider() : SoftwarePackageFormDataProvider
    {
        return new SoftwarePackageFormDataProvider(
            $this->getFacade(),
            $this->getQueryContainer(),
            $this->getMerchantQueryContainer()
        );
    }

    /**
     * @param SoftwareFeatureTransfer $data
     * @param array $options
     * @return FormInterface
     */
    public function createSoftwareFeatureForm(SoftwareFeatureTransfer $data, array $options) : FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(SoftwareFeatureForm::class, $data, $options);
    }

    /**
     * @return SoftwareFeatureFormDataProvider
     */
    public function createSoftwareFeatureFormDataProvider() : SoftwareFeatureFormDataProvider
    {
        return new SoftwareFeatureFormDataProvider(
            $this->getFacade(),
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantQueryContainer(): MerchantQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(SoftwarePackageDependencyProvider::QUERY_CONTAINER_MERCHANT);
    }
}
