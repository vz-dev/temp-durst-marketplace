<?php
/**
 * Durst - project - StateConcreteTourHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;

/**
 * Class StateConcreteTourHydrator
 * @package Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator
 */
class StateConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    /**
     * @param DstConcreteTour $concreteTourEntity
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function hydrateConcreteTour(DstConcreteTour $concreteTourEntity, ConcreteTourTransfer $concreteTourTransfer): void
    {
        if ($concreteTourEntity->getState() !== null) {
            $concreteTourTransfer
                ->setState(
                    $concreteTourEntity
                        ->getState()
                        ->getName()
                );
        }
    }
}
