<?php

namespace Pyz\Zed\DepositPickup\Business;

use Generated\Shared\Transfer\DepositPickupInquiryTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Throwable;

interface DepositPickupFacadeInterface
{
    /**
     * Persists the given deposit pickup inquiry to the database
     *
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     *
     * @throws Throwable
     */
    public function saveInquiry(DepositPickupInquiryTransfer $inquiryTransfer): void;

    /**
     * Returns all inquiries for the given branch ID
     *
     * @param int $fkBranch
     *
     * @return array|DepositPickupInquiryTransfer[]
     *
     * @throws AmbiguousComparisonException
     */
    public function getInquiriesByFkBranch(int $fkBranch): array;

    /**
     * Returns inquiry for the given inquiry ID and branch ID
     *
     * @param int $idDepositPickupInquiry
     * @param int $fkBranch
     *
     * @return DepositPickupInquiryTransfer|null
     *
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function getInquiryByIdAndFkBranch(int $idDepositPickupInquiry, int $fkBranch): ?DepositPickupInquiryTransfer;
}
