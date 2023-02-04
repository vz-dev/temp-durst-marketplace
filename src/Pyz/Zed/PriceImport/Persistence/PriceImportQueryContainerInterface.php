<?php
/**
 * Durst - project - PriceImportQueryContainerInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 16:36
 */

namespace Pyz\Zed\PriceImport\Persistence;


use Orm\Zed\PriceImport\Persistence\DstPriceImportQuery;

interface PriceImportQueryContainerInterface
{
    /**
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     */
    public function queryPriceImport(): DstPriceImportQuery;

    /**
     * @param int $idPriceImport
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     */
    public function queryPriceImportById(int $idPriceImport): DstPriceImportQuery;

    /**
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     */
    public function queryPriceImportWaiting(): DstPriceImportQuery;
}
