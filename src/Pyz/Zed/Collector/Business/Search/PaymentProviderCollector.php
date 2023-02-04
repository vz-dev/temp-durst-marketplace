<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 13.11.18
 * Time: 10:00
 */

namespace Pyz\Zed\Collector\Business\Search;


use Pyz\Shared\Merchant\MerchantConstants;
use Pyz\Zed\Collector\CollectorConfig;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Search\AbstractSearchPdoCollector;
use Spryker\Zed\Search\Business\SearchFacadeInterface;
use Spryker\Zed\Search\Dependency\Plugin\PageMapInterface;

class PaymentProviderCollector extends AbstractSearchPdoCollector
{
    /**
     * @var \Spryker\Zed\Search\Dependency\Plugin\PageMapInterface
     */
    protected $paymentProviderDataPageMapPlugin;

    /**
     * @var \Spryker\Zed\Search\Business\SearchFacadeInterface
     */
    protected $searchFacade;

    /**
     * PaymentProviderCollector constructor.
     *
     * @param UtilDataReaderServiceInterface $utilDataReaderService
     * @param PageMapInterface $paymentProviderDataPageMapPlugin
     * @param SearchFacadeInterface $searchFacade
     */
    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        PageMapInterface $paymentProviderDataPageMapPlugin,
        SearchFacadeInterface $searchFacade
    )
    {
        parent::__construct($utilDataReaderService);

        $this->paymentProviderDataPageMapPlugin = $paymentProviderDataPageMapPlugin;
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
            ->transformPageMapToDocument($this->paymentProviderDataPageMapPlugin, $collectItemData, $this->locale);

        $result = $this->addExtraCollectorFields($result, $collectItemData);

        return $result;
    }

    /**
     * @return string
     */
    protected function collectResourceType() : string
    {
        return MerchantConstants::RESOURCE_TYPE_PAYMENT_PROVIDER;
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