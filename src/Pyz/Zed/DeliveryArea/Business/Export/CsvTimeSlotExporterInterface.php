<?php
/**
 * Durst - project - CsvTimeSlotExporterInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.09.20
 * Time: 13:54
 */

namespace Pyz\Zed\DeliveryArea\Business\Export;


interface CsvTimeSlotExporterInterface
{
    /**
     * @param int $idBranch
     * @param array $emails
     * @param int $page
     * @param string|null $filename
     *
     * @return void
     */
    public function writeChunk(int $idBranch, array $emails, int $page, ?string $filename = null): void;
}
