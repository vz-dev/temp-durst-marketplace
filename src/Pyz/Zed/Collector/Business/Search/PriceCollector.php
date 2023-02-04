<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 09.11.18
 * Time: 15:12
 */

namespace Pyz\Zed\Collector\Business\Search;


use Pyz\Shared\MerchantPrice\MerchantPriceConstants;
use Pyz\Zed\Collector\CollectorConfig;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Search\AbstractSearchPdoCollector;
use Spryker\Zed\Search\Business\SearchFacadeInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

class PriceCollector extends AbstractSearchPdoCollector
{
    /**
     * @var \Spryker\Zed\Search\Dependency\Plugin\PageMapInterface
     */
    protected $priceDataPageMapPlugin;

    /**
     * @var \Spryker\Zed\Search\Business\SearchFacadeInterface
     */
    protected $searchFacade;

    /**
     * PriceCollector constructor.
     *
     * @param UtilDataReaderServiceInterface $utilDataReaderService
     * @param PageMapInterface $priceDataPageMapPlugin
     * @param SearchFacadeInterface $searchFacade
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        PageMapInterface $priceDataPageMapPlugin,
        SearchFacadeInterface $searchFacade
    )
    {
        parent::__construct($utilDataReaderService);

        $this->priceDataPageMapPlugin = $priceDataPageMapPlugin;
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
            ->transformPageMapToDocument($this->priceDataPageMapPlugin, $collectItemData, $this->locale);

        $result = $this->addExtraCollectorFields($result, $collectItemData);

        return $result;
    }

    /**
     * @return string
     */
    protected function collectResourceType() : string
    {
        return MerchantPriceConstants::RESOURCE_TYPE_PRICE;
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