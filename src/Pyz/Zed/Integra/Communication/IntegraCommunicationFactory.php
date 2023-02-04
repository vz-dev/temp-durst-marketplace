<?php

namespace Pyz\Zed\Integra\Communication;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Pyz\Zed\Integra\Communication\Form\CredentialsForm;
use Pyz\Zed\Integra\Communication\Form\CredentialsFormDataProvider;
use Pyz\Zed\Integra\Communication\Table\CredentialsTable;
use Pyz\Zed\Integra\Communication\Table\LogTable;
use Pyz\Zed\Integra\IntegraConfig;
use Pyz\Zed\Integra\IntegraDependencyProvider;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainer;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * @method IntegraQueryContainer getQueryContainer()
 * @method IntegraConfig getConfig()
 */
class IntegraCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return CredentialsTable
     */
    public function createCredentialsTable(): CredentialsTable
    {
        return new CredentialsTable(
            $this->getQueryContainer()
        );
    }

    /**
     * @return LogTable
     */
    public function createLogTable(): LogTable
    {
        return new LogTable(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }

    /**
     * @param IntegraCredentialsTransfer $data
     * @param array $options
     *
     * @return FormInterface
     */
    public function createCredentialsForm(IntegraCredentialsTransfer $data, array $options): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CredentialsForm::class,
                $data,
                $options
            );
    }

    /**
     * @return CredentialsFormDataProvider
     */
    public function createCredentialsFormDataProvider(): CredentialsFormDataProvider
    {
        return new CredentialsFormDataProvider(
            $this->getQueryContainer(),
            $this->getMerchantQueryContainer()
        );
    }

    /**
     * @return MerchantQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMerchantQueryContainer(): MerchantQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(IntegraDependencyProvider::QUERY_CONTAINER_MERCHANT);
    }
}
