<?php

namespace Pyz\Zed\Integra\Business;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method IntegraBusinessFactory getFactory()
 */
class IntegraFacade extends AbstractFacade implements IntegraFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function exportOpenOrdersForBranch(int $idBranch): void
    {
        $this
            ->getFactory()
            ->createOpenOrdersExportManager()
            ->exportOrders($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function exportClosedOrdersForBranch(int $idBranch): void
    {
        $this
            ->getFactory()
            ->createClosedOrdersExportManager()
            ->exportOrders($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param IntegraCredentialsTransfer $transfer
     *
     * @return void
     */
    public function save(IntegraCredentialsTransfer $transfer): void
    {
        $this
            ->getFactory()
            ->createIntegraCredentialsModel()
            ->save($transfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idIntegraCredentials
     *
     * @return void
     */
    public function removeCredentials(int $idIntegraCredentials): void
    {
        $this
            ->getFactory()
            ->createIntegraCredentialsModel()
            ->remove($idIntegraCredentials);
    }

    /**
     * @return array
     */
    public function getBranchIdsThatUseIntegra(): array
    {
        return $this
            ->getFactory()
            ->createIntegraCredentialsModel()
            ->getBranchIdsThatUseIntegra();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseIntegra(int $idBranch): bool
    {
        return $this
            ->getFactory()
            ->createIntegraCredentialsModel()
            ->doesBranchUseIntegra($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return void
     */
    public function importOrdersForBranch(int $idBranch): void
    {
        $this
            ->getFactory()
            ->createImportManager()
            ->importOrdersForBranch($idBranch);
    }

    public function login(int $idBranch): string
    {
        return $this
            ->getFactory()
            ->createImportManager()
            ->login($idBranch);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     * @return IntegraCredentialsTransfer
     */
    public function getCredentialsByIdBranch(int $idBranch): IntegraCredentialsTransfer
    {
        return $this
            ->getFactory()
            ->createIntegraCredentialsModel()
            ->getCredentialsByIdBranch(
                $idBranch
            );
    }
}
