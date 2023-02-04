<?php
/**
 * Created by PhpStorm.
 * User: lindam
 * Date: 13.09.18
 * Time: 14:16
 */

namespace Pyz\Zed\Tour\Business\Model\Saver;


use Generated\Shared\Transfer\AbstractTourTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

/**
 * Class AbstractTimeSlotSaver
 * @package Pyz\Zed\Tour\Business\Model\Saver
 */
class AbstractTimeSlotSaver implements AbstractTimeSlotSaverInterface
{

    /**
     * @var TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * AbstractTimeSlotSaver constructor.
     * @param TourQueryContainerInterface $queryContainer
     */
    public function __construct(TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractTourTransfer $abstractTourTransfer
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function saveAbstractTimeSlotsForAbstractTour(AbstractTourTransfer $abstractTourTransfer)
    {

        $idAbstractTour = $abstractTourTransfer->getIdAbstractTour();
        foreach ($abstractTourTransfer->getAbstractTimeSlotIds() as $idAbstractTimeSlot) {

            $this->addAbstractTimeSlotToAbstractTour($idAbstractTour, $idAbstractTimeSlot);
        }

        $this->removeAbstractTimeSlots($abstractTourTransfer->getAbstractTimeSlotIds(), $abstractTourTransfer->getIdAbstractTour());
    }

    /**
     * @param int $idAbstractTour
     * @param int $idAbstractTimeSlot
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @return void
     */
    protected function addAbstractTimeSlotToAbstractTour(
        int $idAbstractTour,
        int $idAbstractTimeSlot
    )
    {
        $entity = $this
            ->queryContainer
            ->queryAbstractTourToAbstractTimeSlot()
            ->filterByFkAbstractTour($idAbstractTour)
            ->filterByFkAbstractTimeSlot($idAbstractTimeSlot)
            ->findOneOrCreate();

        if($entity->isNew() || $entity->isModified()){
            $entity->save();
        }
    }

    /**
     * @param array $abstractTimeSlotsToKeep
     * @param int $idAbstractTour
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @return void
     */
    protected function removeAbstractTimeSlots(array $abstractTimeSlotsToKeep, int $idAbstractTour)
    {
        $entities = $this
            ->queryContainer
            ->queryAbstractTourToAbstractTimeSlot()
            ->filterByFkAbstractTour($idAbstractTour)
            ->filterByFkAbstractTimeSlot($abstractTimeSlotsToKeep, Criteria::NOT_IN)
            ->find();

        foreach ($entities as $entity) {
            $entity->delete();
        }
    }

}
