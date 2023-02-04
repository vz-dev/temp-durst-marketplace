<?php
/**
 * Durst - project - ProductExportQueryContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.09.20
 * Time: 16:12
 */

namespace Pyz\Zed\ProductExport\Persistence;


use Orm\Zed\ProductExport\Persistence\DstProductExportQuery;
use Orm\Zed\ProductExport\Persistence\Map\DstProductExportTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Class ProductExportQueryContainer
 * @package Pyz\Zed\ProductExport\Persistence
 * @method ProductExportPersistenceFactory getFactory()
 */
class ProductExportQueryContainer extends AbstractQueryContainer implements ProductExportQueryContainerInterface
{
    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     */
    public function queryProductExport(): DstProductExportQuery
    {
        return $this
            ->getFactory()
            ->createProductExportQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryProductExportWaiting(): DstProductExportQuery
    {
        return $this
            ->getFactory()
            ->createProductExportQuery()
            ->filterByStatus(
                DstProductExportTableMap::COL_STATUS_WAITING
            )
            ->orderByCreatedAt(
                Criteria::ASC
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryProductExportWaitingByBranch(int $idBranch): DstProductExportQuery
    {
        return $this
            ->queryProductExportWaiting()
            ->filterByFkBranch(
                $idBranch
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idProductExport
     * @return \Orm\Zed\ProductExport\Persistence\DstProductExportQuery
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function queryProductExportById(int $idProductExport): DstProductExportQuery
    {
        return $this
            ->queryProductExport()
            ->filterByIdProductExport(
                $idProductExport
            );
    }
}
