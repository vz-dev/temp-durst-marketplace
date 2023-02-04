<?php
/**
 * Durst - project - PaymentMethodInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.12.21
 * Time: 11:12
 */

namespace Pyz\Zed\Merchant\Business\Model;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;

interface PaymentMethodInterface
{
    /**
     * @param $idPaymentMethod
     * @param $idBranch
     * @return mixed
     */
    public function removePaymentMethodFromBranch(int $idPaymentMethod, int $idBranch);

    /**
     * @param int $idPaymentMethod
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return mixed
     */
    public function addPaymentMethodToBranch(int $idPaymentMethod, BranchTransfer $branchTransfer);

    /**
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return mixed
     */
    public function addPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer);

    /**
     * @param PaymentMethodTransfer $paymentMethodTransfer
     * @return mixed
     */
    public function updatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer);

    /**
     * @param int $idPaymentMethod
     * @return mixed
     */
    public function removePaymentMethod(int $idPaymentMethod);

    /**
     * @param int $idPaymentMethod
     * @return mixed
     */
    public function getPaymentMethodById(int $idPaymentMethod);

    /**
     * @param int $idBranch
     * @return mixed
     */
    public function getPaymentMethodsByIdBranch(int $idBranch);

    /**
     * @param int $idBranch
     * @param string $paymentMethod
     * @return bool
     */
    public function hasBranchPaymentMethod(int $idBranch, string $paymentMethod): bool;

    /**
     * @param array $branchIds
     * @return PaymentMethodTransfer[]
     */
    public function getSupportedPaymentMethodsForBranches(array $branchIds): array;

    /**
     * @return PaymentMethodTransfer[]
     */
    public function getPaymentMethods(): array;

    /**
     * @param string $code
     * @return int
     */
    public function getPaymentMethodIdByCode(string $code): int;

    /**
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsForCurrentBranch(): array;

    /**
     * @param int $idBranch
     * @return PaymentMethodTransfer[]
     */
    public function getPossiblePaymentMethodsByIdBranch(int $idBranch): array;

    /**
     * @param int $idMerchant
     * @param int $idBranch
     * @return array
     */
    public function getPossiblePaymentMethodsByIdBranchByMerchantId(int $idMerchant, int $idBranch): array;

    /**
     * @param string $code
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException   if no payment method with the given code
     *                                          can be found.
     */
    public function getPaymentMethodByCode(string $code): PaymentMethodTransfer;
}
