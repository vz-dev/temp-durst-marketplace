<?php
/**
 * Durst - project - TimeSlotCsvImporterInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-10-14
 * Time: 14:28
 */

namespace Pyz\Zed\DeliveryArea\Business\Import;


interface TimeSlotCsvImporterInterface
{
    /**
     * @param string $jsonData
     * @return void
     */
    public function importTimeSlotsFromCsv(string $jsonData);
}
