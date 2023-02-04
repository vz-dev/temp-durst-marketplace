<?php
/**
 * Durst - project - PriceImportPersistenceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 16:34
 */

namespace Pyz\Zed\PriceImport\Persistence;


use Orm\Zed\PriceImport\Persistence\DstPriceImportQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

class PriceImportPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     */
    public function createPriceImportQuery(): DstPriceImportQuery
    {
        return DstPriceImportQuery::create();
    }
}
