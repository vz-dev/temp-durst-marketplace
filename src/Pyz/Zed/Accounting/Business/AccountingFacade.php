<?php
/**
 * Durst - project - AccountingFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:11
 */

namespace Pyz\Zed\Accounting\Business;


use Generated\Shared\Transfer\RealaxTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class AccountingFacade
 * @package Pyz\Zed\Accounting\Business
 * @method AccountingBusinessFactory getFactory()
 */
class AccountingFacade extends AbstractFacade implements AccountingFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param array $payload
     * @return array
     */
    public function mapRealaxExport(array $payload): array
    {
        return $this
            ->getFactory()
            ->createRealaxExportMapper()
            ->map(
                $payload
            );
    }

    /**
     * {@inheritDoc}
     *
     * @return int[]
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getAllMerchantsForRealaxExport(): array
    {
        return $this
            ->getFactory()
            ->createRealaxInvoice()
            ->getAllMerchantsForRealaxExport();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\RealaxTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getRealaxTransferByIdMerchant(int $idMerchant): RealaxTransfer
    {
        return $this
            ->getFactory()
            ->createRealaxInvoice()
            ->getRealaxTransferByIdMerchant(
                $idMerchant
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\RealaxTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getRealaxFixedTransferByIdMerchant(int $idMerchant): RealaxTransfer
    {
        return $this
            ->getFactory()
            ->createRealaxInvoiceFixed()
            ->getRealaxTransferByIdMerchant(
                $idMerchant
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @param string|null $path
     * @param int|null $timeout
     * @param string|null $applicationEnv
     * @param string|null $applicationStore
     * @param string|null $applicationRootDir
     * @param string|null $application
     * @return int
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function exportRealax(
        int $idMerchant,
        ?string $path = null,
        ?int $timeout = 0,
        ?string $applicationEnv = null,
        ?string $applicationStore = null,
        ?string $applicationRootDir = null,
        ?string $application = null
    ): int
    {
        return $this
            ->getFactory()
            ->createRealaxInvoice()
            ->exportRealax(
                $idMerchant,
                $path,
                $timeout,
                $applicationEnv,
                $applicationStore,
                $applicationRootDir,
                $application
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @param string|null $path
     * @param int|null $timeout
     * @param string|null $applicationEnv
     * @param string|null $applicationStore
     * @param string|null $applicationRootDir
     * @param string|null $application
     * @return int
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function exportRealaxFixed(
        int $idMerchant,
        ?string $path = null,
        ?int $timeout = 0,
        ?string $applicationEnv = null,
        ?string $applicationStore = null,
        ?string $applicationRootDir = null,
        ?string $application = null
    ): int
    {
        return $this
            ->getFactory()
            ->createRealaxInvoiceFixed()
            ->exportRealax(
                $idMerchant,
                $path,
                $timeout,
                $applicationEnv,
                $applicationStore,
                $applicationRootDir,
                $application
            );
    }
}
