<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 11:45
 */

namespace Pyz\Zed\ProductManagement\Communication\Transfer\DataProvider;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Pyz\Zed\Deposit\Persistence\DepositQueryContainerInterface;
use Pyz\Zed\ProductManagement\Communication\Form\ProductConcreteFormEdit;
use Spryker\Zed\Category\Persistence\CategoryQueryContainerInterface;
use Spryker\Zed\Product\Persistence\ProductQueryContainerInterface;
use Spryker\Zed\ProductManagement\Communication\Form\DataProvider\LocaleProvider;
use Spryker\Zed\ProductManagement\Communication\Form\DataProvider\ProductConcreteFormEditDataProvider as SprykerProductConcreteFormEditDataProvider;
use Spryker\Zed\ProductManagement\Communication\Helper\ProductStockHelperInterface;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToProductImageInterface;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToProductInterface;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToStoreInterface;
use Spryker\Zed\ProductManagement\Persistence\ProductManagementQueryContainerInterface;
use Spryker\Zed\Stock\Persistence\StockQueryContainerInterface;

class ProductConcreteFormEditDataProvider extends SprykerProductConcreteFormEditDataProvider
{
    /**
     * @var DepositQueryContainerInterface
     */
    protected $depositQueryContainer;

    public function __construct(
        CategoryQueryContainerInterface $categoryQueryContainer,
        ProductManagementQueryContainerInterface $productManagementQueryContainer,
        ProductQueryContainerInterface $productQueryContainer,
        StockQueryContainerInterface $stockQueryContainer,
        ProductManagementToProductInterface $productFacade,
        ProductManagementToProductImageInterface $productImageFacade,
        LocaleProvider $localeProvider,
        LocaleTransfer $currentLocale,
        array $attributeCollection,
        array $taxCollection,
        $imageUrlPrefix,
        ProductManagementToStoreInterface $store,
        ProductStockHelperInterface $productStockHelper,
        DepositQueryContainerInterface $depositQueryContainer
    )
    {
        parent::__construct(
            $categoryQueryContainer,
            $productManagementQueryContainer,
            $productQueryContainer,
            $stockQueryContainer,
            $productFacade,
            $productImageFacade,
            $localeProvider,
            $currentLocale,
            $attributeCollection,
            $taxCollection,
            $imageUrlPrefix,
            $store,
            $productStockHelper
        );

        $this->depositQueryContainer = $depositQueryContainer;
    }

    /**
     * @param int|null $idProductAbstract
     * @param string|null $type
     *
     * @return mixed
     */
    public function getOptions($idProductAbstract = null, $type = null)
    {
        $formOptions = parent::getOptions($idProductAbstract);

        $formOptions[ProductConcreteFormEdit::OPTION_DEPOSIT] = $this->getDepositOptions();

        return $formOptions;
    }

    /**
     * @return array
     */
    protected function getDepositOptions()
    {
        $deposits = $this
            ->depositQueryContainer
            ->queryDeposit()
            ->find();

        $options = [];
        foreach($deposits as $deposit){
            $options[$deposit->getIdDeposit()] = $deposit->getName();
        }

        return $options;
    }

    /**
     * @param ProductAbstractTransfer $productAbstractTransfer
     * @param ProductConcreteTransfer $productTransfer
     * @param array $formData
     * @return array
     */
    protected function appendVariantGeneralAndSeoData(
        ProductAbstractTransfer $productAbstractTransfer,
        ProductConcreteTransfer $productTransfer,
        array $formData)
    {
        $formData = parent::appendVariantGeneralAndSeoData(
            $productAbstractTransfer,
            $productTransfer,
            $formData
        );

        $formData[ProductConcreteFormEdit::FIELD_DEPOSIT] = $productTransfer->getFkDeposit();

        return $formData;
    }
}