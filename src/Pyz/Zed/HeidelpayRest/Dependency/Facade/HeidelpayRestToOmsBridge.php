<?php
/**
 * Durst - project - HeidelpayRestToOmsBridge.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.02.19
 * Time: 21:22
 */

namespace Pyz\Zed\HeidelpayRest\Dependency\Facade;

use Generated\Shared\Transfer\DurstCompanyTransfer;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;

class HeidelpayRestToOmsBridge implements HeidelpayRestToOmsBridgeInterface
{
    /**
     * @var \Pyz\Zed\HeidelpayRest\Dependency\Facade\OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * HeidelpayRestToOmsBridge constructor.
     *
     * @param \Pyz\Zed\Oms\Business\OmsFacadeInterface $omsFacade
     */
    public function __construct(OmsFacadeInterface $omsFacade)
    {
        $this->omsFacade = $omsFacade;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $eventId
     * @param array $orderItemIds
     * @param array $data
     *
     * @return array
     */
    public function triggerEventForOrderItems(string $eventId, array $orderItemIds, array $data = []): array
    {
        return $this
            ->omsFacade
            ->triggerEventForOrderItems($eventId, $orderItemIds, $data);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer(): DurstCompanyTransfer
    {
        return $this
            ->omsFacade
            ->createDurstCompanyTransfer();
    }
}
