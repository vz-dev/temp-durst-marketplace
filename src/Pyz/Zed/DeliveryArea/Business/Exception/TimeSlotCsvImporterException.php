<?php

namespace Pyz\Zed\DeliveryArea\Business\Exception;

use Exception;
use Throwable;

class TimeSlotCsvImporterException extends Exception
{
    const MESSAGE = 'The time slot import failed';

    /**
     * @var int
     */
    protected $failedRow;

    /**
     * @var array
     */
    protected $failedItem;

    public function __construct($message = self::MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getFailedRow(): int
    {
        return $this->failedRow;
    }

    /**
     * @param int $failedRow
     *
     * @return self
     */
    public function setFailedRow(int $failedRow): self
    {
        $this->failedRow = $failedRow;

        return $this;
    }

    /**
     * @return array
     */
    public function getFailedItem(): array
    {
        return $this->failedItem;
    }

    /**
     * @param array $failedItem
     *
     * @return self
     */
    public function setFailedItem(array $failedItem): self
    {
        $this->failedItem = $failedItem;

        return $this;
    }
}
