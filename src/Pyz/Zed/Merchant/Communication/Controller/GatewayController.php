<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-15
 * Time: 15:53
 */

namespace Pyz\Zed\Merchant\Communication\Controller;

use ArrayObject;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\BranchCollectionTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\GetBranchesResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Pyz\Zed\Merchant\Business\Exception\BranchInactiveException;
use Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Merchant\Communication\MerchantCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\Merchant\Communication
 * @method MerchantFacadeInterface getFacade()
 * @method MerchantCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchantByMerchantPinAction(MerchantTransfer $merchantTransfer)
    {
        return $this
            ->getFacade()
            ->getMerchantByMerchantPin($merchantTransfer->getMerchantPin());
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\BranchCollectionTransfer
     */
    public function getBranchesForMerchantAction(MerchantTransfer $merchantTransfer)
    {
        $branches = $this
            ->getFacade()
            ->getBranchesByIdMerchant($merchantTransfer->getIdMerchant());

        return (new BranchCollectionTransfer())
            ->setBranches(
                new ArrayObject($branches)
            );
    }

    //ToDo fix dependency to AppApiTransfers
    /**
     * @return GetBranchesResponseTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getBranchesAction(): GetBranchesResponseTransfer
    {
        $branches = $this
            ->getFacade()
            ->getBranches();

        $response = new GetBranchesResponseTransfer();
        $response->setBranches(new \ArrayObject($branches));

        return $response;
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\DeliveryArea\Business\Exception\DeliveryAreaNotFoundException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchesByZipCodeAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $branches = $this
            ->getFacade()
            ->getBranchesByZipCode($requestTransfer->getZipCode());

        $responseTransfer = new AppApiResponseTransfer();
        $responseTransfer->setBranches(new \ArrayObject($branches));

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\AppApiResponseTransfer
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchNotFoundException
     */
    public function getBranchByCodeAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {

        $responseTransfer = new AppApiResponseTransfer();

        try {
            $branch = $this
                ->getFacade()
                ->getBranchByCode($requestTransfer->getCode());

            $responseTransfer->setBranch($branch);
        } catch (BranchNotFoundException|BranchInactiveException $exception){
            $responseTransfer->setBranch(null);
        }

        return $responseTransfer;
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return \Generated\Shared\Transfer\BranchTransfer
     */
    public function getBranchByIdAction(AppApiRequestTransfer $requestTransfer): BranchTransfer
    {
        return $this
            ->getFacade()
            ->getBranchById($requestTransfer->getIdBranch());
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getPaymentMethodsForBranchesAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $paymentMethodTransfers = $this
            ->getFacade()
            ->getSupportedPaymentMethodsForBranches($requestTransfer->getBranchIds());

        return (new AppApiResponseTransfer())
            ->setPaymentMethods(new \ArrayObject($paymentMethodTransfers));
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     */
    public function getPaymentMethodsAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $paymentMethodTransfers = $this
            ->getFacade()
            ->getPaymentMethods();

        return (new AppApiResponseTransfer())
            ->setPaymentMethods(new \ArrayObject($paymentMethodTransfers));
    }

    /**
     * @param AppApiRequestTransfer $requestTransfer
     * @return AppApiResponseTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function getBranchByIdAndZipCodeAction(AppApiRequestTransfer $requestTransfer): AppApiResponseTransfer
    {
        $requestTransfer->requireIdBranch()->requireZipCode();

        $branchTransfer = $this
            ->getFacade()
            ->getBranchByIdAndZipCode($requestTransfer->getIdBranch(), $requestTransfer->getZipCode());

        $responseTransfer = (new AppApiResponseTransfer())
            ->setBranch($branchTransfer);

        return $responseTransfer;
    }
}
