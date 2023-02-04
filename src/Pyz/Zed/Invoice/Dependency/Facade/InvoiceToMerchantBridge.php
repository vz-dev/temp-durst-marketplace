<?php
/**
 * Durst - project - InvoiceToMerchantFacade.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 30.12.19
 * Time: 11:06
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;

use Generated\Shared\Transfer\BranchTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class InvoiceToMerchantBridge implements InvoiceToMerchantBridgeInterface
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
     * @return BranchTransfer[]
     */
    public function getBranches() : array
    {
        return $this
            ->merchantFacade
            ->getBranches();
    }
}
