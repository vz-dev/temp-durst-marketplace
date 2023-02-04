<?php
/**
 * Durst - project - MerchantUserInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.21
 * Time: 10:52
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\MerchantUserTransfer;

interface MerchantUserInterface
{
    /**
     * @param int $idMerchantUser
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getMerchantUserById(int $idMerchantUser): MerchantUserTransfer;

    /**
     * @param string $email
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function getMerchantUserByEmail(string $email): MerchantUserTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     */
    public function save(MerchantUserTransfer $merchantUserTransfer): MerchantUserTransfer;

    /**
     * @param int $idMerchantUser
     * @return bool
     */
    public function deleteMerchantUser(int $idMerchantUser): bool;

    /**
     * @param int $idMerchantUser
     * @return bool
     */
    public function activateMerchantUser(int $idMerchantUser): bool;

    /**
     * @param int $idMerchantUser
     * @return bool
     */
    public function deactivateMerchantUser(int $idMerchantUser): bool;

    /**
     * @return \Generated\Shared\Transfer\MerchantUserTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\MerchantUserNotFoundException
     */
    public function getCurrentMerchantUser(): MerchantUserTransfer;

    /**
     * @param \Generated\Shared\Transfer\MerchantUserTransfer $merchantUserTransfer
     * @return mixed
     */
    public function setCurrentMerchantUser(MerchantUserTransfer $merchantUserTransfer);

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool;

    /**
     * @return void
     */
    public function logout(): void;

    /**
     * @param string $email
     * @return bool
     */
    public function hasActiveMerchantUserByEmail(string $email): bool;

    /**
     * @return bool
     */
    public function hasCurrentMerchantUser(): bool;

    /**
     * @return void
     */
    public function unsetCurrentMerchantUser(): void;

    /**
     * @param int $idMerchant
     * @return MerchantUserTransfer[]
     */
    public function getMerchantUsersByIdMerchant(int $idMerchant): array;
}
