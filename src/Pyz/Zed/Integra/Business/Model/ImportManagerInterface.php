<?php
/**
 * Durst - project - ImportManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.11.20
 * Time: 11:10
 */

namespace Pyz\Zed\Integra\Business\Model;

interface ImportManagerInterface
{
    /**
     * @param int $idBranch
     *
     * @return void
     */
    public function importOrdersForBranch(int $idBranch): void;
}
