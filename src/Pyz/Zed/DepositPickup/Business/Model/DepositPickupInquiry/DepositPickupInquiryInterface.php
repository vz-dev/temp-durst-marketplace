<?php

namespace Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry;

use Generated\Shared\Transfer\DepositPickupInquiryTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Throwable;

interface DepositPickupInquiryInterface
{
    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     *
     * @throws Throwable
     */
    public function save(DepositPickupInquiryTransfer $inquiryTransfer): void;

    /**
     * @param int $fkBranch
     *
     * @return array|DepositPickupInquiryTransfer[]
     *
     * @throws AmbiguousComparisonException
     */
    public function getInquiriesByFkBranch(int $fkBranch): array;

    /**
     * @param int $idDepositPickupInquiry
     * @param int $fkBranch
     *
     * @return DepositPickupInquiryTransfer|null
     *
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function getInquiryByIdAndFkBranch(int $idDepositPickupInquiry, int $fkBranch): ?DepositPickupInquiryTransfer;
}
