<?php
/**
 * Durst - project - DeliveryNoteManagerInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 24.08.21
 * Time: 21:54
 */

namespace Pyz\Zed\Integra\Business\Model;


interface DeliveryNoteManagerInterface
{
    /**
     * @param int[] $idOrders
     * @param int $idBranch
     * @return array
     */
    public function createDeliveryNotePdfs(array $idOrders, int $idBranch): array;
}
