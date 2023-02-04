<?php
/**
 * Durst - project - PathManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.05.20
 * Time: 09:00
 */

namespace Pyz\Zed\Billing\Business\Model\File;


interface PathManagerInterface
{
    /**
     * @return void
     */
    public function checkZipFilePath(): void;
}
