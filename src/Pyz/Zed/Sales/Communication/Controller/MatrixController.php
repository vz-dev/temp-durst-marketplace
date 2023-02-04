<?php

namespace Pyz\Zed\Sales\Communication\Controller;

use Pyz\Zed\Sales\Communication\SalesCommunicationFactory;
use Spryker\Zed\Sales\Communication\Controller\MatrixController as SprykerMatrixController;

/**
 * @method SalesCommunicationFactory getFactory()
 */
class MatrixController extends SprykerMatrixController
{
    /**
     * @return array
     */
    public function indexAction()
    {
        $matrix = $this
            ->getFactory()
            ->getOmsFacade()
            ->getOrderItemMatrix();

        return [
            'matrix' => $matrix,
        ];
    }
}
