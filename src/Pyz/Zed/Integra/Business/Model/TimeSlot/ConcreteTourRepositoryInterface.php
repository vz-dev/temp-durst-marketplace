<?php
/**
 * Durst - project - ConcreteTourRepositoryInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 20.11.20
 * Time: 16:05
 */

namespace Pyz\Zed\Integra\Business\Model\TimeSlot;

interface ConcreteTourRepositoryInterface
{
    /**
     * @param string $tourReference
     *
     * @return int
     */
    public function getTourIdByReference(string $tourReference): int;

    /**
     * @param array $references
     * @param int $idBranch
     * @param array $refsToDates
     *
     * @return int
     */
    public function loadTours(array $references, int $idBranch, array $refsToDates): int;
}
