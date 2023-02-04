<?php
/**
 * Durst - project - MerchantWholesaleOrderFailStateMailTypePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 29.07.20
 * Time: 11:39
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Mail;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class MerchantWholesaleOrderFailStateMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'merchant wholesale order fail state mail';

    protected const TEXT_TEMPLATE = 'oms/mail/merchant-wholesale-order-fail-state.text.twig';

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getName(): string
    {
        return static::MAIL_TYPE;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     * @return void
     */
    public function build(MailBuilderInterface $mailBuilder): void
    {
        $this
            ->setTextTemplate($mailBuilder)
            ->setSender($mailBuilder);
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder
            ->setTextTemplate(
                static::TEXT_TEMPLATE
            );

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $mailBuilder
     * @return $this
     */
    protected function setSender(MailBuilderInterface $mailBuilder): self
    {
        $mailBuilder
            ->setSender(
                'mail.sender.email',
                'mail.sender.name'
            );

        return $this;
    }
}
