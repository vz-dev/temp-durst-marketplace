<?php
/**
 * Durst - project - ConcreteTimeSlotRepositoryInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 18.11.20
 * Time: 16:36
 */

namespace Pyz\Zed\Integra\Business\Model\TimeSlot;

interface ConcreteTimeSlotRepositoryInterface
{
    /**
     * @param string $zipCode
     * @param int $idBranch
     * @param string $start
     * @param string $end
     *
     * @return int
     */
    public function getTimeSlotId(
        string $zipCode,
        int $idBranch,
        string $start,
        string $end
    ): int;

    /**
     * @return void
     */
    public function resetCounter(): void;

    /**
     * @return int
     */
    public function getCounter(): int;
}
