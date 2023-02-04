<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-14
 * Time: 11:08
 */

namespace Pyz\Zed\Edifact\Persistence;


use Orm\Zed\Edifact\Persistence\DstEdifactExportLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class EdifactQueryContainer
 * @package Pyz\Zed\Edifact\Persistence
 * @method EdifactPersistenceFactory getFactory()
 */
class EdifactQueryContainer extends AbstractQueryContainer implements EdifactQueryContainerInterface
{

    /**
     * @return DstEdifactExportLogQuery
     */
    public function queryEdifactExportLog(): DstEdifactExportLogQuery
    {
        return $this
            ->getFactory()
            ->createEdifactExportLogQuery();
    }
}