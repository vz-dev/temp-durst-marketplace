<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:05
 */

namespace Pyz\Zed\Edifact\Persistence;


use Orm\Zed\Edifact\Persistence\DstEdifactExportLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class EdifactPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return DstEdifactExportLogQuery
     */
    public function createEdifactExportLogQuery(): DstEdifactExportLogQuery
    {
        return DstEdifactExportLogQuery::create();
    }
}