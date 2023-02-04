<?php
/**
 * Durst - project - RealaxExportOutputStream.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.03.20
 * Time: 14:46
 */

namespace Pyz\Zed\Accounting\Business\Stream;


use ArrayObject;
use Generated\Shared\Transfer\MailAttachmentTransfer;
use Generated\Shared\Transfer\MailRecipientTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Pyz\Zed\Accounting\AccountingConfig;
use Pyz\Zed\Accounting\Communication\Plugin\Mail\RealaxMailTypePlugin;
use Pyz\Zed\Accounting\Dependency\Facade\AccountingToMailBridgeInterface;
use SplFileObject;
use SprykerMiddleware\Shared\Process\Stream\StreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;

class RealaxExportOutputStream implements StreamInterface, WriteStreamInterface
{
    protected const REALAX_MAIL_SUBJECT = 'Lizenzrechnung (variabel) %s';
    protected const REALAX_MAIL_MESSAGE = 'Das System hat die Lizenzrechnung (variabel) %s erstellt.';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMailBridgeInterface
     */
    protected $mailFacade;

    /**
     * @var \Pyz\Zed\Accounting\AccountingConfig
     */
    protected $config;

    /**
     * @var \SplFileObject
     */
    protected $handle;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * RealaxExportOutputStream constructor.
     * @param string $path
     * @param \Pyz\Zed\Accounting\Dependency\Facade\AccountingToMailBridgeInterface $mailFacade
     * @param \Pyz\Zed\Accounting\AccountingConfig $config
     */
    public function __construct(
        string $path,
        AccountingToMailBridgeInterface $mailFacade,
        AccountingConfig $config
    )
    {
        $this->path = $path;
        $this->mailFacade = $mailFacade;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function open(): bool
    {
        $this->handle = new SplFileObject(
            $this->path,
            'w'
        );

        $this->data = [];

        $this->position = 0;

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function close(): bool
    {
        if ($this->handle !== null) {
            unset($this->handle);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek(int $offset, int $whence): int
    {
        $newPosition = $this
            ->getNewPosition(
                $offset,
                $whence
            );

        if (
            $newPosition < 0 ||
            $newPosition > count($this->data)
        ) {
            return static::STATUS_SEEK_FAIL;
        }

        $this->position = $newPosition;

        return static::STATUS_SEEK_SUCCESS;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function eof(): bool
    {
        return ($this->position >= count($this->data));
    }

    /**
     * {@inheritDoc}
     *
     * @param array $data
     * @return int
     */
    public function write(array $data): int
    {
        $this->data[$this->position++] = $data;

        return 1;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function flush(): bool
    {
        foreach ($this->data as $item) {
            $content = implode(
                $this->config->getRealaxDelimiter(),
                $item
            );

            if (mb_detect_encoding($content, 'UTF-8', true) === 'UTF-8') {
                $content = utf8_decode($content);
            }

            $this
                ->handle
                ->fwrite(
                    sprintf(
                        $this->config->getRealaxCsvLineFormat(),
                        $content
                    )
                );
        }

        $result = $this
            ->handle
            ->fflush();

        if ($result === true) {
            $this->data = [];
            $this->position = 0;

            $this
                ->sendExportMail();
        }

        return $result;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    protected function getNewPosition(int $offset, int $whence): int
    {
        $newPosition = $this->position;

        if ($whence === SEEK_SET) {
            $newPosition = $offset;
        }

        if ($whence === SEEK_CUR) {
            $newPosition = $this->position + $offset;
        }

        if ($whence === SEEK_END) {
            $newOffset = 0;

            if ($offset <= 0) {
                $newOffset = $offset;
            }

            $newPosition = count($this->data) + $newOffset;
        }

        return $newPosition;
    }

    /**
     * @return void
     */
    protected function sendExportMail(): void
    {
        $mailTransfer = $this
            ->createMailTransfer();

        $this
            ->mailFacade
            ->handleMail(
                $mailTransfer
            );
    }

    /**
     * @return \Generated\Shared\Transfer\MailTransfer
     */
    protected function createMailTransfer(): MailTransfer
    {
        $mailTransfer = new MailTransfer();

        $fileName = $this
            ->getFilename();

        $mailTransfer
            ->setSubject(
                sprintf(
                    static::REALAX_MAIL_SUBJECT,
                    $fileName
                )
            )
            ->setMessage(
                sprintf(
                    static::REALAX_MAIL_MESSAGE,
                    $fileName
                )
            )
            ->setType(
                RealaxMailTypePlugin::MAIL_TYPE
            )
            ->setRecipients(
                $this
                    ->getMailRecipients()
            )
            ->addAttachment(
                $this
                    ->createMailAttachment()
            );

        return $mailTransfer;
    }

    /**
     * @return \ArrayObject
     */
    protected function getMailRecipients(): ArrayObject
    {
        $recipients = new ArrayObject();

        foreach ($this->config->getRealaxRecipients() as $email => $name) {
            $transfer = new MailRecipientTransfer();

            $transfer
                ->setName($name)
                ->setEmail($email);

            $recipients
                ->append($transfer);
        }

        return $recipients;
    }

    /**
     * @return \Generated\Shared\Transfer\MailAttachmentTransfer
     */
    protected function createMailAttachment(): MailAttachmentTransfer
    {
        $fileName = $this
            ->getFilename();

        return (new MailAttachmentTransfer())
            ->setAttachmentUrl($this->handle->getRealPath())
            ->setDisplayName($fileName)
            ->setFileName($this->handle->getFilename());
    }

    /**
     * @return string
     */
    protected function getFilename(): string
    {
        return pathinfo(
            $this
                ->handle
                ->getBasename(),
            PATHINFO_FILENAME
        );
    }
}
