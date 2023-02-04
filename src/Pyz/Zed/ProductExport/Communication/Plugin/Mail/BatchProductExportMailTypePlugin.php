<?php
/**
 * Durst - project - BatchProductExportMailTypePlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 01.10.20
 * Time: 11:11
 */

namespace Pyz\Zed\ProductExport\Communication\Plugin\Mail;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Mail\Business\Model\Mail\Builder\MailBuilderInterface;
use Spryker\Zed\Mail\Dependency\Plugin\MailTypePluginInterface;

class BatchProductExportMailTypePlugin extends AbstractPlugin implements MailTypePluginInterface
{
    public const MAIL_TYPE = 'batch product export mail';

    protected const TEXT_TEMPLATE = 'product-export/mail/batch-product-export-mail.text.twig';

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
