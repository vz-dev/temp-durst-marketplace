<?php
/**
 * Durst - project - AccountingToMerchantBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:31
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use Generated\Shared\Transfer\MerchantTransfer;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class AccountingToMerchantBridge implements AccountingToMerchantBridgeInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * AccountingToMerchantBridge constructor.
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        MerchantFacadeInterface $merchantFacade
    )
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\BranchTransfer[]
     */
    public function getBranchesByIdMerchant(int $idMerchant): array
    {
        return $this
            ->merchantFacade
            ->getBranchesByIdMerchant($idMerchant);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer[]
     */
    public function getMerchants(): array
    {
        return $this
            ->merchantFacade
            ->getMerchants();
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idMerchant
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantById(int $idMerchant): MerchantTransfer
    {
        return $this
            ->merchantFacade
            ->getMerchantById($idMerchant);
    }
}
