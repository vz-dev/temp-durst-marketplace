<?php
/**
 * Durst - project - TaxCommunicationFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 25.06.20
 * Time: 09:06
 */

namespace Pyz\Zed\Tax\Communication;

use Generated\Shared\Transfer\TaxRateTransfer;
use Pyz\Zed\Tax\Communication\Form\TaxRateForm;
use Pyz\Zed\Tax\TaxDependencyProvider;
use Pyz\Zed\TaxProductConnector\Persistence\TaxProductConnectorQueryContainer;
use Spryker\Zed\Tax\Communication\Form\DataProvider\TaxRateFormDataProvider;
use Spryker\Zed\Tax\Communication\TaxCommunicationFactory as SprykerTaxCommunicationFactory;

/**
 * Class TaxCommunicationFactory
 * @package Pyz\Zed\Tax\Communication
 * @method \Pyz\Zed\Tax\TaxConfig getConfig()
 */
class TaxCommunicationFactory extends SprykerTaxCommunicationFactory
{
    /**
     * @param \Spryker\Zed\Tax\Communication\Form\DataProvider\TaxRateFormDataProvider|null $taxRateFormDataProvider
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getTaxRateForm(?TaxRateFormDataProvider $taxRateFormDataProvider = null)
    {
        return $this
            ->getFormFactory()
            ->create(
                TaxRateForm::class,
                $this->getTaxRateFormData($taxRateFormDataProvider),
                [
                    'data_class' => TaxRateTransfer::class,
                ]
            );
    }

    /**
     *
     * @return \Pyz\Zed\TaxProductConnector\Persistence\TaxProductConnectorQueryContainer
     */
    public function getTaxProductQueryContainer(): TaxProductConnectorQueryContainer
    {
        return $this
            ->getProvidedDependency(TaxDependencyProvider::QUERY_CONTAINER_TAX_PRODUCT);
    }
}
