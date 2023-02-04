<?php
/**
 * Durst - project - MerchantInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 14:12
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantInterface
{
    /**
     * @param string $password
     * @return string
     */
    public function encryptPassword(string $password): string;

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool;

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function save(MerchantTransfer $merchantTransfer): MerchantTransfer;

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function removeMerchant(int $idMerchant): MerchantTransfer;

    /**
     * @param string $merchantname
     * @return bool
     */
    public function hasMerchantByMerchantname(string $merchantname): bool;

    /**
     * @param string $merchantPin
     * @return bool
     */
    public function hasMerchantByMerchantPin(string $merchantPin): bool;

    /**
     * @param string $merchantname
     * @return bool
     */
    public function hasActiveMerchantByMerchantname(string $merchantname): bool;

    /**
     * @param int $idMerchant
     * @return bool
     */
    public function hasMerchantById(int $idMerchant): bool;

    /**
     * @param string $merchantname
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantname(string $merchantname): MerchantTransfer;

    /**
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer;

    /**
     * @param string $merchantPin
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer;

    /**
     * @param int $idMerchant
     * @param bool $hasBranchUser
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getActiveMerchantById(int $idMerchant, bool $hasBranchUser = false): MerchantTransfer;

    /**
     * @param int $idMerchant
     * @return bool
     */
    public function activateMerchant(int $idMerchant): bool;

    /**
     * @param int $idMerchant
     * @return bool
     */
    public function deactivateMerchant(int $idMerchant): bool;

    /**
     * @return MerchantTransfer
     */
    public function getCurrentMerchant(): MerchantTransfer;

    /**
     * @return bool
     */
    public function hasCurrentMerchant() : bool;

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchant
     * @return mixed
     */
    public function setCurrentMerchant(MerchantTransfer $merchant);

    /**
     * @return void
     */
    public function unsetCurrentMerchant(): void;

    /**
     * @return array|MerchantTransfer[]
     */
    public function getMerchants(): array;

    /**
     * @param string $merchantPin
     * @return bool
     */
    public function hasActiveMerchantByMerchantPin(string $merchantPin): bool;

    /**
     * @param int $idBranch
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByIdBranch(int $idBranch): MerchantTransfer;
}
