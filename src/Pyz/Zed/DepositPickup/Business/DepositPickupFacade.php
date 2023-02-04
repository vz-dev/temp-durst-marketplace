<?php

namespace Pyz\Zed\DepositPickup\Business;

use Generated\Shared\Transfer\DepositPickupInquiryTransfer;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Throwable;

/**
 * @method DepositPickupBusinessFactory getFactory()
 */
class DepositPickupFacade extends AbstractFacade implements DepositPickupFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     *
     * @throws Throwable
     */
    public function saveInquiry(DepositPickupInquiryTransfer $inquiryTransfer): void
    {
        $this
            ->getFactory()
            ->createDepositPickupInquiryModel()
            ->save($inquiryTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $fkBranch
     *
     * @return array|DepositPickupInquiryTransfer[]
     *
     * @throws AmbiguousComparisonException
     * @throws ContainerKeyNotFoundException
     */
    public function getInquiriesByFkBranch(int $fkBranch): array
    {
        return $this
            ->getFactory()
            ->createDepositPickupInquiryModel()
            ->getInquiriesByFkBranch($fkBranch);
    }

    /**
     * {@inheritDoc}
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
    public function getInquiryByIdAndFkBranch(int $idDepositPickupInquiry, int $fkBranch): ?DepositPickupInquiryTransfer
    {
        return $this
            ->getFactory()
            ->createDepositPickupInquiryModel()
            ->getInquiryByIdAndFkBranch($idDepositPickupInquiry, $fkBranch);
    }
}
