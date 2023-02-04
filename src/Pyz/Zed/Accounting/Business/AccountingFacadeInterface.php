<?php
/**
 * Durst - project - AccountingFacadeInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:11
 */

namespace Pyz\Zed\Accounting\Business;


use Generated\Shared\Transfer\RealaxTransfer;

interface AccountingFacadeInterface
{
    /**
     * Map the given payload (from transfer toArray) to the needed order in the csv file
     *
     * @param array $payload
     * @return array
     */
    public function mapRealaxExport(array $payload): array;

    /**
     * Get a list of all merchants that should be exported
     * - exclude inactive merchants
     * - exclude merchants without Realax debitor number
     *
     * @return int[]
     */
    public function getAllMerchantsForRealaxExport(): array;

    /**
     * Get a Realax transfer with variable values from the given merchant ID
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\RealaxTransfer
     */
    public function getRealaxTransferByIdMerchant(int $idMerchant): RealaxTransfer;

    /**
     * Get a Realax transfer with fixed values from the given merchant ID
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\RealaxTransfer
     */
    public function getRealaxFixedTransferByIdMerchant(int $idMerchant): RealaxTransfer;

    /**
     * Export the CSV file for the given merchant to the given path
     * It will export all variable values like:
     * - license variable (sold products)
     *
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

    /**
     * Export the CSV file for the given merchant to the given path
     * It will export all fixed values like
     * - license fixed
     * - marketing fixed
     *
     * @param int $idMerchant
     * @param string|null $path
     * @param int|null $timeout
     * @param string|null $applicationEnv
     * @param string|null $applicationStore
     * @param string|null $applicationRootDir
     * @param string|null $application
     * @return int
     */
    public function exportRealaxFixed(
        int $idMerchant,
        ?string $path = null,
        ?int $timeout = 0,
        ?string $applicationEnv = null,
        ?string $applicationStore = null,
        ?string $applicationRootDir = null,
        ?string $application = null
    ): int;
}
