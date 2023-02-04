<?php
/**
 * Durst - project - OpenOrdersExportManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 20:12
 */

namespace Pyz\Zed\Integra\Business\Model;


interface ExportManagerInterface
{
    /**
     * @param int $idBranch
     *
     * @return void
     */
    public function exportOrders(int $idBranch): void;
}
