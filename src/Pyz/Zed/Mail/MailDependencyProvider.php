<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Mail;

use Pyz\Shared\Config\Environment;
use Pyz\Zed\Accounting\Communication\Plugin\Mail\RealaxMailTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderCashTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderCreditCardTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderInvoiceGuaranteedTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderInvoiceTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderPaypalTypePlugin;
use Pyz\Zed\CancelOrder\Communication\Plugin\Mail\CancelOrderSepaDirectDebitTypePlugin;
use Pyz\Zed\Customer\Communication\Plugin\Mail\CustomerRegistrationMailTypePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotExportMailTypePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotImportFailedDevelopersMailTypePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotImportFailedMerchantMailTypePlugin;
use Pyz\Zed\DeliveryArea\Communication\Plugin\Mail\TimeSlotImportSuccessMailTypePlugin;
use Pyz\Zed\DepositPickup\Communication\Plugin\Mail\DepositPickupInquiryNotificationMailTypePlugin;
use Pyz\Zed\Log\Communication\Plugin\Mail\ErrorMailTypePlugin;
use Pyz\Zed\Mail\Dependency\Mailer\MailToMailerBridge;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderConfirmCustomerTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderConfirmMerchantTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceInvoiceMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantOrderInvoiceSepaMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderBillingUpdateMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderConfirmCustomerTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderConfirmSepaCustomerTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderFailStateMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholesaleOrderInvalidCustomerMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\MerchantWholeSaleOrderRefundMailTypePlugin;
use Pyz\Zed\Oms\Communication\Plugin\Mail\StuckOrdersNotificationMailTypePlugin;
use Pyz\Zed\PriceImport\Communication\Plugin\Mail\BatchPriceImportDeactivatedProductsMailTypePlugin;
use Pyz\Zed\PriceImport\Communication\Plugin\Mail\BatchPriceImportMailTypePlugin;
use Pyz\Zed\ProductExport\Communication\Plugin\Mail\BatchProductExportMailTypePlugin;
use Pyz\Zed\Tour\Communication\Plugin\Mail\MerchantNotifyGoodsTypePlugin;
use Pyz\Zed\Tour\Communication\Plugin\Mail\MerchantNotifyReturnTypePlugin;
use Spryker\Zed\Customer\Communication\Plugin\Mail\CustomerRestoredPasswordConfirmationMailTypePlugin;
use Spryker\Zed\Customer\Communication\Plugin\Mail\CustomerRestorePasswordMailTypePlugin;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Mail\Business\Model\Mail\MailTypeCollectionAddInterface;
use Spryker\Zed\Mail\Business\Model\Provider\MailProviderCollectionAddInterface;
use Spryker\Zed\Mail\Communication\Plugin\MailProviderPlugin;
use Spryker\Zed\Mail\MailConfig;
use Spryker\Zed\Mail\MailDependencyProvider as SprykerMailDependencyProvider;
use Spryker\Zed\Newsletter\Communication\Plugin\Mail\NewsletterSubscribedMailTypePlugin;
use Spryker\Zed\Newsletter\Communication\Plugin\Mail\NewsletterUnsubscribedMailTypePlugin;
use Spryker\Zed\Oms\Communication\Plugin\Mail\OrderConfirmationMailTypePlugin;
use Spryker\Zed\Oms\Communication\Plugin\Mail\OrderShippedMailTypePlugin;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_Message;
use Swift_SmtpTransport;

class MailDependencyProvider extends SprykerMailDependencyProvider
{
    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container->extend(self::MAIL_TYPE_COLLECTION, function (MailTypeCollectionAddInterface $mailCollection) {
            $mailCollection
                ->add(new CustomerRegistrationMailTypePlugin())
                ->add(new CustomerRestorePasswordMailTypePlugin())
                ->add(new CustomerRestoredPasswordConfirmationMailTypePlugin())
                ->add(new NewsletterSubscribedMailTypePlugin())
                ->add(new NewsletterUnsubscribedMailTypePlugin())
                ->add(new OrderConfirmationMailTypePlugin())
                ->add(new OrderShippedMailTypePlugin())
                ->add(new MerchantOrderConfirmCustomerTypePlugin())
                ->add(new MerchantOrderConfirmMerchantTypePlugin())
                ->add(new MerchantOrderInvoiceMailTypePlugin())
                ->add(new MerchantWholesaleOrderConfirmCustomerTypePlugin())
                ->add(new MerchantWholeSaleOrderRefundMailTypePlugin())
                ->add(new MerchantWholesaleOrderConfirmSepaCustomerTypePlugin())
                ->add(new MerchantOrderInvoiceSepaMailTypePlugin())
                ->add(new MerchantWholeSaleOrderRefundMailTypePlugin())
                ->add(new MerchantNotifyReturnTypePlugin())
                ->add(new MerchantNotifyGoodsTypePlugin())
                ->add(new MerchantOrderInvoiceInvoiceMailTypePlugin())
                ->add(new ErrorMailTypePlugin())
                ->add(new RealaxMailTypePlugin())
                ->add(new MerchantWholesaleOrderInvalidCustomerMailTypePlugin())
                ->add(new MerchantWholesaleOrderFailStateMailTypePlugin())
                ->add(new TimeSlotExportMailTypePlugin())
                ->add(new TimeSlotImportFailedMerchantMailTypePlugin())
                ->add(new TimeSlotImportFailedDevelopersMailTypePlugin())
                ->add(new TimeSlotImportSuccessMailTypePlugin())
                ->add(new MerchantWholesaleOrderFailStateMailTypePlugin())
                ->add(new BatchProductExportMailTypePlugin())
                ->add(new BatchPriceImportMailTypePlugin())
                ->add(new BatchPriceImportDeactivatedProductsMailTypePlugin())
                ->add(new DepositPickupInquiryNotificationMailTypePlugin())
                ->add(new MerchantWholesaleOrderBillingUpdateMailTypePlugin())
                ->add(new CancelOrderCashTypePlugin())
                ->add(new CancelOrderCreditCardTypePlugin())
                ->add(new CancelOrderInvoiceTypePlugin())
                ->add(new CancelOrderInvoiceGuaranteedTypePlugin())
                ->add(new CancelOrderPaypalTypePlugin())
                ->add(new CancelOrderSepaDirectDebitTypePlugin())
                ->add(new StuckOrdersNotificationMailTypePlugin());

            return $mailCollection;
        });

        $container->extend(self::MAIL_PROVIDER_COLLECTION, function (MailProviderCollectionAddInterface $mailProviderCollection) {
            $mailProviderCollection->addProvider(new MailProviderPlugin(), MailConfig::MAIL_TYPE_ALL);

            return $mailProviderCollection;
        });


        return $container;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Kernel\Container $container
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMailer(Container $container): Container
    {
        $config = $this->getConfig();

        $transport = Environment::isDocker()
            ? Swift_SmtpTransport::newInstance($config->getSmtpHost(), $config->getSmtpPort())
            : Swift_MailTransport::newInstance();

        $container[static::MAILER] = function() use ($transport) {
            $message = Swift_Message::newInstance();
            $mailer = Swift_Mailer::newInstance($transport);

            $mailerBridge = new MailToMailerBridge(
                $message,
                $mailer
            );

            return $mailerBridge;
        };

        return $container;
    }
}
