<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 26.11.18
 * Time: 10:11
 */

namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;

interface ConcreteTourHydratorInterface
{
    /**
     * @param DstConcreteTour $concreteTourEntity
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return void
     */
    public function hydrateConcreteTour(
        DstConcreteTour $concreteTourEntity,
        ConcreteTourTransfer $concreteTourTransfer
    );
}
