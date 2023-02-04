<?php
/**
 * Durst - project - MerchantClient.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 15:45
 */

namespace Pyz\Client\Merchant;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GetBranchesRequestTransfer;
use Generated\Shared\Transfer\GetBranchesResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * Class MerchantClient
 * @package Pyz\Client\Merchant
 * @method MerchantFactory getFactory()
 */
class MerchantClient extends AbstractClient implements MerchantClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\GetBranchesRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\GetBranchesResponseTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranches(GetBranchesRequestTransfer $transfer): GetBranchesResponseTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantStub()
            ->getBranches(
                $transfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer|string $merchantTransfer
     * @return \Generated\Shared\Transfer\MerchantTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantStub()
            ->getMerchantByMerchantPin($merchantPin);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return iterable
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranchesForMerchant(MerchantTransfer $merchantTransfer): iterable
    {
        return $this
            ->getFactory()
            ->createMerchantStub()
            ->getBranchesForMerchant($merchantTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     * @throws \Spryker\Client\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranchById(int $idBranch): BranchTransfer
    {
        return $this
            ->getFactory()
            ->createMerchantStub()
            ->getBranchById($idBranch);
    }
}
