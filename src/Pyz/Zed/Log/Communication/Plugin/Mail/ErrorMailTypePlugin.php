<?php
/**
 * Durst - project - ErrorMailTypePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 16:10
 */

namespace Pyz\Zed\Log\Communication\Plugin\Mail;


use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class ErrorMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'error mail for important errors';

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
    public function build(MailBuilderInterface $mailBuilder)
    {
        $this
            ->setTextTemplate($mailBuilder)
            ->setSender($mailBuilder);
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $builder
     * @return $this
     */
    protected function setTextTemplate(MailBuilderInterface $builder): self
    {
        $builder
            ->setTextTemplate(
                'log/mail/error-mail.text.twig'
            );

        return $this;
    }

    /**
     * @param \Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface $builder
     * @return $this
     */
    protected function setSender(MailBuilderInterface $builder): self
    {
        $builder
            ->setSender(
                'mail.sender.email',
                'mail.sender.name'
            );

        return $this;
    }
}
