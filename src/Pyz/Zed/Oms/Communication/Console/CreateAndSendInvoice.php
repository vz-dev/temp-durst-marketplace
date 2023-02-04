<?php
/**
 * Durst - project - CreateAndSendInvoice.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-04-15
 * Time: 15:45
 */

namespace Pyz\Zed\Oms\Communication\Console;

use DateTime;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Oms\Business\OmsFacade;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceMailTypePlugin;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class CreateAndSendInvoice
 * @package Pyz\Zed\Oms\Communication\Console
 *
 * @method OmsFacade getFacade()
 * @method OmsCommunicationFactory getFactory()
 */
class CreateAndSendInvoice extends Console
{
    public const COMMAND_NAME = 'oms:create-send-invoice-by-id-with-delivery-date';
    public const DESCRIPTION = 'This command will create and send a invoice for the order with the given id, set the invoice created date to the delivery date and update the corresponding billing period';

    public const OPTION_SALES_ORDER_ID = 'sales_order-id';
    public const OPTION_SALES_ORDER_ID_DESCRIPTION = 'The Id of the sales order for which a invoice should be created and sent';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:create-send-invoice']);
        $this->addArgument(self::OPTION_SALES_ORDER_ID, InputArgument::REQUIRED, self::OPTION_SALES_ORDER_ID_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::DESCRIPTION);

        $idSalesOrder = $input->getArgument(self::OPTION_SALES_ORDER_ID);

        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getDeflatedOrderByIdSalesOrder($idSalesOrder);

        if($orderTransfer->getInvoiceReference() !== null || $orderTransfer->getInvoiceCreatedAt() !== null)
        {
            $this->error("The given Sales Order already has a invoice reference or invoice created at");
            return;
        }

        $orderTransfer
            ->setInvoiceReference(
                $this
                    ->getFactory()
                    ->getInvoiceFacade()
                    ->createInvoiceReference($orderTransfer)
            )
            ->setInvoiceCreatedAt($orderTransfer->getConcreteTimeSlot()->getEndTime());

        $this
            ->getFactory()
            ->getSalesFacade()
            ->updateOrder($orderTransfer, $idSalesOrder);

        $this->updateBillingPeriod($orderTransfer);

        $this
            ->getFacade()
            ->sendInvoiceMail($orderTransfer, $orderTransfer->getBranch(), MerchantOrderInvoiceMailTypePlugin::MAIL_TYPE);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return void
     */
    protected function updateBillingPeriod(OrderTransfer $orderTransfer): void
    {
        $fkBranch = $orderTransfer->getFkBranch();

        $isBillingPeriodMissing = $this
            ->isBillingPeriodMissing(
                $orderTransfer
            );

        if ($isBillingPeriodMissing !== true) {
            return;
        }

        $billingPeriodTransfer = $this
            ->getFactory()
            ->getBillingFacade()
            ->getBillingPeriodByTimeAndBranch(new DateTime($orderTransfer->getConcreteTimeSlot()->getEndTime()), $fkBranch);

        $idBillingPeriod = $billingPeriodTransfer->getIdBillingPeriod();

        $this
            ->getFactory()
            ->getBillingFacade()
            ->createBillingItemsForBillingPeriodByBillingPeriodId($idBillingPeriod);

        $this
            ->getFacade()
            ->sendBillingMail($orderTransfer->getBranch(), $billingPeriodTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @return bool
     * @throws \Exception
     */
    protected function isBillingPeriodMissing(OrderTransfer $orderTransfer): bool
    {
        $branch = $orderTransfer
            ->getBranch();

        // no start date or cycle set, no billing period wanted
        if (
            $branch->getBillingStartDate() === null ||
            $branch->getBillingCycle() === null
        ) {
            return false;
        }

        $startDate = $branch
            ->getBillingStartDate();

        if (is_string($startDate)) {
            $startDate = new DateTime($startDate);
        }

        $now = new DateTime('now');

        // check, if billing period starts in the future
        if ($startDate > $now) {
            return false;
        }

        return true;
    }
}
