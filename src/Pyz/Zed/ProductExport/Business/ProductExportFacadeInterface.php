<?php
/**
 * Durst - project - ProductExportFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:45
 */

namespace Pyz\Zed\ProductExport\Business;

use Generated\Shared\Transfer\ProductExportTransfer;

interface ProductExportFacadeInterface
{
    /**
     * Create an entry in the database table with the values from the transfer
     *
     * @param \Generated\Shared\Transfer\ProductExportTransfer $productExportTransfer
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     */
    public function createProductExport(ProductExportTransfer $productExportTransfer): ProductExportTransfer;

    /**
     * Export the next product export in status "waiting"
     * Sorted by creation date
     *
     * @return void
     */
    public function exportNext(): void;
}
