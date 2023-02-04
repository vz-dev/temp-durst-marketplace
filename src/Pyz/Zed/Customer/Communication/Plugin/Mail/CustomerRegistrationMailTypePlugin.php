<?php
/**
 * Durst - project - CustomerRegistrationMailTypePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 22.11.21
 * Time: 10:24
 */

namespace Pyz\Zed\Customer\Communication\Plugin\Mail;

use Spryker\Zed\Customer\Communication\Plugin\Mail\CustomerRegistrationMailTypePlugin as SprykerCustomerRegistrationMailTypePlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;

class CustomerRegistrationMailTypePlugin extends SprykerCustomerRegistrationMailTypePlugin
{
    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setHtmlTemplate(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setHtmlTemplate('customer/mail/customer_registration_new.html.twig');

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder->setTextTemplate('customer/mail/customer_registration_new.text.twig');

        return $this;
    }
}
