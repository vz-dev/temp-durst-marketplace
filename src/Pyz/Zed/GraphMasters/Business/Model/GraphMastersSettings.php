<?php
/**
 * Durst - project - GraphMastersSettings.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 27.05.21
 * Time: 18:05
 */

namespace Pyz\Zed\GraphMasters\Business\Model;

use Generated\Shared\Transfer\GraphMastersSettingsTransfer;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersSettings;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\GraphMasters\Business\Exception\EntityNotFoundException;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Pyz\Zed\Touch\Business\TouchFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class GraphMastersSettings implements GraphMastersSettingsInterface
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var OpeningTimeInterface
     */
    protected $openingTimeModel;

    /**
     * @var CommissioningTimeInterface
     */
    protected $commissioningTimeModel;

    /**
     * @var TouchFacadeInterface
     */
    protected $touchFacade;

    /**
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param OpeningTimeInterface $openingTimeModel
     * @param CommissioningTimeInterface $commissioningTimeModel
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        OpeningTimeInterface $openingTimeModel,
        CommissioningTimeInterface $commissioningTimeModel,
        TouchFacadeInterface $touchFacade
    )
    {
        $this->queryContainer = $queryContainer;
        $this->openingTimeModel = $openingTimeModel;
        $this->commissioningTimeModel = $commissioningTimeModel;
        $this->touchFacade = $touchFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param GraphMastersSettingsTransfer $transfer
     *
     * @return void
     *
     * @throws PropelException
     */
    public function save(GraphMastersSettingsTransfer $transfer): void
    {
        $settingsEntity = $this->getEntity($transfer->getIdGraphmastersSettings());
        $settingsEntity->fromArray($transfer->toArray());

        if ($settingsEntity->isNew() || $settingsEntity->isModified()) {
            $settingsEntity->save();
        }

        $this->saveOpeningTimes($settingsEntity, $transfer);
        $this->saveCommissioningTimes($settingsEntity, $transfer);

        $this->touchFacade->touchActive(GraphMastersConstants::GRAPHMASTERS_SETTINGS_RESOURCE_TYPE, $settingsEntity->getFkBranch());
    }

    /**
     * @param int $idSettings
     *
     * @return void
     */
    public function remove(int $idSettings): void
    {
        $entity = $this
            ->queryContainer
            ->queryGraphMastersSettingsById($idSettings)
            ->findOne();

        if ($entity !== null) {
            $entity->delete();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return bool
     */
    public function doesBranchUseGraphmasters(int $idBranch): bool
    {
        return ($this
                ->queryContainer
                ->queryGraphMastersSettingsByIdBranch($idBranch)
                ->filterByIsActive(true)
                ->count() > 0);
    }

    /**
     * @param int $idSettings
     * @param bool $withRelatedObjects
     *
     * @return GraphMastersSettingsTransfer
     *
     * @throws PropelException
     */
    public function getSettingsById(int $idSettings, bool $withRelatedObjects = false): GraphMastersSettingsTransfer
    {
        $query = $this
            ->queryContainer
            ->queryGraphMastersSettingsById($idSettings);

        $entity = $withRelatedObjects === true
            ? $query
                ->leftJoinWithDstGraphmastersOpeningTime()
                ->leftJoinWithDstGraphmastersCommissioningTime()
                ->find()
                ->getFirst()
            : $query->findOne();

        if ($entity === null) {
            EntityNotFoundException::branch($idSettings);
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idBranch
     *
     * @return GraphMastersSettingsTransfer
     */
    public function getSettingsByIdBranch(int $idBranch): GraphMastersSettingsTransfer
    {
        $entity = $this
            ->queryContainer
            ->queryGraphMastersSettingsByIdBranch($idBranch)
            ->findOne();

        if ($entity === null) {
            EntityNotFoundException::branch($idBranch);
        }

        return $this->entityToTransfer($entity);
    }

    /**
     * @return GraphMastersSettingsTransfer[]|array
     *
     * @throws AmbiguousComparisonException
     * @throws PropelException
     */
    public function getActiveSettings(): array
    {
        $entities = $this
                ->queryContainer
                ->queryGraphmastersSettings()
                ->filterByIsActive(true)
                ->find();

        $transfers = [];

        foreach ($entities as $entity) {
            $transfers[] = $this->entityToTransfer($entity);
        }

        return $transfers;
    }

    /**
     * @param DstGraphmastersSettings $entity
     *
     * @return GraphMastersSettingsTransfer
     *
     * @throws PropelException
     */
    protected function entityToTransfer(DstGraphmastersSettings $entity): GraphMastersSettingsTransfer
    {
        $settingsTransfer = (new GraphMastersSettingsTransfer())
            ->fromArray($entity->toArray(), true);

        foreach ($entity->getDstGraphmastersOpeningTimes() as $openingTimeEntity) {
            $settingsTransfer->addOpeningTimes(
                $this->openingTimeModel->entityToTransfer($openingTimeEntity)
            );
        }

        foreach ($entity->getDstGraphmastersCommissioningTimes() as $commissioningTimeEntity) {
            $settingsTransfer->addCommissioningTimes(
                $this->commissioningTimeModel->entityToTransfer($commissioningTimeEntity)
            );
        }

        return $settingsTransfer;
    }

    /**
     * @param int|null $idSettings
     *
     * @return DstGraphmastersSettings
     */
    protected function getEntity(?int $idSettings = null): DstGraphmastersSettings
    {
        if ($idSettings === null) {
            return new DstGraphmastersSettings();
        }

        $entity = $this
            ->queryContainer
            ->queryGraphMastersSettingsById($idSettings)
            ->findOne();

        if ($entity === null) {
            throw EntityNotFoundException::build($idSettings);
        }

        return $entity;
    }

    /**
     * @param DstGraphmastersSettings $settingsEntity
     * @param GraphMastersSettingsTransfer $transfer
     *
     * @throws PropelException
     */
    private function saveOpeningTimes(
        DstGraphmastersSettings $settingsEntity,
        GraphMastersSettingsTransfer $transfer
    ): void {
        $newOpeningTimeIds = [];

        foreach ($transfer->getOpeningTimes() as $openingTimeTransfer) {
            $openingTimeTransfer->setFkGraphmastersSettings($settingsEntity->getIdGraphmastersSettings());

            $openingTimeEntity = $this->openingTimeModel->save($openingTimeTransfer);

            $settingsEntity->addDstGraphmastersOpeningTime($openingTimeEntity);

            $newOpeningTimeIds[] = $openingTimeEntity->getIdGraphmastersOpeningTime();
        }

        foreach ($settingsEntity->getDstGraphmastersOpeningTimes() as $openingTimeEntity) {
            if (!in_array($openingTimeEntity->getIdGraphmastersOpeningTime(), $newOpeningTimeIds)) {
                $this->openingTimeModel->remove($openingTimeEntity->getIdGraphmastersOpeningTime());

                $settingsEntity->removeDstGraphmastersOpeningTime($openingTimeEntity);
            }
        }
    }

    /**
     * @param DstGraphmastersSettings $settingsEntity
     * @param GraphMastersSettingsTransfer $transfer
     *
     * @throws PropelException
     */
    private function saveCommissioningTimes(
        DstGraphmastersSettings $settingsEntity,
        GraphMastersSettingsTransfer $transfer
    ): void {
        $newCommissioningTimeIds = [];

        foreach ($transfer->getCommissioningTimes() as $commissioningTimeTransfer) {
            $commissioningTimeTransfer->setFkGraphmastersSettings($settingsEntity->getIdGraphmastersSettings());

            $commissioningTimeEntity = $this->commissioningTimeModel->save($commissioningTimeTransfer);

            $settingsEntity->addDstGraphmastersCommissioningTime($commissioningTimeEntity);

            $newCommissioningTimeIds[] = $commissioningTimeEntity->getIdGraphmastersCommissioningTime();
        }

        foreach ($settingsEntity->getDstGraphmastersCommissioningTimes() as $commissioningTimeEntity) {
            if (!in_array($commissioningTimeEntity->getIdGraphmastersCommissioningTime(), $newCommissioningTimeIds)) {
                $this->commissioningTimeModel->remove($commissioningTimeEntity->getIdGraphmastersCommissioningTime());

                $settingsEntity->removeDstGraphmastersCommissioningTime($commissioningTimeEntity);
            }
        }
    }
}
