<?php
/**
 * Durst - project - StateMachineTourItemSaver.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Business\Model;


use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Business\Exception\ConcreteTourNotExistsException;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class StateMachineTourItemSaver implements StateMachineTourItemSaverInterface
{
    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * StateMachineTourItemSaver constructor.
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer
     */
    public function __construct(TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     * @return bool
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function itemStateUpdate(StateMachineItemTransfer $stateMachineItemTransfer): bool
    {
        $tourEntity = $this
            ->queryContainer
            ->queryConcreteTour()
            ->findOneByIdConcreteTour($stateMachineItemTransfer->getIdentifier());

        if($tourEntity === null){
            throw ConcreteTourNotExistsException::doesntExistWithId($stateMachineItemTransfer->getIdentifier());
        }

        $tourEntity
            ->setFkStateMachineItemState($stateMachineItemTransfer->getIdItemState());

        $affectedRowCount = $tourEntity
            ->save();

        return ($affectedRowCount > 0);
    }
}
