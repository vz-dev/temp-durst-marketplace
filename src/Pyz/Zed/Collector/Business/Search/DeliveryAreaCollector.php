<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 07.11.18
 * Time: 10:17
 */

namespace Pyz\Zed\Collector\Business\Search;


use Pyz\Shared\DeliveryArea\DeliveryAreaConstants;
use Pyz\Zed\Collector\CollectorConfig;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Search\AbstractSearchPdoCollector;
use Spryker\Zed\Search\Business\SearchFacadeInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

class DeliveryAreaCollector extends AbstractSearchPdoCollector
{

    /**
     * @var \Spryker\Zed\Search\Dependency\Plugin\PageMapInterface
     */
    protected $deliveryareaDataPageMapPlugin;

    /**
     * @var \Spryker\Zed\Search\Business\SearchFacadeInterface
     */
    protected $searchFacade;

    /**
     * DeliveryAreaCollector constructor.
     *
     * @param UtilDataReaderServiceInterface $utilDataReaderService
     * @param PageMapInterface $deliveryAreaDataPageMapPlugin
     * @param SearchFacadeInterface $searchFacade
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        PageMapInterface $deliveryAreaDataPageMapPlugin,
        SearchFacadeInterface $searchFacade
    )
    {
        parent::__construct($utilDataReaderService);

        $this->deliveryareaDataPageMapPlugin = $deliveryAreaDataPageMapPlugin;
        $this->searchFacade = $searchFacade;
    }


    /**
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem($touchKey, array $collectItemData) : array
    {
        $result = $this
            ->searchFacade
            ->transformPageMapToDocument($this->deliveryareaDataPageMapPlugin, $collectItemData, $this->locale);

        $result = $this->addExtraCollectorFields($result, $collectItemData);

        return $result;
    }

    /**
     * @return string
     */
    protected function collectResourceType() : string
    {
        return DeliveryAreaConstants::RESOURCE_TYPE_DELIVERY_AREA;
    }

    /**
     * @param array $result
     * @param array $collectItemData
     *
     * @return array
     */
    protected function addExtraCollectorFields(array $result, array $collectItemData) : array
    {
        $result[CollectorConfig::COLLECTOR_TOUCH_ID] = (int)$collectItemData[CollectorConfig::COLLECTOR_TOUCH_ID];
        $result[CollectorConfig::COLLECTOR_RESOURCE_ID] = (int)$collectItemData[CollectorConfig::COLLECTOR_RESOURCE_ID];
        $result[CollectorConfig::COLLECTOR_SEARCH_KEY] = $collectItemData[CollectorConfig::COLLECTOR_SEARCH_KEY];

        return $result;
    }
}