<?php
/**
 * Durst - project - ApkFileManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-07
 * Time: 10:14
 */

namespace Pyz\Zed\DriverApp\Business\Model\File;

use Pyz\Zed\DriverApp\Business\Exception\FileNotFoundException;
use Pyz\Zed\DriverApp\Business\Exception\FileRemoveException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class ApkFileManager implements ApkFileManagerInterface
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fileSystem;

    /**
     * ApkFileManager constructor.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $fileName
     *
     * @throws \Pyz\Zed\DriverApp\Business\Exception\FileRemoveException
     *
     * @return void
     */
    public function deleteFile(string $fileName): void
    {
        if($this
            ->fileSystem
            ->exists($fileName) !== true){
            throw new FileNotFoundException($fileName);
        }

        try {
            $this
                ->fileSystem
                ->remove($fileName);
        } catch (IOException $e) {
            throw new FileRemoveException($fileName);
        }
    }
}
