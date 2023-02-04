<?php
/**
     * Created by PhpStorm.
     * User: Giuliano
     * Date: 25.01.18
     * Time: 15:40
     */

namespace Pyz\Zed\Absence\Persistence;


use Orm\Zed\Absence\Persistence\SpyAbsenceQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class AbsenceQueryContainer
 * @package Pyz\Zed\Absence\Persistence
 * @method AbsencePersistenceFactory getFactory()
 */
class AbsenceQueryContainer extends AbstractQueryContainer implements AbsenceQueryContainerInterface
{

    /**
     * @return SpyAbsenceQuery
     */
    public function queryAbsence()
    {
        return $this->getFactory()->createAbsenceQuery();
    }
}