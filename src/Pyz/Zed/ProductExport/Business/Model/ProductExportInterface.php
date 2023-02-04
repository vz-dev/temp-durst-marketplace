<?php
/**
 * Durst - project - ProductExportInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:37
 */

namespace Pyz\Zed\ProductExport\Business\Model;


use Generated\Shared\Transfer\ProductExportTransfer;

interface ProductExportInterface
{
    /**
     * @param \Generated\Shared\Transfer\ProductExportTransfer $productExportTransfer
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     */
    public function createProductExport(ProductExportTransfer $productExportTransfer): ProductExportTransfer;

    /**
     * @param int $idProductExport
     * @return \Generated\Shared\Transfer\ProductExportTransfer
     */
    public function getProductExportById(int $idProductExport): ProductExportTransfer;
}
