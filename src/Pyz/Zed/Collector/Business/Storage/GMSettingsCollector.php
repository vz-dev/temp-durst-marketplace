<?php
/**
 * Durst - project - GMSettingsCollector.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 18.10.21
 * Time: 15:08
 */

namespace Pyz\Zed\Collector\Business\Storage;


use Orm\Zed\DeliveryArea\Persistence\Map\SpyDeliveryAreaTableMap;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Pyz\Zed\GraphMasters\Persistence\GraphMastersQueryContainerInterface;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Storage\AbstractStoragePdoCollector;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class GMSettingsCollector extends AbstractStoragePdoCollector
{
    /**
     * @var GraphMastersQueryContainerInterface
     */
    protected $graphmastersQuery;


    const KEY_FK_BRANCH = 'fk_branch';
    const KEY_DEPOT_API_ID = 'depot_api_id';
    const KEY_LEAD_TIME = 'lead_time';
    const KEY_BUFFER_TIME = 'buffer_time';
    const KEY_ACTIVE = 'is_active';
    const KEY_ID_SETTING = 'id_graphmasters_settings';
    const KEY_COMMISSIONING_TIMES = 'commissioning_times';
    const KEY_OPENING_TIMES = 'opening_times';
    const KEY_DELIVERY_AREAS = 'delivery_areas';


    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        GraphMastersQueryContainerInterface $graphmastersQuery
    ) {
        parent::__construct($utilDataReaderService);

        $this->graphmastersQuery = $graphmastersQuery;
    }
    /**
     * @return string
     */
    protected function collectResourceType() : string
    {
        return GraphMastersConstants::GRAPHMASTERS_SETTINGS_RESOURCE_TYPE;
    }

    /**
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem($touchKey, array $collectItemData) : array
    {
        return [
            self::KEY_ACTIVE => $collectItemData[self::KEY_ACTIVE],
            self::KEY_DEPOT_API_ID => $collectItemData[self::KEY_DEPOT_API_ID],
            self::KEY_LEAD_TIME => $collectItemData[self::KEY_LEAD_TIME],
            self::KEY_BUFFER_TIME => $collectItemData[self::KEY_BUFFER_TIME],
            self::KEY_FK_BRANCH => $collectItemData[self::KEY_FK_BRANCH],
            self::KEY_DELIVERY_AREAS => $this->getDeliveryAreaCategories($collectItemData[self::KEY_FK_BRANCH]),
            self::KEY_OPENING_TIMES => $this->getOpeningTimes($collectItemData[self::KEY_ID_SETTING]),
            self::KEY_COMMISSIONING_TIMES => $this->getCommisioningTimes($collectItemData[self::KEY_ID_SETTING]),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param array $data
     * @param string $localeName
     * @param array $collectedItemData
     *
     * @return string
     */
    protected function collectKey($data, $localeName, array $collectedItemData) : string
    {
        return $this->generateKey($collectedItemData[static::KEY_FK_BRANCH], $localeName);
    }

    /**
     * @param int $idSettings
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    protected function getOpeningTimes(int $idSettings) : array
    {
        $openingTimes = [];

        $openingTimesEntities = $this
            ->graphmastersQuery
            ->createGraphmastersOpeningTimeQuery()
            ->filterByFkGraphmastersSettings($idSettings)
            ->find();

        foreach ($openingTimesEntities as $entity){

            if(array_key_exists($entity->getWeekday(), $openingTimes) !== true){
                $openingTimes[$entity->getWeekday()] = [];
            }

            $openingTimes[$entity->getWeekday()][] = [
                'start_time' => $entity->getStartTime()->format('H:i') ,
                'end_time' => $entity->getEndTime()->format('H:i') ,
                'pause_start_time' => $entity->getPauseStartTime(),
                'pause_end_time' => $entity->getPauseEndTime()
            ];
        }

        return $openingTimes;
    }

    /**
     * @param int $idSettings
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    protected function getCommisioningTimes(int $idSettings) : array
    {
        $commissioningTimes = [];

        $commissioningTimesEntities = $this
            ->graphmastersQuery
            ->createGraphmastersCommissioningTimeQuery()
            ->filterByFkGraphmastersSettings($idSettings)
            ->find();

        foreach ($commissioningTimesEntities as $entity){

            if(array_key_exists($entity->getWeekday(), $commissioningTimes) !== true){
                $commissioningTimes[$entity->getWeekday()] = [];
            }

            $commissioningTimes[$entity->getWeekday()][] = [
                'start_time' => $entity->getStartTime()->format('H:i') ,
                'end_time' => $entity->getEndTime()->format('H:i'),
            ];
        }

        return $commissioningTimes;
    }

    protected function getDeliveryAreaCategories(int $idBranch) : array
    {
        $deliveryAreaCats = [];

        $deliveryAreaCatsEntities = $this
            ->graphmastersQuery
            ->queryGraphmastersDeliveryAreaCategory()
            ->filterByFkBranch($idBranch)
            ->filterByIsActive(true)
            ->find();

        foreach ($deliveryAreaCatsEntities as $entity){

            $deliveryAreaCats[] = [
                'category_name' => $entity->getCategoryName(),
                'slot_size' => $entity->getSlotSize(),
                'edtm_cutoff_small' => $entity->getEdtmCutoffSmall(),
                'edtm_cutoff_medium' => $entity->getEdtmCutoffMedium(),
                'edtm_cutoff_large' => $entity->getEdtmCutoffLarge(),
                'edtm_cutoff_xlarge' => $entity->getEdtmCutoffXlarge(),
                'min_value' => $entity->getMinValue(),
                'zip_codes' => $this->getZipCodesForCategory($entity->getIdDeliveryAreaCategory()),
            ];
        }

        return $deliveryAreaCats;
    }

    /**
     * @param int $idDeliveryAreaCategory
     * @return array
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    protected function getZipCodesForCategory(int $idDeliveryAreaCategory) : array
    {
        return $this
            ->graphmastersQuery
            ->queryCategoryToDeliveryArea()
            ->joinSpyDeliveryArea()
            ->filterByFkDeliveryAreaCategory($idDeliveryAreaCategory)
            ->select(SpyDeliveryAreaTableMap::COL_ZIP_CODE)
            ->find()
            ->toArray();
    }
}
