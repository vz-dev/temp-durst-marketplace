<?php
/**
 * Durst - project - BillingToMerchantBridge.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 13:41
 */

namespace Pyz\Zed\Billing\Dependency\Facade;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class BillingToMerchantBridge implements BillingToMerchantBridgeInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * InvoiceToMerchantFacade constructor.
     *
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(MerchantFacadeInterface $merchantFacade)
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer
    {
        return $this
            ->merchantFacade
            ->getBranchById($idBranch);
    }

    /**
     * @param int $idMerchant
     * @return MerchantTransfer
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer
    {
        return $this
            ->merchantFacade
            ->getMerchantById($idMerchant);
    }

    /**
     * @param string $code
     * @return PaymentMethodTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\PaymentMethodNotFoundException
     */
    public function getPaymentMethodByCode(string $code): PaymentMethodTransfer
    {
        return $this
            ->merchantFacade
            ->getPaymentMethodByCode($code);
    }
}
