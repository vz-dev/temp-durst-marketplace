<?php

namespace Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry;

use DateTime;
use DateTimeZone;
use Exception;
use Generated\Shared\Transfer\DepositPickupInquiryTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Orm\Zed\DepositPickup\Persistence\DstDepositPickupInquiry;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DepositPickup\Communication\Plugin\Mail\DepositPickupInquiryNotificationMailTypePlugin;
use Pyz\Zed\DepositPickup\DepositPickupConfig;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Throwable;

class DepositPickupInquirySaver implements DepositPickupInquirySaverInterface
{
    use TransactionTrait;

    /**
     * @var DepositPickupConfig
     */
    protected $config;

    /**
     * @var MerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var MailFacadeInterface
     */
    protected $mailFacade;

    /**
     * @param MerchantFacadeInterface $merchantFacade
     * @param MailFacadeInterface $mailFacade
     */
    public function __construct(DepositPickupConfig $config, MerchantFacadeInterface $merchantFacade, MailFacadeInterface $mailFacade)
    {
        $this->config = $config;
        $this->merchantFacade = $merchantFacade;
        $this->mailFacade = $mailFacade;
    }

    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     *
     * @throws Throwable
     */
    public function saveDepositPickupInquiry(DepositPickupInquiryTransfer $inquiryTransfer): void
    {
        $this->assertDepositPickupInquiryRequirements($inquiryTransfer);

        $this->getTransactionHandler()->handleTransaction(function () use ($inquiryTransfer) {
            $inquiryEntity = $this->saveDepositPickupInquiryEntity($inquiryTransfer);

            $inquiryTransfer->setIdDepositPickupInquiry($inquiryEntity->getIdDepositPickupInquiry());
        });

        $this->sendNotificationMail($inquiryTransfer);
    }

    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     */
    protected function sendNotificationMail(DepositPickupInquiryTransfer $inquiryTransfer): void
    {
        $branchTransfer = $this
            ->merchantFacade
            ->getBranchById($inquiryTransfer->getFkBranch());

        $mailTransfer = (new MailTransfer())
            ->setType(DepositPickupInquiryNotificationMailTypePlugin::NAME)
            ->setEmail($branchTransfer->getEmail())
            ->setMerchantCenterBaseUrl($this->config->getMerchantCenterBaseUrl())
            ->setIdDepositPickupInquiry($inquiryTransfer->getIdDepositPickupInquiry());

        $this->mailFacade->handleMail($mailTransfer);
    }

    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     */
    protected function assertDepositPickupInquiryRequirements(DepositPickupInquiryTransfer $inquiryTransfer): void
    {
        $inquiryTransfer
            ->requireFkBranch()
            ->requireName()
            ->requireAddress()
            ->requireEmail()
            ->requirePhoneNumber()
            ->requirePreferredDate()
            ->requireMessage();
    }

    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     *
     * @return DstDepositPickupInquiry
     *
     * @throws PropelException
     */
    protected function saveDepositPickupInquiryEntity(DepositPickupInquiryTransfer $inquiryTransfer): DstDepositPickupInquiry
    {
        $inquiryEntity = new DstDepositPickupInquiry();

        $this->hydrateInquiryEntity($inquiryTransfer, $inquiryEntity);

        $inquiryEntity->save();

        return $inquiryEntity;
    }

    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     * @param DstDepositPickupInquiry $inquiryEntity
     *
     * @throws Exception
     */
    protected function hydrateInquiryEntity(DepositPickupInquiryTransfer $inquiryTransfer, DstDepositPickupInquiry $inquiryEntity): void
    {
        $inquiryEntity
            ->setFkBranch($inquiryTransfer->getFkBranch())
            ->setName($inquiryTransfer->getName())
            ->setAddress($inquiryTransfer->getAddress())
            ->setEmail($inquiryTransfer->getEmail())
            ->setPhoneNumber($inquiryTransfer->getPhoneNumber())
            ->setPreferredDate((new DateTime($inquiryTransfer->getPreferredDate()))->setTimezone(new DateTimeZone('UTC')))
            ->setMessage($inquiryTransfer->getMessage());
    }
}
