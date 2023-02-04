<?php
/**
 * Durst - project - RealaxInvoiceFixedInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.09.20
 * Time: 15:31
 */

namespace Pyz\Zed\Accounting\Business\Model;


use Generated\Shared\Transfer\RealaxTransfer;

interface RealaxInvoiceFixedInterface
{
    /**
     * @return int[]
     */
    public function getAllMerchantsForRealaxExport(): array;

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\RealaxTransfer
     */
    public function getRealaxTransferByIdMerchant(int $idMerchant): RealaxTransfer;

    /**
     * @param int $idMerchant
     * @param string|null $path
     * @param int|null $timeout
     * @param string|null $applicationEnv
     * @param string|null $applicationStore
     * @param string|null $applicationRootDir
     * @param string|null $application
     * @return int
     */
    public function exportRealax(
        int $idMerchant,
        ?string $path = null,
        ?int $timeout = 0,
        ?string $applicationEnv = null,
        ?string $applicationStore = null,
        ?string $applicationRootDir = null,
        ?string $application = null
    ): int;
}
