<?php
/**
 * Durst - project - DepositMerchantConnectorStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-29
 * Time: 12:44
 */

namespace Pyz\Client\DepositMerchantConnector\Zed;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DriverAppApiRequestTransfer;
use Generated\Shared\Transfer\DriverAppApiResponseTransfer;
use Spryker\Client\ZedRequest\ZedRequestClientInterface;

class DepositMerchantConnectorStub implements DepositMerchantConnectorStubInterface
{
    protected const URL_GET_DEPOSITS_FOR_BRANCH = '/deposit-merchant-connector/gateway/get-deposits-for-branch';

    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * DepositMerchantConnectorStub constructor.
     *
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient
     */
    public function __construct(ZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \Generated\Shared\Transfer\DepositTransfer[]
     */
    public function getDepositsForBranch(BranchTransfer $branchTransfer): iterable
    {
        $requestTransfer = (new DriverAppApiRequestTransfer())
            ->setBranch($branchTransfer);

        /** @var DriverAppApiResponseTransfer $responseTransfer */
        $responseTransfer = $this
            ->zedRequestClient
            ->call(
                self::URL_GET_DEPOSITS_FOR_BRANCH,
                $requestTransfer,
                null
            );


        return $responseTransfer
            ->getDeposits();
    }
}
