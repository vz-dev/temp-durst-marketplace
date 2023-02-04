<?php
/**
     * Created by PhpStorm.
     * User: Giuliano
     * Date: 25.01.18
     * Time: 15:36
     */

namespace Pyz\Zed\Absence\Persistence;


use Orm\Zed\Absence\Persistence\SpyAbsenceQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class AbsencePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return SpyAbsenceQuery
     */
    public function createAbsenceQuery()
    {
        return SpyAbsenceQuery::create();
    }
}