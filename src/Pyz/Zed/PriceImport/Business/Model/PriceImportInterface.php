<?php
/**
 * Durst - project - PriceImportInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.10.20
 * Time: 16:03
 */

namespace Pyz\Zed\PriceImport\Business\Model;


use Generated\Shared\Transfer\PriceImportTransfer;

interface PriceImportInterface
{
    /**
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return \Generated\Shared\Transfer\PriceImportTransfer
     */
    public function createPriceImport(PriceImportTransfer $importTransfer): PriceImportTransfer;
}
