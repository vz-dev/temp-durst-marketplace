<?php
/**
 * Durst - project - MerchantStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 15:47
 */

namespace Pyz\Client\Merchant\Zed;

use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GetBranchesRequestTransfer;
use Generated\Shared\Transfer\GetBranchesResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class MerchantStub implements MerchantStubInterface
{
    protected const URL_GET_MERCHANT_BY_MERCHANT_PIN = '/merchant/gateway/get-merchant-by-merchant-pin';
    protected const URL_GET_BRANCH_BY_ID = '/merchant/gateway/get-branch-by-id';
    protected const URL_GET_BRANCHES_FOR_MERCHANT = '/merchant/gateway/get-branches-for-merchant';
    protected const URL_GET_BRANCHES = '/merchant/gateway/get-branches';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedStub;

    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedStub
     */
    public function __construct(
        ZedRequestClientInterface $zedStub
    )
    {
        $this->zedStub = $zedStub;
    }

    /**
     * @param \Generated\Shared\Transfer\GetBranchesRequestTransfer $transfer
     * @return \Generated\Shared\Transfer\GetBranchesResponseTransfer|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    public function getBranches(GetBranchesRequestTransfer $transfer): GetBranchesResponseTransfer
    {
        return $this
            ->zedStub
            ->call(
                self::URL_GET_BRANCHES,
                $transfer,
                null
            );
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer|string $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantPin(string $merchantPin): MerchantTransfer
    {
        $merchantTransfer = (new MerchantTransfer())
            ->setMerchantPin($merchantPin);

        /** @var \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer */
        $merchantTransfer = $this
            ->zedStub
            ->call(
                self::URL_GET_MERCHANT_BY_MERCHANT_PIN,
                $merchantTransfer
            );

        return $merchantTransfer;
    }

    /**
     * @param int $idBranch
     *
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchById(int $idBranch): BranchTransfer
    {
        $branchTransfer = (new AppApiRequestTransfer())
            ->setIdBranch($idBranch);

        /** @var \Generated\Shared\Transfer\BranchTransfer $branchTransfer */
        $branchTransfer = $this
            ->zedStub
            ->call(
                self::URL_GET_BRANCH_BY_ID,
                $branchTransfer
            );

        return $branchTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return iterable
     */
    public function getBranchesForMerchant(MerchantTransfer $merchantTransfer): iterable
    {
        /** @var \Generated\Shared\Transfer\BranchCollectionTransfer $branchCollection */
        $branchCollection = $this
            ->zedStub
            ->call(
                self::URL_GET_BRANCHES_FOR_MERCHANT,
                $merchantTransfer
            );

        return $branchCollection->getBranches();
    }
}
