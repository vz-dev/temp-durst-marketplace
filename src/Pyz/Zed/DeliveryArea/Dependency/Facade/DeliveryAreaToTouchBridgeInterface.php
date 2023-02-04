<?php
/**
 * Durst - project - DeliveryAreaToTouchBridgeInterface.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.10.18
 * Time: 11:14
 */

namespace Pyz\Zed\DeliveryArea\Dependency\Facade;


interface DeliveryAreaToTouchBridgeInterface
{
    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    public function touchActive(string $itemType, int $idItem) : bool;

    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    public function touchDeleted(string $itemType, int $idItem) : bool;
}