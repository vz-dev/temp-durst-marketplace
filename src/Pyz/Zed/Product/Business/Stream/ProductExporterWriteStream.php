<?php
/**
 * Durst - project - ProductExporterWriteStream.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 12.09.18
 * Time: 16:00
 */

namespace Pyz\Zed\Product\Business\Stream;

use SprykerMiddleware\Shared\Process\Stream\StreamInterface;
use SprykerMiddleware\Shared\Process\Stream\WriteStreamInterface;
use SprykerMiddleware\Zed\Process\Business\Exception\MethodNotSupportedException;

class ProductExporterWriteStream implements StreamInterface, WriteStreamInterface
{
    /**
     * @var
     */
    private $file;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * ProductExporterWriteStream constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return bool
     */
    public function open(): bool
    {

        $dirname = dirname($this->path);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        $this->file = fopen($this->path, "w");

        $this->data = [];
        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @param int $offset
     * @param int $whence
     *
     * @throws \SprykerMiddleware\Zed\Process\Business\Exception\MethodNotSupportedException
     *
     * @return int
     */
    public function seek(int $offset, int $whence): int
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @throws \SprykerMiddleware\Zed\Process\Business\Exception\MethodNotSupportedException
     *
     * @return bool
     */
    public function eof(): bool
    {
        throw new MethodNotSupportedException();
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function write(array $data): int
    {
        if (empty($this->data)) {
            fputcsv($this->file, array_keys($data), ";");
        }

        $this->data[] = $data;

        fputcsv($this->file, $data, ";");
        return 1;
    }

    /**
     * @return bool
     */
    public function flush(): bool
    {
        return true;
    }
}
