<?php
/**
 * Durst - project - ExportInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.11.20
 * Time: 14:11
 */

namespace Pyz\Zed\Integra\Business\Model\Export;

interface ExportInterface
{
    /**
     * @param int $idBranch
     *
     * @return array
     */
    public function getMappedData(int $idBranch): array;

    /**
     * @return void
     */
    public function updateOrders(): void;

    /**
     * @return string
     */
    public function getExportType() : string;
}
