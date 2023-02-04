<?php


namespace Pyz\Zed\Driver\Business\Model\Hydrator;


use Generated\Shared\Transfer\DriverTransfer;
use Orm\Zed\Driver\Persistence\DstDriver;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;

class BranchHydrator implements BranchHydratorInterface
{
    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * BranchHydrator constructor.
     *
     * @param \Pyz\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(MerchantFacadeInterface $merchantFacade)
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Orm\Zed\Driver\Persistence\DstDriver $driverEntity
     * @param \Generated\Shared\Transfer\DriverTransfer $driverTransfer
     * @return void
     */
    public function hydrateDriverByBranch(
        DstDriver $driverEntity,
        DriverTransfer $driverTransfer
    ) {
        $branchTransfer = $this
            ->merchantFacade
            ->getBranchById($driverEntity->getFkBranch());

        $driverTransfer->setBranch($branchTransfer);
    }
}
