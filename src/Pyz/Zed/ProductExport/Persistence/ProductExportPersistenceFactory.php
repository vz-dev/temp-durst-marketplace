<?php
/**
 * Durst - project - ProductExportPersistenceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:13
 */

namespace Pyz\Zed\ProductExport\Persistence;


use Orm\Zed\ProductExport\Persistence\DstProductExportQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class ProductExportPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     */
    public function createProductExportQuery(): DstProductExportQuery
    {
        return DstProductExportQuery::create();
    }
}
