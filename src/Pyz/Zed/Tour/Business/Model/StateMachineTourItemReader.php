<?php
/**
 * Durst - project - StateMachineTourItemReader.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 2019-10-07
 * Time: 14:04
 */


namespace Pyz\Zed\Tour\Business\Model;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Persistence\TourQueryContainerInterface;

class StateMachineTourItemReader implements StateMachineTourItemReaderInterface
{

    /**
     * @var \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * StateMachineTourItemReader constructor.
     * @param \Pyz\Zed\Tour\Persistence\TourQueryContainerInterface $queryContainer
     */
    public function __construct(TourQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * {@inheritDoc}
     *
     * @param int[] $stateIds
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getStateMachineItemsByStateIds(array $stateIds = []): array
    {
        $tourEntities = $this
            ->queryContainer
            ->queryStateMachineItemsByStateIds($stateIds);

        return $this
            ->hydrateStateMachineTransferFromPersistence($tourEntities);
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getStateMachineItems(): array
    {
        $tourEntities = $this
            ->queryContainer
            ->queryConcreteTour()
            ->find();

        return $this
            ->hydrateStateMachineTransferFromPersistence($tourEntities);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idConcreteTour
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getStateMachineItemByIdConcreteTour(int $idConcreteTour): StateMachineItemTransfer
    {
        $tourEntity = $this
            ->queryContainer
            ->queryConcreteTour()
            ->filterByIdConcreteTour($idConcreteTour)
            ->find();

        $states = $this
            ->hydrateStateMachineTransferFromPersistence($tourEntity);

        return reset($states);
    }

    /**
     * @param iterable|\Orm\Zed\Tour\Persistence\DstConcreteTour[] $tourEntities
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function hydrateStateMachineTransferFromPersistence(iterable $tourEntities): array
    {
        $stateMachineTransfers = [];

        foreach ($tourEntities as $tourEntity) {
            if ($tourEntity->getFkStateMachineItemState() === null) {
                continue;
            }

            $process = $tourEntity
                ->getState()
                ->getProcess();

            $stateMachineItem = new StateMachineItemTransfer();

            $stateMachineItem
                ->setIdentifier($tourEntity->getIdConcreteTour())
                ->setIdItemState($tourEntity->getFkStateMachineItemState())
                ->setProcessName($process->getName())
                ->setStateMachineName($process->getStateMachineName());

            $stateMachineTransfers[] = $stateMachineItem;
        }

        return $stateMachineTransfers;
    }
}
