<?php
/**
 * Durst - project - PriceImportFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 10:56
 */

namespace Pyz\Zed\PriceImport\Business;


use Generated\Shared\Transfer\PriceImportTransfer;

interface PriceImportFacadeInterface
{
    /**
     * Create an entry in the price import queue for the given transfer data
     *
     * @param \Generated\Shared\Transfer\PriceImportTransfer $importTransfer
     * @return \Generated\Shared\Transfer\PriceImportTransfer
     */
    public function createPriceImport(PriceImportTransfer $importTransfer): PriceImportTransfer;

    /**
     * Find the next price import and import these prices
     *
     * @return array
     */
    public function importNext(): array;
}
