<?php
/**
 * Durst - project - DeliveryAreaToTouchBridge.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.10.18
 * Time: 11:13
 */

namespace Pyz\Zed\DeliveryArea\Dependency\Facade;

use Spryker\Zed\Touch\Business\TouchFacadeInterface;

class DeliveryAreaToTouchBridge implements DeliveryAreaToTouchBridgeInterface
{
    /**
     * @var \Spryker\Zed\Touch\Business\TouchFacadeInterface
     */
    protected $touchFacade;

    /**
     * @param \Spryker\Zed\Touch\Business\TouchFacadeInterface $touchFacade
     */
    public function __construct(TouchFacadeInterface $touchFacade)
    {
        $this->touchFacade = $touchFacade;
    }

    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    public function touchActive(string $itemType, int $idItem) : bool
    {
        return $this->touchFacade->touchActive($itemType, $idItem);
    }

    /**
     * @param string $itemType
     * @param int $idItem
     *
     * @return bool
     */
    public function touchDeleted(string $itemType, int $idItem) : bool
    {
        return $this->touchFacade->touchDeleted($itemType, $idItem);
    }
}