<?php

namespace Pyz\Zed\DepositPickup\Persistence;

use Orm\Zed\DepositPickup\Persistence\DstDepositPickupInquiryQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

interface DepositPickupQueryContainerInterface extends QueryContainerInterface
{
    /**
     * Query deposit pickup inquiries filtered by the given branch ID
     *
     * @param int $fkBranch
     *
     * @return DstDepositPickupInquiryQuery
     *
     * @throws AmbiguousComparisonException
     */
    public function queryInquiriesByFkBranch(int $fkBranch): DstDepositPickupInquiryQuery;
}
