<?php
/**
 * Durst - project - TimeStampUpdaterInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:11
 */

namespace Pyz\Zed\Touch\Business\Util;


interface TimeStampUpdaterInterface
{
    /**
     * @return void
     */
    public function touchAllNow();
}