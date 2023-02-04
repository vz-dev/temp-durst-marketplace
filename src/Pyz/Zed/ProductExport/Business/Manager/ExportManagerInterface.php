<?php
/**
 * Durst - project - ExportManagerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.09.20
 * Time: 11:24
 */

namespace Pyz\Zed\ProductExport\Business\Manager;


interface ExportManagerInterface
{
    /**
     * @return void
     */
    public function exportNext(): void;
}
