<?php
/**
 * Durst - project - DepositMerchantClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:40
 */

namespace Pyz\Client\DepositMerchantConnector;

use Generated\Shared\Transfer\BranchTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class DepositMerchantConnectorClient
 * @package Pyz\Client\DepositMerchantConnector
 * @method DepositMerchantConnectorFactory getFactory()
 */
class DepositMerchantConnectorClient extends AbstractClient implements DepositMerchantConnectorClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     *
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): iterable
    {
        return $this
            ->getFactory()
            ->createDepositMerchantConnectorStub()
            ->getDepositsForBranch($branchTransfer);
    }
}
