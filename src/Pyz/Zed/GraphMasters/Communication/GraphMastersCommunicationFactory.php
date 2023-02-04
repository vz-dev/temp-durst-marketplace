<?php

namespace Pyz\Zed\GraphMasters\Communication;

use Generated\Shared\Transfer\GraphMastersDeliveryAreaCategoryTransfer;
use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Communication\Form\CategoryForm;
use Pyz\Zed\GraphMasters\Communication\Form\CategoryFormDataProvider;
use Pyz\Zed\GraphMasters\Communication\Form\SettingsForm;
use Pyz\Zed\GraphMasters\Communication\Form\SettingsFormDataProvider;
use Pyz\Zed\GraphMasters\Communication\Table\DeliveryAreaCategoryTable;
use Pyz\Zed\GraphMasters\Communication\Table\SettingsTable;
use Pyz\Zed\GraphMasters\Communication\Table\TourTable;
use Pyz\Zed\GraphMasters\GraphMastersDependencyProvider;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainer getQueryContainer()
 * @method \Pyz\Zed\GraphMasters\GraphMastersConfig getConfig()
 * @method GraphMastersFacadeInterface getFacade()
 */
class GraphMastersCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return SettingsTable
     */
    public function createSettingsTable() : SettingsTable
    {
        return new SettingsTable($this->getQueryContainer());
    }

    /**
     * @param GraphMastersSettingsTransfer $data
     * @param array $options
     *
     * @return FormInterface
     */
    public function createSettingsForm(GraphMastersSettingsTransfer $data, array $options): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                SettingsForm::class,
                $data,
                $options
            );
    }

    /**
     * @return SettingsFormDataProvider
     */
    public function createSettingsFormDataProvider(): SettingsFormDataProvider
    {
        return new SettingsFormDataProvider(
            $this->getQueryContainer(),
            $this->getMerchantQueryContainer(),
            $this->getFacade()
        );
    }

    /**
     * @return DeliveryAreaCategoryTable
     */
    public function createDeliveryAreaCategoryTable() : DeliveryAreaCategoryTable
    {
        return new DeliveryAreaCategoryTable(
            $this->getQueryContainer(),
            $this->getFacade()
        );
    }

    /**
     * @param GraphMastersDeliveryAreaCategoryTransfer $data
     * @param array $options
     * @return FormInterface
     */
    public function createCategoryForm(GraphMastersDeliveryAreaCategoryTransfer $data, array $options): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CategoryForm::class,
                $data,
                $options
            );
    }

    /**
     * @return CategoryFormDataProvider
     */
    public function createCategoryFormDataProvider(): CategoryFormDataProvider
    {
        return new CategoryFormDataProvider(
            $this->getQueryContainer(),
            $this->getFacade(),
            $this->getMerchantQueryContainer(),
            $this->getDeliveryAreaQueryContainer()
        );
    }

    /**
     * @return TourTable
     */
    public function createTourTable(): TourTable
    {
        return new TourTable(
            $this->getQueryContainer(),
            $this->getFacade()
        );
    }

    /**
     * @return MerchantQueryContainerInterface
     */
    protected function getMerchantQueryContainer(): MerchantQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(GraphMastersDependencyProvider::QUERY_CONTAINER_MERCHANT);
    }

    /**
     * @return MerchantQueryContainerInterface
     */
    protected function getDeliveryAreaQueryContainer(): DeliveryAreaQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(GraphMastersDependencyProvider::QUERY_CONTAINER_DELIVERY_AREA);
    }
}
