<?php
/**
 * Durst - project - ApkFileManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-08-07
 * Time: 10:26
 */

namespace Pyz\Zed\DriverApp\Business\Model\File;


interface ApkFileManagerInterface
{
    /**
     * @param string $fileName
     *
     * @throws \Pyz\Zed\DriverApp\Business\Exception\FileRemoveException
     *
     * @return void
     */
    public function deleteFile(string $fileName): void;
}