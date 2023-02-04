<?php

namespace Pyz\Zed\GraphMasters\Business\Model;

use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\GraphMastersCommissioningTimeTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersCommissioningTime;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Pyz\Zed\GraphMasters\GraphMastersConfig;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;

class CommissioningTime implements CommissioningTimeInterface
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
     * @param GraphMastersCommissioningTimeTransfer $transfer
     *
     * @return DstGraphmastersCommissioningTime
     *
     * @throws PropelException
     */
    public function save(GraphMastersCommissioningTimeTransfer $transfer): DstGraphmastersCommissioningTime
    {
        $entity = $this->getEntity($transfer->getIdGraphmastersCommissioningTime());
        $entity->fromArray($transfer->toArray());

        $entity->setStartTime($this->toUtcDateTime($transfer->getStartTime()));
        $entity->setEndTime($this->toUtcDateTime($transfer->getEndTime()));

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }

        return $entity;
    }

    /**
     * @param int $idCommissioningTime
     *
     * @return void
     *
     * @throws PropelException
     */
    public function remove(int $idCommissioningTime): void
    {
        $entity = $this
            ->queryContainer
            ->createGraphmastersCommissioningTimeQuery()
            ->findOneByIdGraphmastersCommissioningTime($idCommissioningTime);

        if ($entity !== null) {
            $entity->delete();
        }
    }

    /**
     * @param DstGraphmastersCommissioningTime $entity
     *
     * @return GraphMastersCommissioningTimeTransfer
     *
     * @throws PropelException
     */
    public function entityToTransfer(DstGraphmastersCommissioningTime $entity): GraphMastersCommissioningTimeTransfer
    {
        $transfer = (new GraphMastersCommissioningTimeTransfer())
            ->fromArray($entity->toArray(), true);

        $transfer->setStartTime($this->toLocalDateTimeString($entity->getStartTime()));
        $transfer->setEndTime($this->toLocalDateTimeString($entity->getEndTime()));

        return $transfer;
    }

    /**
     * @param int|null $idCommissioningTime
     *
     * @return DstGraphmastersCommissioningTime
     */
    protected function getEntity(?int $idCommissioningTime = null): DstGraphmastersCommissioningTime
    {
        if ($idCommissioningTime === null) {
            return new DstGraphmastersCommissioningTime();
        }

        $entity = $this
            ->queryContainer
            ->createGraphmastersCommissioningTimeQuery()
            ->findOneByIdGraphmastersCommissioningTime($idCommissioningTime);

        if ($entity === null) {
            throw EntityNotFoundException::build($idCommissioningTime);
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
            //->setTimezone(new DateTimeZone('UTC'));
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
