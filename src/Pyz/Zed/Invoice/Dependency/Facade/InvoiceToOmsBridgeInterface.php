<?php
/**
 * Durst - project - InvoiceToOmsBridgeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.03.20
 * Time: 09:32
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;


use Generated\Shared\Transfer\DurstCompanyTransfer;

interface InvoiceToOmsBridgeInterface
{
    /**
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer(): DurstCompanyTransfer;
}
