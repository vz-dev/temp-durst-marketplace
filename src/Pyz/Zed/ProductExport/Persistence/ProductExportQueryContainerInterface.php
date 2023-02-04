<?php
/**
 * Durst - project - ProductExportQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:12
 */

namespace Pyz\Zed\ProductExport\Persistence;


use Orm\Zed\ProductExport\Persistence\DstProductExportQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface ProductExportQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     */
    public function queryProductExport(): DstProductExportQuery;

    /**
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     */
    public function queryProductExportWaiting(): DstProductExportQuery;

    /**
     * @param int $idBranch
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     */
    public function queryProductExportWaitingByBranch(int $idBranch): DstProductExportQuery;

    /**
     * @param int $idProductExport
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     */
    public function queryProductExportById(int $idProductExport): DstProductExportQuery;
}
