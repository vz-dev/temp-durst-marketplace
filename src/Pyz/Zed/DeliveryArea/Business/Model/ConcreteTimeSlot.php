<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 16.10.17
 * Time: 15:51
 */

namespace Pyz\Zed\DeliveryArea\Business\Model;


use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\DeliveryArea\Persistence\SpyConcreteTimeSlot;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DeliveryArea\Business\Exception\ConcreteTimeSlotNotFoundException;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxPayloadAssertion;
use Pyz\Zed\DeliveryArea\Business\Model\Assertion\MaxProductsAssertion;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotDeletePluginInterface;
use Pyz\Zed\DeliveryArea\Communication\Plugin\PostConcreteTimeSlotSavePluginInterface;
use Pyz\Zed\DeliveryArea\DeliveryAreaConfig;
use Pyz\Zed\DeliveryArea\Persistence\DeliveryAreaQueryContainerInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

class ConcreteTimeSlot
{
    public const ERROR_CONCRETE_TIME_SLOT_INVALID = 'The concrete time slot is not valid anymore.';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_INVALID = 5678;

    public const ERROR_ZIP_CODE_MISMATCH = 'The shipping addresses zip code doesn\'t match the time slots zip code';
    public const ERROR_CODE_ZIP_CODE_MISMATCH = 5679;

    public const ERROR_CONCRETE_TIME_SLOT_PAYLOAD_INVALID = 'The concrete time slot is not valid anymore. (max. payload)';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_PAYLOAD_INVALID = 5680;

    public const ERROR_CONCRETE_TIME_SLOT_MAX_PRODUCTS_INVALID = 'The concrete time slot is not valid anymore. (max. products)';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_MAX_PRODUCTS_INVALID = 5681;

    public const ERROR_CONCRETE_TIME_SLOTS_EMPTY = 'There are no concrete time slots inside the quote.';
    public const ERROR_CODE_CONCRETE_TIME_SLOTS_EMPTY = 5682;

    public const ERROR_CONCRETE_TIME_SLOT_DOES_NOT_BELONG_TO_BRANCH = 'The concrete timeslot does not belong to the selected branch.';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_DOES_NOT_BELONG_TO_BRANCH = 5683;

    public const ERROR_CONCRETE_TIME_SLOT_MIN_VALUE_INVALID = 'The concrete time slot is not valid anymore. (min. value)';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_MIN_VALUE_INVALID = 5684;

    public const ERROR_CONCRETE_TIME_SLOT_MIN_UNITS_INVALID = 'The concrete time slot is not valid anymore. (min. units)';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_MIN_UNITS_INVALID = 5685;

    public const ERROR_CONCRETE_TIME_SLOT_CUSTOMERS_INVALID = 'The concrete time slot is not valid anymore. (max. customers)';
    public const ERROR_CODE_CONCRETE_TIME_SLOT_CUSTOMERS_INVALID = 5686;

    public const SUCCESS_CODE = 5200;

    /**
     * @var DeliveryAreaQueryContainerInterface
     */
    protected $queryContainer;

    /** @var DeliveryAreaConfig */
    protected $config;

    /**
     * @var ConcreteTimeSlotAssertionInterface
     */
    protected $assertionChecker;

    /**
     * @var MaxPayloadAssertion
     */
    protected $maxPayloadAssertion;

    /**
     * @var MaxProductsAssertion
     */
    protected $maxProductsAssertion;

    /**
     * @var PostConcreteTimeSlotSavePluginInterface[]
     */
    protected $concreteTimeSlotSavePlugins;

    /**
     * @var PostConcreteTimeSlotDeletePluginInterface[]
     */
    protected $concreteTimeSlotDeletePlugins;

    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * ConcreteTimeSlot constructor.
     * @param DeliveryAreaQueryContainerInterface $queryContainer
     * @param DeliveryAreaConfig $config
     * @param ConcreteTimeSlotAssertionInterface $assertionChecker
     * @param MaxPayloadAssertion $maxPayloadAssertion
     * @param MaxProductsAssertion $maxProductsAssertion
     * @param array $concreteTimeSlotSavePlugins
     * @param array $concreteTimeSlotDeletePlugins
     * @param TourFacadeInterface $tourFacade
     */
    public function __construct(
        DeliveryAreaQueryContainerInterface $queryContainer,
        DeliveryAreaConfig $config,
        ConcreteTimeSlotAssertionInterface $assertionChecker,
        MaxPayloadAssertion $maxPayloadAssertion,
        MaxProductsAssertion $maxProductsAssertion,
        array $concreteTimeSlotSavePlugins,
        array $concreteTimeSlotDeletePlugins,
        TourFacadeInterface $tourFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->assertionChecker = $assertionChecker;
        $this->maxPayloadAssertion = $maxPayloadAssertion;
        $this->maxProductsAssertion = $maxProductsAssertion;
        $this->concreteTimeSlotSavePlugins = $concreteTimeSlotSavePlugins;
        $this->concreteTimeSlotDeletePlugins = $concreteTimeSlotDeletePlugins;
        $this->tourFacade = $tourFacade;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     * @throws ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function checkConcreteTimeSlotAssertions(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) : bool
    {
        if($quoteTransfer->getUseFlexibleTimeSlots() === true)
        {
            $checkoutResponseTransfer
                ->setIsSuccess(true);
            return true;
        }

        $entity = $this
            ->getConcreteTimeSlotEntityById($quoteTransfer->getFkConcreteTimeSlot());

        if($this->assertionChecker->isValid($entity) !== true){
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError($this->createErrorTransfer(
                        self::ERROR_CONCRETE_TIME_SLOT_INVALID,
                        self::ERROR_CODE_CONCRETE_TIME_SLOT_INVALID
                    )
                );

            $this->runConcreteTimeSlotDeletePlugins($entity);

            return false;
        }

        if($this->checkQuoteMinValue($quoteTransfer) !== true) {
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError($this->createErrorTransfer(
                    self::ERROR_CONCRETE_TIME_SLOT_MIN_VALUE_INVALID,
                    self::ERROR_CODE_CONCRETE_TIME_SLOT_MIN_VALUE_INVALID
                    )
                );

            return false;
        }

        if($this->checkQuoteMinUnits($quoteTransfer) !== true) {
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError($this->createErrorTransfer(
                    self::ERROR_CONCRETE_TIME_SLOT_MIN_UNITS_INVALID,
                    self::ERROR_CODE_CONCRETE_TIME_SLOT_MIN_UNITS_INVALID
                )
                );

            return false;
        }

        if($this->maxPayloadAssertion->isValid($entity, $quoteTransfer) !== true)
        {
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError($this->createErrorTransfer(
                    self::ERROR_CONCRETE_TIME_SLOT_PAYLOAD_INVALID,
                    self::ERROR_CODE_CONCRETE_TIME_SLOT_PAYLOAD_INVALID
                    )
                );
            return false;
        }

        if($this->assertConcreteTimeSlotBelongsToBranch($entity, $quoteTransfer) !== true)
        {
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError($this->createErrorTransfer(
                    self::ERROR_CONCRETE_TIME_SLOT_DOES_NOT_BELONG_TO_BRANCH,
                    self::ERROR_CODE_CONCRETE_TIME_SLOT_DOES_NOT_BELONG_TO_BRANCH
                )
                );
            return false;
        }

        return true;
    }

    /**
     * @param CartChangeTransfer $cartChangeTransfer
     * @return CartPreCheckResponseTransfer
     */
    public function validateConcreteTimeSlotAssertions(CartChangeTransfer $cartChangeTransfer) : CartPreCheckResponseTransfer
    {
        $concreteTimeSlotTransfers = $cartChangeTransfer
            ->getQuote()
            ->getConcreteTimeSlots();

        $cartResponse = new CartPreCheckResponseTransfer();
        $cartResponse
            ->setIsSuccess(true);

        if($cartChangeTransfer->getQuote()->getUseFlexibleTimeSlots() === true)
        {
            return $cartResponse;
        }

        if ($concreteTimeSlotTransfers->count() < 1) {
            $cartResponse
                ->setIsSuccess(false);
            $cartResponse
                ->addMessage(
                    (new MessageTransfer())
                    ->setValue(self::ERROR_CONCRETE_TIME_SLOTS_EMPTY)
                );
        }

        foreach ($concreteTimeSlotTransfers as $concreteTimeSlotTransfer) {
            $entity = $this
                ->getConcreteTimeSlotEntityById($concreteTimeSlotTransfer->getIdConcreteTimeSlot());

            if($this->assertionChecker->isValid($entity) !== true){

                $cartResponse->setIsSuccess(false);
                $cartResponse->addMessage((new MessageTransfer())->setValue(self::ERROR_CONCRETE_TIME_SLOT_INVALID));

                $this->runConcreteTimeSlotDeletePlugins($entity);
            }
        }

        return $cartResponse;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return bool
     */
    protected function checkQuoteMinValue(QuoteTransfer $quoteTransfer) : bool
    {
        $quoteTransfer->requireTotals()->getTotals()->getGrossSubtotal();
        return (($quoteTransfer->getTotals()->getGrossSubtotal() + $quoteTransfer->getTotals()->getDiscountTotal()) >= $quoteTransfer->getMinValue());
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @return bool
     */
    protected function checkQuoteMinUnits(QuoteTransfer $quoteTransfer) : bool
    {
        $quoteTransfer->requireTotals()->getTotals()->getMissingMinUnitsTotal();
        return ($quoteTransfer->getTotals()->getMissingMinUnitsTotal() < 1);
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     * @throws ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function checkZipCodeCondition(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer) : bool
    {
        if($quoteTransfer->getUseFlexibleTimeSlots() === true){
            return true;
        }

        $concreteTimeSlotEntity = $this
            ->getConcreteTimeSlotEntityById($quoteTransfer->getFkConcreteTimeSlot());

        $timeSlotZipCode = $concreteTimeSlotEntity->getSpyTimeSlot()->getSpyDeliveryArea()->getZipCode();
        $deliveryAddressZipCode = $quoteTransfer->getShippingAddress()->getZipCode();

        if($timeSlotZipCode != $deliveryAddressZipCode){
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError(
                    $this->createErrorTransfer(
                        self::ERROR_ZIP_CODE_MISMATCH,
                        self::ERROR_CODE_ZIP_CODE_MISMATCH
                    )
                );

            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @param int $code
     * @return CheckoutErrorTransfer
     */
    protected function createErrorTransfer(string $message, int $code) : CheckoutErrorTransfer
    {
        return (new CheckoutErrorTransfer())
            ->setErrorCode($code)
            ->setMessage($message);
    }

    /**
     * @param int $idConcreteTimeSlot
     * @return SpyConcreteTimeSlot
     * @throws ConcreteTimeSlotNotFoundException
     */
    protected function getConcreteTimeSlotEntityById(int $idConcreteTimeSlot) : SpyConcreteTimeSlot
    {
        $entity = $this
            ->queryContainer
            ->queryConcreteTimeSlotById($idConcreteTimeSlot)
            ->findOne();

        if($entity === null){
            throw new ConcreteTimeSlotNotFoundException(
                sprintf(
                    ConcreteTimeSlotNotFoundException::NOT_FOUND,
                    $idConcreteTimeSlot
                )
            );
        }

        return $entity;
    }

    /**
     * @param int $idBranch
     * @param DateTime $start
     * @param DateTime $end
     * @return ConcreteTimeSlotTransfer
     * @throws PropelException
     */
    public function getConcreteTimeSlotByIdBranchAndStartAndEnd(
        int $idBranch, DateTime $start, DateTime $end
    ) : ConcreteTimeSlotTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryConcreteTimeSlotForBranchByStartAndEnd($idBranch, $start, $end)
            ->findOneOrCreate();

        if($entity->isNew()){
            $concreteTourTransfer = $this->tourFacade->createConcreteTourForConcreteTimeSlot($this
                ->entityToTransfer($entity));
            if ($concreteTourTransfer !== null){
                $entity->setFkConcreteTour($concreteTourTransfer->getIdConcreteTour());
            }
            $entity->save();
            $this->runConcreteTimeSlotSavePlugins($entity);
        }

        return $this
            ->entityToTransfer($entity);
    }

    /**
     * @param $idConcreteTimeSlot
     * @return ConcreteTimeSlotTransfer
     * @throws ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function getConcreteTimeSlotById($idConcreteTimeSlot) : ConcreteTimeSlotTransfer
    {
        $concreteTimeSlotEntity = $this
            ->queryContainer
            ->queryConcreteTimeSlotById($idConcreteTimeSlot)
            ->findOne();


        if($concreteTimeSlotEntity === null){
            throw new ConcreteTimeSlotNotFoundException(sprintf(ConcreteTimeSlotNotFoundException::NOT_FOUND, $idConcreteTimeSlot));
        }

        return $this->entityToTransfer($concreteTimeSlotEntity);
    }

    /**
     * @param ConcreteTimeSlotTransfer $concreteTimeSlotTransfer
     * @return ConcreteTimeSlotTransfer
     * @throws ConcreteTimeSlotNotFoundException
     * @throws PropelException
     */
    public function setFkConcreteTourInConcreteTimeSlot(ConcreteTimeSlotTransfer $concreteTimeSlotTransfer) : ConcreteTimeSlotTransfer
    {
        $concreteTimeSlotEntity = $this
            ->getConcreteTimeSlotEntityById($concreteTimeSlotTransfer->getIdConcreteTimeSlot());

        $concreteTimeSlotEntity->setFkConcreteTour($concreteTimeSlotTransfer->getFkConcreteTour());
        $concreteTimeSlotEntity->save();

        $this->runConcreteTimeSlotSavePlugins($concreteTimeSlotEntity);

        return $this->entityToTransfer($concreteTimeSlotEntity);
    }

    /**
     * @param SpyConcreteTimeSlot $entity
     * @return ConcreteTimeSlotTransfer
     * @throws PropelException
     */
    public function entityToTransfer(SpyConcreteTimeSlot $entity) : ConcreteTimeSlotTransfer
    {
        $transfer = new ConcreteTimeSlotTransfer();
        $transfer->fromArray($entity->toArray(), true);
        $transfer->setTimeFormat($this->config->getDateTimeFormat());

        if($entity->getStartTime() !== null) {
            $startTime = $entity->getStartTime()->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setStartTime($startTime->format($this->config->getDateTimeFormat()));
        }
        if($entity->getEndTime() !== null) {
            $endTime = $entity->getEndTime()->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()));
            $transfer->setEndTime($endTime->format($this->config->getDateTimeFormat()));
        }

        return $transfer;
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlot
     * @return void
     */
    protected function runConcreteTimeSlotSavePlugins(SpyConcreteTimeSlot $concreteTimeSlot)
    {
        foreach ($this->concreteTimeSlotSavePlugins as $savePlugin)
        {
            $savePlugin->save($concreteTimeSlot);
        }
    }

    /**
     * @param SpyConcreteTimeSlot $concreteTimeSlot
     * @return void
     */
    protected function runConcreteTimeSlotDeletePlugins(SpyConcreteTimeSlot $concreteTimeSlot)
    {
        foreach ($this->concreteTimeSlotDeletePlugins as $deletePlugin)
        {
            $deletePlugin->delete($concreteTimeSlot);
        }
    }

    /**
     * @param SpyConcreteTimeSlot $entity
     * @param QuoteTransfer $quoteTransfer
     * @return bool
     * @throws PropelException
     */
    protected function assertConcreteTimeSlotBelongsToBranch(SpyConcreteTimeSlot $entity, QuoteTransfer $quoteTransfer) : bool
    {
        return intval($entity->getSpyTimeSlot()->getFkBranch()) === intval($quoteTransfer->getFkBranch());
    }
}
