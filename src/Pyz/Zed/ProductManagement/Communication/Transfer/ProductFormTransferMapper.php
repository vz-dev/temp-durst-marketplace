<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 11:32
 */

namespace Pyz\Zed\ProductManagement\Communication\Transfer;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Pyz\Zed\ProductManagement\Communication\Form\ProductConcreteFormEdit;
use Spryker\Zed\ProductManagement\Communication\Transfer\ProductFormTransferMapper as SprykerProductFormTransferMapper;
use Symfony\Component\Form\FormInterface;

class ProductFormTransferMapper extends SprykerProductFormTransferMapper
{
    /**
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     * @param \Symfony\Component\Form\FormInterface $form
     * @param int $idProduct
     *
     * @return \Generated\Shared\Transfer\ProductConcreteTransfer
     */
    public function buildProductConcreteTransfer(
        ProductAbstractTransfer $productAbstractTransfer,
        FormInterface $form,
        $idProduct
    )
    {
        $productConcreteTransfer = parent::buildProductConcreteTransfer($productAbstractTransfer, $form, $idProduct);
        return $productConcreteTransfer->setFkDeposit($form->get(ProductConcreteFormEdit::FIELD_DEPOSIT)->getData());
    }
}