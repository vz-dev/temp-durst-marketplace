<?php
/**
 * Durst - project - TouchFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:16
 */

namespace Pyz\Zed\Touch\Business;

use Spryker\Zed\Touch\Business\TouchFacadeInterface as SprykerTouchFacadeInterface;

interface TouchFacadeInterface extends SprykerTouchFacadeInterface
{
    /**
     * Updates the time stamps of all touch entities to 'now'
     *
     * @return void
     */
    public function touchAllNow();

    /**
     * Removes all entries from the touch_search table
     *
     * @return void
     */
    public function removeAllTouchSearchEntries();
}