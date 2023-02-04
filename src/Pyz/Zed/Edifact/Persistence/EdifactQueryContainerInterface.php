<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:07
 */

namespace Pyz\Zed\Edifact\Persistence;


use Orm\Zed\Edifact\Persistence\DstEdifactExportLogQuery;

interface EdifactQueryContainerInterface
{
    /**
     * @return DstEdifactExportLogQuery
     */
    public function queryEdifactExportLog(): DstEdifactExportLogQuery;
}