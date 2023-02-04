<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 26.11.18
 * Time: 10:22
 */

namespace Pyz\Zed\Tour\Business\Model\ConcreteTourHydrator;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Orm\Zed\Tour\Persistence\DstConcreteTour;
use Pyz\Zed\Tour\Business\Model\AbstractTourInterface;


class AbstractTourConcreteTourHydrator implements ConcreteTourHydratorInterface
{
    /**
     * @var AbstractTourInterface
     */
    protected $abstractTourModel;

    /**
     * AbstractTourHydrator constructor.
     * @param AbstractTourInterface $abstractTourModel
     */
    public function __construct(AbstractTourInterface $abstractTourModel)
    {
        $this->abstractTourModel = $abstractTourModel;
    }

    /**
     * @param DstConcreteTour $concreteTourEntity
     * @param ConcreteTourTransfer $concreteTourTransfer
     * @return void
     */
    public function hydrateConcreteTour(
        DstConcreteTour $concreteTourEntity,
        ConcreteTourTransfer $concreteTourTransfer)
    {
        $abstractTourTransfer = $this
            ->abstractTourModel
            ->entityToTransfer($concreteTourEntity->getDstAbstractTour());
        $concreteTourTransfer->setAbstractTour($abstractTourTransfer);
    }
}
