<?php
/**
 * Durst - project - ProductExporterReadStream.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.09.18
 * Time: 14:24
 */

namespace Pyz\Zed\Product\Business\Stream;


use Propel\Runtime\ActiveQuery\ModelCriteria;
use SprykerMiddleware\Shared\Process\Stream\ReadStreamInterface;
use SprykerMiddleware\Shared\Process\Stream\StreamInterface;

class ProductExporterReadStream implements StreamInterface, ReadStreamInterface
{
    public const COLUMN_SKU = 'sku';


    /**
     * @var \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    protected $modelCriteria;

    /**
     * @var \Propel\Runtime\Collection\CollectionIterator
     */
    protected $iterator;

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $modelCriteria
     */
    public function __construct(ModelCriteria $modelCriteria)
    {
        $this->modelCriteria = $modelCriteria;
    }

    /**
     * @return mixed
     */
    public function read()
    {
        return $this->get()->toArray();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $item = $this->iterator
            ->current();
        $this->iterator->next();
        return $item;
    }

    /**
     * @return bool
     */
    public function open(): bool
    {
        $this->iterator = $this->modelCriteria
            ->orderBy(static::COLUMN_SKU)
            ->find()
            ->getIterator();

        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        unset($this->iterator);
        return true;
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @return int
     */
    public function seek(int $offset, int $whence): int
    {
        if ($whence === SEEK_SET) {
            $this->iterator->seek($offset);
            return 0;
        }

        return -1;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        return !$this->iterator->valid();
    }
}
