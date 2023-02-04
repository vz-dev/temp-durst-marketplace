<?php
/**
 * Durst - project - TouchSearchTruncaterInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:26
 */

namespace Pyz\Zed\Touch\Business\Util\Search;


interface TouchSearchTruncaterInterface
{
    /**
     * @return void
     */
    public function truncateTouchSearchTable();
}