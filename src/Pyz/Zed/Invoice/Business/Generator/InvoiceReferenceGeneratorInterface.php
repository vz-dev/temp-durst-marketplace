<?php
/**
 * Durst - project - InvoiceReferenceGeneratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 10:47
 */

namespace Pyz\Zed\Invoice\Business\Generator;


use Generated\Shared\Transfer\OrderTransfer;

interface InvoiceReferenceGeneratorInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string
     */
    public function createInvoiceReference(OrderTransfer $orderTransfer): string;

    /**
     * @param int $idBranch
     * @return string
     */
    public function createInvoiceReferenceFromBranchId(int $idBranch): string;
}
