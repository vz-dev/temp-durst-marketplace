<?php
/**
 * Durst - project - TimeSlotConditionChecker.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 03.04.19
 * Time: 13:39
 */

namespace Pyz\Zed\Tour\Business\Checkout;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Pyz\Shared\Tour\TourConstants;
use Pyz\Zed\DeliveryArea\Business\DeliveryAreaFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\SoftwarePackage\Business\SoftwarePackageFacadeInterface;
use Pyz\Zed\Tour\Business\Model\ConcreteTourInterface;

class TimeSlotConditionChecker implements TimeSlotConditionCheckerInterface
{
    protected const ERROR_MESSAGE = 'The chosen time slot is not linked to a tour';
    protected const ERROR_CODE = 'T000001';

    /**
     * @var DeliveryAreaFacadeInterface
     */
    protected $deliveryAreaFacade;

    /**
     * @var SoftwarePackageFacadeInterface
     */
    protected $softwarePackageFacade;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var ConcreteTourInterface
     */
    protected $concreteTourModel;

    /**
     * TimeSlotConditionChecker constructor.
     * @param DeliveryAreaFacadeInterface $deliveryAreaFacade
     * @param SoftwarePackageFacadeInterface $softwarePackageFacade
     * @param MerchantFacadeInterface $merchantFacade
     * @param ConcreteTourInterface $concreteTourModel
     */
    public function __construct(
        DeliveryAreaFacadeInterface $deliveryAreaFacade,
        SoftwarePackageFacadeInterface $softwarePackageFacade,
        MerchantFacadeInterface $merchantFacade,
        ConcreteTourInterface               $concreteTourModel
    )
    {
        $this->deliveryAreaFacade = $deliveryAreaFacade;
        $this->softwarePackageFacade = $softwarePackageFacade;
        $this->merchantFacade = $merchantFacade;
        $this->concreteTourModel = $concreteTourModel;
    }

    /**
     * {@inheritdoc}
     *
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkConcreteTimeSlotHasConcreteTour(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {

        if($quoteTransfer->getUseFlexibleTimeSlots() === true)
        {
            return true;
        }

        $this
            ->assertRequirements($quoteTransfer);

        if (
            $this->checkSoftwarePackageIsWholesale($quoteTransfer) !== true ||
            $this->checkBranchHasOrderOnTimeslotSet($quoteTransfer) === true
        ) {
            return true;
        }

        $concreteTimeSlotTransfer = $this
            ->deliveryAreaFacade
            ->getConcreteTimeSlotById($quoteTransfer->getFkConcreteTimeSlot());

        if ($concreteTimeSlotTransfer->getFkConcreteTour() === null) {
            $this->prepareResponseTransfer($checkoutResponseTransfer);

            return false;
        }

        return $this
            ->checkConcreteTourIsNotDeleted($concreteTimeSlotTransfer->getFkConcreteTour(), $checkoutResponseTransfer);
    }

    /**
     * @param int $idConcreteTour
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    protected function checkConcreteTourIsNotDeleted(
        int $idConcreteTour,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool
    {
        $tour = $this
            ->concreteTourModel
            ->getConcreteTourById($idConcreteTour);

        if($tour === null || $tour->getStatus() === null || $tour->getStatus() === TourConstants::TOUR_STATE_DELETED){
            $this->prepareResponseTransfer($checkoutResponseTransfer);

            return false;
        }

        return true;
    }

    /**
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return CheckoutResponseTransfer
     */
    protected function prepareResponseTransfer(CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        $errorMessageTransfer = (new CheckoutErrorTransfer())
            ->setMessage(static::ERROR_MESSAGE)
            ->setErrorCode(static::ERROR_CODE);

        $checkoutResponseTransfer
            ->addError($errorMessageTransfer)
            ->setIsSuccess(false);

        return $checkoutResponseTransfer;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function assertRequirements(QuoteTransfer $quoteTransfer): void
    {
        $quoteTransfer
            ->requireFkConcreteTimeSlot()
            ->requireFkBranch();
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function checkSoftwarePackageIsWholesale(QuoteTransfer $quoteTransfer): bool
    {
        $branchTransfer = $this
            ->merchantFacade
            ->getBranchById($quoteTransfer->getFkBranch());

        $branchTransfer->requireFkMerchant();

        return $this
            ->softwarePackageFacade
            ->hasMerchantWholesalePackage($branchTransfer->getFkMerchant());
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return bool
     */
    protected function checkBranchHasOrderOnTimeslotSet(QuoteTransfer $quoteTransfer): bool
    {
        $branchTransfer = $this
            ->merchantFacade
            ->getBranchById(
                $quoteTransfer
                    ->getFkBranch()
            );

        return ($branchTransfer->getOrderOnTimeslot() === true);
    }
}
