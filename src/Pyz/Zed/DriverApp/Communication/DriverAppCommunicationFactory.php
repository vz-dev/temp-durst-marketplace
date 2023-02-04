<?php


namespace Pyz\Zed\DriverApp\Communication;

use Generated\Shared\Transfer\DriverAppReleaseTransfer;
use Pyz\Zed\DriverApp\Communication\Form\DataProvider\ReleaseTypeDataProvider;
use Pyz\Zed\DriverApp\Communication\Form\ReleaseType;
use Pyz\Zed\DriverApp\Communication\Table\ReleaseTable;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * Class DriverAppCommunicationFactory
 * @package Pyz\Zed\DriverApp\Communication
 * @method \Pyz\Zed\DriverApp\Persistence\DriverAppQueryContainerInterface getQueryContainer()
 * @method \Pyz\Zed\DriverApp\DriverAppConfig getConfig()
 * @method \Pyz\Zed\DriverApp\Business\DriverAppFacadeInterface getFacade()
 */
class DriverAppCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\DriverApp\Communication\Table\ReleaseTable
     */
    public function createReleaseTable(): ReleaseTable
    {
        return new ReleaseTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\DriverApp\Communication\Form\DataProvider\ReleaseTypeDataProvider
     */
    public function createReleaseTypeDataProvider(): ReleaseTypeDataProvider
    {
        return new ReleaseTypeDataProvider(
            $this->getConfig(),
            $this->getFacade()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\DriverAppReleaseTransfer $data
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createReleaseForm(DriverAppReleaseTransfer $data, array $options): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(ReleaseType::class, $data, $options);
    }
}
