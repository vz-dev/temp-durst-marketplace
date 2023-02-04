<?php

namespace Pyz\Zed\DepositPickup\Business\Model\DepositPickupInquiry;

use DateTimeZone;
use Generated\Shared\Transfer\DepositPickupInquiryTransfer;
use Orm\Zed\DepositPickup\Persistence\DstDepositPickupInquiry;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\DepositPickup\Business\Exception\DepositPickupInquiryNotFoundException;
use Pyz\Zed\DepositPickup\DepositPickupConfig;
use Pyz\Zed\DepositPickup\Persistence\DepositPickupQueryContainerInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;
use Throwable;

class DepositPickupInquiry implements DepositPickupInquiryInterface
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var DepositPickupConfig
     */
    protected $config;

    /**
     * @var DepositPickupInquirySaverInterface
     */
    protected $saver;

    /**
     * @var DepositPickupQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param DepositPickupConfig $config
     * @param DepositPickupInquirySaverInterface $saver
     * @param DepositPickupQueryContainerInterface $queryContainer
     */
    public function __construct(
        DepositPickupConfig $config,
        DepositPickupInquirySaverInterface $saver,
        DepositPickupQueryContainerInterface $queryContainer
    ) {
        $this->config = $config;
        $this->saver = $saver;
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param DepositPickupInquiryTransfer $inquiryTransfer
     *
     * @throws Throwable
     */
    public function save(DepositPickupInquiryTransfer $inquiryTransfer): void
    {
        $this->saver->saveDepositPickupInquiry($inquiryTransfer);
    }

    /**
     * @param int $fkBranch
     *
     * @return array|DepositPickupInquiryTransfer[]
     *
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function getInquiriesByFkBranch(int $fkBranch): array
    {
        $inquiryEntities = $this
            ->queryContainer
            ->queryInquiriesByFkBranch($fkBranch)
            ->find();

        $inquiryTransfers = [];

        foreach ($inquiryEntities as $inquiryEntity) {
            $inquiryTransfers[] = $this->entityToTransfer($inquiryEntity);
        }

        return $inquiryTransfers;
    }

    /**
     * @param int $idDepositPickupInquiry
     * @param int $fkBranch
     *
     * @return DepositPickupInquiryTransfer|null
     *
     * @throws AmbiguousComparisonException
     * @throws PropelException
     * @throws DepositPickupInquiryNotFoundException
     */
    public function getInquiryByIdAndFkBranch(int $idDepositPickupInquiry, int $fkBranch): ?DepositPickupInquiryTransfer
    {
        $inquiryEntity = $this
            ->queryContainer
            ->queryInquiry()
            ->filterByFkBranch($fkBranch)
            ->findOneByIdDepositPickupInquiry($idDepositPickupInquiry);

        if ($inquiryEntity === null) {
            throw new DepositPickupInquiryNotFoundException(
                sprintf(
                    'Inquiry could not be found for inquiry ID %s and branch ID %s',
                    $idDepositPickupInquiry,
                    $fkBranch
                )
            );
        }

        return $this->entityToTransfer($inquiryEntity);
    }

    /**
     * @param DstDepositPickupInquiry $inquiryEntity
     *
     * @return DepositPickupInquiryTransfer
     * @throws PropelException
     */
    public function entityToTransfer(DstDepositPickupInquiry $inquiryEntity): DepositPickupInquiryTransfer
    {
        $inquiryTransfer = new DepositPickupInquiryTransfer();
        $inquiryTransfer->fromArray($inquiryEntity->toArray(), true);

        $inquiryTransfer->setPreferredDate(
            $inquiryEntity
                ->getPreferredDate()
                ->setTimezone(new DateTimeZone($this->config->getProjectTimezone()))
                ->format(self::DATE_FORMAT)
        );

        $inquiryTransfer->setCreatedAt(
            $inquiryEntity
                ->getCreatedAt()
                ->setTimezone(new DateTimeZone($this->config->getProjectTimezone()))
                ->format(self::DATE_FORMAT)
        );

        return $inquiryTransfer;
    }
}
