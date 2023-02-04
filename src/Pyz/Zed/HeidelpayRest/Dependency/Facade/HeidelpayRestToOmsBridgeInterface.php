<?php

/**
 * Durst - project - HeidelpayRestToOmsFacadeInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.02.19
 * Time: 21:21
 */

namespace Pyz\Zed\HeidelpayRest\Dependency\Facade;

use Generated\Shared\Transfer\DurstCompanyTransfer;

interface HeidelpayRestToOmsBridgeInterface
{
    /**
     * @param string $eventId
     * @param array $orderItemIds
     * @param array $data
     *
     * @return array
     */
    public function triggerEventForOrderItems(string $eventId, array $orderItemIds, array $data = []): array;

    /**
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer(): DurstCompanyTransfer;
}
