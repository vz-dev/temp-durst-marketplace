<?php
/**
 * Durst - project - PriceImportManagerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 11:13
 */

namespace Pyz\Zed\PriceImport\Business\Manager;


interface PriceImportManagerInterface
{
    /**
     * @return array
     */
    public function importNext(): array;
}
