<?php
/**
 * Durst - project - BillingToMerchantFacadeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 13:40
 */

namespace Pyz\Zed\Billing\Dependency\Facade;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;

interface BillingToMerchantBridgeInterface
{
    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer;

    /**
     * @param int $idMerchant
     * @return MerchantTransfer
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer;

    /**
     * @param string $code
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     */
    public function getPaymentMethodByCode(string $code): PaymentMethodTransfer;
}
