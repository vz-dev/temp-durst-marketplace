<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 11:31
 */

namespace Pyz\Zed\ProductManagement\Communication;

use Pyz\Zed\ProductManagement\Communication\Form\ProductConcreteFormEdit;
use Pyz\Zed\ProductManagement\Communication\Table\ProductTable;
use Pyz\Zed\ProductManagement\Communication\Table\VariantTable;
use Pyz\Zed\ProductManagement\Communication\Transfer\DataProvider\ProductConcreteFormEditDataProvider;
use Pyz\Zed\ProductManagement\Communication\Transfer\ProductFormTransferMapper;
use Pyz\Zed\ProductManagement\ProductManagementDependencyProvider;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Spryker\Zed\ProductManagement\Communication\ProductManagementCommunicationFactory as SprykerProductManagementCommunicationFactory;
use Symfony\Component\Form\FormInterface;

class ProductManagementCommunicationFactory extends SprykerProductManagementCommunicationFactory
{
    /**
     * @return ProductFormTransferMapper|\Spryker\Zed\ProductManagement\Communication\Transfer\ProductFormTransferMapper
     */
    public function createProductFormTransferGenerator() : ProductFormTransferMapper
    {
        return new ProductFormTransferMapper(
            $this->getProductQueryContainer(),
            $this->getQueryContainer(),
            $this->getLocaleFacade(),
            $this->getUtilTextService(),
            $this->createLocaleProvider()
        );
    }

    /**
     * @param array $formData
     * @param array $formOptions
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createProductVariantFormEdit(array $formData, array $formOptions = []) : FormInterface
    {
        return $this->getFormFactory()->create(ProductConcreteFormEdit::class, $formData, $formOptions);
    }

    /**
     * @return ProductConcreteFormEditDataProvider|\Spryker\Zed\ProductManagement\Communication\Form\DataProvider\ProductConcreteFormEditDataProvider
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createProductVariantFormEditDataProvider() : ProductConcreteFormEditDataProvider
    {
        $currentLocale = $this->getLocaleFacade()->getCurrentLocale();

        return new ProductConcreteFormEditDataProvider(
            $this->getCategoryQueryContainer(),
            $this->getQueryContainer(),
            $this->getProductQueryContainer(),
            $this->getStockQueryContainer(),
            $this->getProductFacade(),
            $this->getProductImageFacade(),
            $this->createLocaleProvider(),
            $currentLocale,
            $this->getProductAttributeCollection(),
            $this->getProductTaxCollection(),
            $this->getConfig()->getImageUrlPrefix(),
            $this->getStore(),
            $this->createProductStockHelper(),
            $this->getDepositQueryContainer()
        );
    }

    /**
     * @return DepositQueryContainerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getDepositQueryContainer() : DepositQueryContainerInterface
    {
        return $this
            ->getProvidedDependency(ProductManagementDependencyProvider::QUERY_CONTAINER_DEPOSIT);
    }

    /**
     * @return ProductTable|\Spryker\Zed\Gui\Communication\Table\AbstractTable
     */
    public function createProductTable() : ProductTable
    {
        return new ProductTable($this->getProductQueryContainer(), $this->getLocaleFacade()->getCurrentLocale());
    }

    /**
     * @param int $idProductAbstract
     * @param string $type
     * @return VariantTable|\Spryker\Zed\Gui\Communication\Table\AbstractTable
     */
    public function createVariantTable($idProductAbstract, $type)
    {
        return new VariantTable(
            $this->getProductQueryContainer(),
            $idProductAbstract,
            $this->getLocaleFacade()->getCurrentLocale(),
            $type
        );
    }
}
