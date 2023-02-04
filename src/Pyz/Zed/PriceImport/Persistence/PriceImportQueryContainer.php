<?php
/**
 * Durst - project - PriceImportQueryContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 05.10.20
 * Time: 16:36
 */

namespace Pyz\Zed\PriceImport\Persistence;


use Orm\Zed\PriceImport\Persistence\DstPriceImportQuery;
use Orm\Zed\PriceImport\Persistence\Map\DstPriceImportTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * Class PriceImportQueryContainer
 * @package Pyz\Zed\PriceImport\Persistence
 * @method PriceImportPersistenceFactory getFactory()
 */
class PriceImportQueryContainer extends AbstractQueryContainer implements PriceImportQueryContainerInterface
{
    /**
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     */
    public function queryPriceImport(): DstPriceImportQuery
    {
        return $this
            ->getFactory()
            ->createPriceImportQuery();
    }

    /**
     * @param int $idPriceImport
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryPriceImportById(int $idPriceImport): DstPriceImportQuery
    {
        return $this
            ->queryPriceImport()
            ->filterByIdPriceImport(
                $idPriceImport
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\PriceImport\Persistence\DstPriceImportQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryPriceImportWaiting(): DstPriceImportQuery
    {
        return $this
            ->queryPriceImport()
            ->filterByStatus(
                DstPriceImportTableMap::COL_STATUS_WAITING
            )
            ->orderByCreatedAt(
                Criteria::ASC
            );
    }
}
