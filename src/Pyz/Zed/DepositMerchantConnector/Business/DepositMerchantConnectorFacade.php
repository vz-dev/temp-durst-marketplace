<?php
/**
 * Durst - project - DepositMerchantConnectorFacaed.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 11:25
 */

namespace Pyz\Zed\DepositMerchantConnector\Business;

use Generated\Shared\Transfer\BranchTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * Class DepositMerchantConnectorFacade
 * @package Pyz\Zed\DepositMerchantConnector\Business
 * @method DepositMerchantConnectorBusinessFactory getFactory()
 */
class DepositMerchantConnectorFacade extends AbstractFacade implements DepositMerchantConnectorFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): array
    {
        return $this
            ->getFactory()
            ->createDepositBranchModel()
            ->getDepositsForBranch($branchTransfer);
    }
}
