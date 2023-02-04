<?php
/**
 * Durst - project - BillingReferenceGenerator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-19
 * Time: 16:49
 */

namespace Pyz\Zed\Billing\Business\Generator;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Pyz\Zed\Billing\BillingConfig;
use Pyz\Zed\Billing\Dependency\Facade\BillingToMerchantBridgeInterface;
use Pyz\Zed\Billing\Dependency\Facade\BillingToSequenceNumberBridgeInterface;

class BillingReferenceGenerator implements BillingReferenceGeneratorInterface
{
    public const BILLING_PERIOD_FOR_BRANCH_REF_PREFIX = 'BRA';
    public const BILLING_PERIOD_FOR_MERCHANT_REF_PREFIX = 'MER';


    /**
     * @var BillingConfig
     */
    protected $config;

    /**
     * @var BillingToMerchantBridgeInterface
     */
    protected $merchantFacade;

    /**
     * @var BillingToSequenceNumberBridgeInterface
     */
    protected $sequenceNumberFacade;

    /**
     * BillingReferenceGenerator constructor.
     *
     * @param BillingConfig $config
     * @param BillingToMerchantBridgeInterface $merchantFacade
     * @param BillingToSequenceNumberBridgeInterface $sequenceNumberFacade
     */
    public function __construct(
        BillingConfig $config,
        BillingToMerchantBridgeInterface $merchantFacade,
        BillingToSequenceNumberBridgeInterface $sequenceNumberFacade
    ) {
        $this->config = $config;
        $this->merchantFacade = $merchantFacade;
        $this->sequenceNumberFacade = $sequenceNumberFacade;
    }

    /**
     * @param int $idBranch
     * @return string
     */
    public function createBillingReferenceFromBranchId(int $idBranch): string
    {
        $branch = $this
            ->merchantFacade
            ->getBranchById($idBranch);

        $merchant = $this
            ->merchantFacade
            ->getMerchantById($branch->requireFkMerchant()->getFkMerchant());

        $billingPeriodIdentifier = $this
            ->getMerchantOrBranchBillingPeriodIdentifier($branch, $merchant);

        return $this
            ->sequenceNumberFacade
            ->generate(
                $this
                    ->config
                    ->getBillingSequenceNumberSettingsTransfer($billingPeriodIdentifier)
            );
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @param MerchantTransfer $merchantTransfer
     * @return string
     */
    protected function getMerchantOrBranchBillingPeriodIdentifier(BranchTransfer $branchTransfer, MerchantTransfer $merchantTransfer): string
    {
        if($merchantTransfer->getBillingPeriodPerBranch() === true){
            return sprintf(
                '%s-%d',
                self::BILLING_PERIOD_FOR_BRANCH_REF_PREFIX,
                $branchTransfer->getIdBranch()
            );
        }

        return sprintf(
            '%s-%d',
            self::BILLING_PERIOD_FOR_MERCHANT_REF_PREFIX,
            $branchTransfer->requireFkMerchant()->getFkMerchant()
        );
    }
}
