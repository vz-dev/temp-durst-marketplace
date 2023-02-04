<?php

namespace Pyz\Zed\GraphMasters\Business\Model;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\GraphMastersOpeningTimeTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOpeningTime;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;

class OpeningTime implements OpeningTimeInterface
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var GraphMastersConfig
     */
    protected $config;

    /**
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param GraphMastersConfig $config
     */
    public function __construct(GraphMastersQueryContainerInterface $queryContainer, GraphMastersConfig $config)
    {
        $this->queryContainer = $queryContainer;
        $this->config = $config;
    }

    /**
     * @param GraphMastersOpeningTimeTransfer $transfer
     *
     * @return DstGraphmastersOpeningTime
     *
     * @throws PropelException
     */
    public function save(GraphMastersOpeningTimeTransfer $transfer): DstGraphmastersOpeningTime
    {
        $entity = $this->getEntity($transfer->getIdGraphmastersOpeningTime());
        $entity->fromArray($transfer->toArray());

        $entity->setStartTime($this->toUtcDateTime($transfer->getStartTime()));
        $entity->setEndTime($this->toUtcDateTime($transfer->getEndTime()));

        $entity->setPauseStartTime(
            ($transfer->getPauseStartTime() !== null && $transfer->getPauseStartTime() !== '')
                ? $this->toUtcDateTime($transfer->getPauseStartTime())
                : $transfer->getPauseStartTime()
        );

        $entity->setPauseEndTime(
            ($transfer->getPauseEndTime() !== null && $transfer->getPauseEndTime() !== '')
                ? $this->toUtcDateTime($transfer->getPauseEndTime())
                : $transfer->getPauseEndTime()
        );

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }

        return $entity;
    }

    /**
     * @param int $idOpeningTime
     *
     * @return void
     *
     * @throws PropelException
     */
    public function remove(int $idOpeningTime): void
    {
        $entity = $this
            ->queryContainer
            ->createGraphmastersOpeningTimeQuery()
            ->findOneByIdGraphmastersOpeningTime($idOpeningTime);

        if ($entity !== null) {
            $entity->delete();
        }
    }

    /**
     * @param DstGraphmastersOpeningTime $entity
     *
     * @return GraphMastersOpeningTimeTransfer
     *
     * @throws PropelException
     */
    public function entityToTransfer(DstGraphmastersOpeningTime $entity): GraphMastersOpeningTimeTransfer
    {
        $transfer = (new GraphMastersOpeningTimeTransfer())
            ->fromArray($entity->toArray(), true);

        $transfer->setStartTime($this->toLocalDateTimeString($entity->getStartTime()));
        $transfer->setEndTime($this->toLocalDateTimeString($entity->getEndTime()));

        if ($entity->getPauseStartTime() !== null) {
            $transfer->setPauseStartTime($this->toLocalDateTimeString($entity->getPauseStartTime()));
        }

        if ($entity->getPauseEndTime() !== null) {
            $transfer->setPauseEndTime($this->toLocalDateTimeString($entity->getPauseEndTime()));
        }

        return $transfer;
    }

    /**
     * @param int|null $idOpeningTime
     *
     * @return DstGraphmastersOpeningTime
     */
    protected function getEntity(?int $idOpeningTime = null): DstGraphmastersOpeningTime
    {
        if ($idOpeningTime === null) {
            return new DstGraphmastersOpeningTime();
        }

        $entity = $this
            ->queryContainer
            ->createGraphmastersOpeningTimeQuery()
            ->findOneByIdGraphmastersOpeningTime($idOpeningTime);

        if ($entity === null) {
            throw EntityNotFoundException::build($idOpeningTime);
        }

        return $entity;
    }

    /**
     * @param string $dateTime
     *
     * @return DateTime
     */
    private function toUtcDateTime(string $dateTime): DateTime
    {
        return (new DateTime($dateTime, new DateTimeZone($this->config->getProjectTimeZone())));
            //->setTimezone(new DateTimeZone('UTC'))
    }

    /**
     * @param DateTime $dateTime
     *
     * @return string
     */
    private function toLocalDateTimeString(DateTime $dateTime): string
    {
        return $dateTime
            //->setTimezone(new DateTimeZone($this->config->getProjectTimeZone()))
            ->format('H:i:s');
    }
}
