<?php
/**
     * Created by PhpStorm.
     * User: Giuliano
     * Date: 25.01.18
     * Time: 15:39
     */

namespace Pyz\Zed\Absence\Persistence;

use Orm\Zed\Absence\Persistence\SpyAbsenceQuery;

interface AbsenceQueryContainerInterface
{
    /**
     * @return SpyAbsenceQuery
     */
    public function queryAbsence();
}