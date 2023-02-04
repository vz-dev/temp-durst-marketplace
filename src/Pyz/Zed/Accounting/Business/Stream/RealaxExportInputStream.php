<?php
/**
 * Durst - project - RealaxExportInputStream.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 26.03.20
 * Time: 16:03
 */

namespace Pyz\Zed\Accounting\Business\Stream;


use ArrayObject;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\RealaxTransfer;
use Pyz\Zed\Accounting\AccountingConfig;
use Pyz\Zed\Accounting\Business\AccountingFacadeInterface;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\StreamInterface;

class RealaxExportInputStream implements StreamInterface, ReadStreamInterface
{
    /**
     * @var int
     */
    protected $idMerchant;

    /**
     * @var \Pyz\Zed\Accounting\Business\AccountingFacadeInterface
     */
    protected $facade;

    /**
     * @var \Pyz\Zed\Accounting\AccountingConfig
     */
    protected $config;

    /**
     * @var \DateTime
     */
    protected $currentDate;

    /**
     * @var \ArrayIterator
     */
    protected $iterator;

    /**
     * RealaxExportInputStream constructor.
     * @param int $idMerchant
     * @param \Pyz\Zed\Accounting\Business\AccountingFacadeInterface $accountingFacade
     * @param \Pyz\Zed\Accounting\AccountingConfig $config
     * @throws \Exception
     */
    public function __construct(
        int $idMerchant,
        AccountingFacadeInterface $accountingFacade,
        AccountingConfig $config
    )
    {
        $this->idMerchant = $idMerchant;
        $this->facade = $accountingFacade;
        $this->config = $config;

        $this
            ->setCurrentDate();
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function read()
    {
        return $this
            ->get();
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function open(): bool
    {
        $realaxTransfer = $this
        ->facade
            ->getRealaxTransferByIdMerchant(
                $this
                    ->idMerchant
            );

        $this->iterator = $this
            ->flattenRealaxTransfer($realaxTransfer)
            ->getIterator();

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function close(): bool
    {
        unset($this->iterator);

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
        if (
            $whence === SEEK_SET &&
            $this->iterator->count() > 0
        ) {
            $this
                ->iterator
                ->seek($offset);

            return 0;
        }

        return 1;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function eof(): bool
    {
        return !$this
            ->iterator
            ->valid();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $currentItem = $this
            ->iterator
            ->current();

        $this
            ->iterator
            ->next();

        return $currentItem;
    }

    /**
     * @throws \Exception
     */
    protected function setCurrentDate(): void
    {
        $this->currentDate = new DateTime('now');
        $projectTimezone = new DateTimeZone(
            $this
                ->config
                ->getProjectTimeZone()
        );

        $this
            ->currentDate
            ->setTimezone(
                $projectTimezone
            );
    }

    /**
     * @param \Generated\Shared\Transfer\RealaxTransfer $realaxTransfer
     * @return \ArrayObject
     */
    protected function flattenRealaxTransfer(RealaxTransfer $realaxTransfer): ArrayObject
    {
        $result = new ArrayObject();

        $result
            ->append(
                $realaxTransfer
                    ->getHeader()
                    ->toArray()
            );

        $result
            ->append(
                $realaxTransfer
                    ->getBookingHead()
                    ->toArray()
            );

        foreach ($realaxTransfer->getBookingPositions() as $bookingPosition) {
            $result
                ->append(
                    $bookingPosition
                        ->toArray()
                );
        }

        return $result;
    }
}
