<?php

namespace Pyz\Zed\GraphMasters\Communication\Table;

use DateTime;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersTour;
use Orm\Zed\GraphMasters\Persistence\Map\DstGraphmastersTourTableMap;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\GraphMasters\Communication\Controller\TourController;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class TourTable extends AbstractTable
{
    protected const HEADER_ID = 'ID';
    protected const HEADER_REFERENCE = 'Referenz';
    protected const HEADER_ORIGINAL_ID = 'Original-ID';
    protected const HEADER_START_ETA = 'Startzeitpunkt';
    protected const HEADER_DESTINATION_ETA = 'Endzeitpunkt';
    protected const HEADER_STATUS = 'Status';
    protected const HEADER_CUTOFF_TIME = 'Cutoff-Time';
    protected const HEADER_BRANCH_ID = 'Branch-ID';
    protected const HEADER_BRANCH_NAME = 'Branch-Name';
    protected const HEADER_ACTIONS = 'Aktionen';

    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var GraphMastersFacadeInterface
     */
    protected $facade;

    /**
     * @param GraphMastersQueryContainerInterface $queryContainer
     * @param GraphMastersFacadeInterface $facade
     */
    public function __construct(
        GraphMastersQueryContainerInterface $queryContainer,
        GraphMastersFacadeInterface $facade
    ) {
        $this->queryContainer = $queryContainer;
        $this->facade = $facade;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstGraphmastersTourTableMap::COL_ID_GRAPHMASTERS_TOUR => static::HEADER_ID,
            DstGraphmastersTourTableMap::COL_REFERENCE => static::HEADER_REFERENCE,
            DstGraphmastersTourTableMap::COL_ORIGINAL_ID => static::HEADER_ORIGINAL_ID,
            DstGraphmastersTourTableMap::COL_TOUR_START_ETA => static::HEADER_START_ETA,
            DstGraphmastersTourTableMap::COL_TOUR_DESTINATION_ETA => static::HEADER_DESTINATION_ETA,
            DstGraphmastersTourTableMap::COL_TOUR_COMMISSIONING_CUT_OFF => static::HEADER_CUTOFF_TIME,
            DstGraphmastersTourTableMap::COL_TOUR_STATUS => static::HEADER_STATUS,
            DstGraphmastersTourTableMap::COL_FK_BRANCH => static::HEADER_BRANCH_ID,
            SpyBranchTableMap::COL_NAME => static::HEADER_BRANCH_NAME,
            static::HEADER_ACTIONS => static::HEADER_ACTIONS,
        ]);

        $config->setRawColumns([static::HEADER_ACTIONS]);

        $config->setSearchable([
            DstGraphmastersTourTableMap::COL_ID_GRAPHMASTERS_TOUR,
            DstGraphmastersTourTableMap::COL_REFERENCE,
            DstGraphmastersTourTableMap::COL_ORIGINAL_ID,
            SpyBranchTableMap::COL_NAME,
        ]);

        $config->setSortable([
            DstGraphmastersTourTableMap::COL_TOUR_START_ETA,
            DstGraphmastersTourTableMap::COL_TOUR_DESTINATION_ETA,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return array
     *
     * @throws PropelException
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->createGraphmastersTourQuery()
            ->joinWithSpyBranch();

        $tours = $this->runQuery($query, $config, true);

        $results = [];

        /** @var DstGraphmastersTour $tour */
        foreach ($tours as $tour) {
            $results[] = [
                DstGraphmastersTourTableMap::COL_ID_GRAPHMASTERS_TOUR => $tour->getIdGraphmastersTour(),
                DstGraphmastersTourTableMap::COL_REFERENCE => $tour->getReference(),
                DstGraphmastersTourTableMap::COL_ORIGINAL_ID => $tour->getOriginalId(),
                DstGraphmastersTourTableMap::COL_TOUR_START_ETA => $tour->getTourStartEta()->format('d.m.Y, H:i'),
                DstGraphmastersTourTableMap::COL_TOUR_DESTINATION_ETA => $tour->getTourDestinationEta()->format('d.m.Y, H:i'),
                DstGraphmastersTourTableMap::COL_TOUR_COMMISSIONING_CUT_OFF => $this->getCutOffTime($tour->getTourCommissioningCutOff()),
                DstGraphmastersTourTableMap::COL_TOUR_STATUS => $tour->getTourStatus(),
                DstGraphmastersTourTableMap::COL_FK_BRANCH => $tour->getFkBranch(),
                SpyBranchTableMap::COL_NAME => $tour->getSpyBranch()->getName(),
                static::HEADER_ACTIONS => $this->formatActionButtons($tour->getIdGraphmastersTour()),
            ];
        }

        return $results;
    }

    /**
     * @param int $idTour
     *
     * @return string
     */
    protected function formatActionButtons(int $idTour): string
    {
        $buttons = [];

        $buttons[] = $this
            ->generateViewButton(
                sprintf(
                    '%s?%s=%d',
                    TourController::URL_DETAIL,
                    TourController::PARAM_ID_TOUR,
                    $idTour
                ),
                'Anzeigen'
            );

        return implode('', $buttons);
    }

    /**
     * @param DateTime|null $cutOff
     * @return string
     */
    protected function getCutOffTime(?DateTime $cutOff) : string
    {
        if($cutOff === null)
        {
            return 'n/a';
        }

        return $cutOff->format('d.m.Y, H:i');
    }
}
