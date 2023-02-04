<?php
/**
 * Durst - project - RealaxMailTypePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 25.06.20
 * Time: 11:29
 */

namespace Pyz\Zed\Accounting\Communication\Plugin\Mail;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class RealaxMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'realax mail for exported files';

    protected const TEXT_TEMPLATE = 'accounting/mail/realax-export-mail.text.twig';

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
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $builder
     * @return $this
     */
    protected function setTextTemplate(
        MailBuilderInterface $builder
    ): self
    {
        $builder
            ->setTextTemplate(
                static::TEXT_TEMPLATE
            );

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $builder
     * @return $this
     */
    protected function setSender(
        MailBuilderInterface $builder
    ): self
    {
        $builder
            ->setSender(
                'mail.sender.email',
                'mail.sender.name'
            );

        return $this;
    }
}
