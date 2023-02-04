<?php

namespace Pyz\Zed\DepositPickup\Persistence;

use Orm\Zed\DepositPickup\Persistence\DstDepositPickupInquiryQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * @method DepositPickupPersistenceFactory getFactory()
 */
class DepositPickupQueryContainer extends AbstractQueryContainer implements DepositPickupQueryContainerInterface
{
    /**
     * @return DstDepositPickupInquiryQuery
     */
    public function queryInquiry(): DstDepositPickupInquiryQuery
    {
        return $this
            ->getFactory()
            ->createDepositPickupInquiryQuery();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $fkBranch
     *
     * @return DstDepositPickupInquiryQuery
     *
     * @throws AmbiguousComparisonException
     */
    public function queryInquiriesByFkBranch(int $fkBranch): DstDepositPickupInquiryQuery
    {
        return $this
            ->getFactory()
            ->createDepositPickupInquiryQuery()
            ->filterByFkBranch($fkBranch)
            ->orderByCreatedAt(Criteria::DESC);
    }
}
