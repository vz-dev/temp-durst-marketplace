<?php
/**
 * Durst - project - GMTimeslotDataPageMapBuilder.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 11.10.21
 * Time: 11:21
 */

namespace Pyz\Zed\GraphMasters\Business\Map;


use Generated\Shared\Search\GmTimeSlotIndexMap;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Pyz\Shared\GraphMasters\GraphMastersConstants;
use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;

class GMTimeslotDataPageMapBuilder
{
    /**
     * @param PageMapBuilderInterface $pageMapBuilder
     * @param array $timeslotData
     * @param LocaleTransfer $localeTransfer
     * @return PageMapTransfer
     * @throws \Exception
     */
    public function buildPageMap(
        PageMapBuilderInterface $pageMapBuilder,
        array $timeslotData,
        LocaleTransfer $localeTransfer
    ) : PageMapTransfer
    {
        $pageMapTransfer = (new PageMapTransfer())
            ->setStore(Store::getInstance()->getStoreName())
            ->setLocale($localeTransfer->getLocaleName())
            ->setType(GraphMastersConstants::GRAPHMASTERS_TIMESLOT_RESOURCE_TYPE);

        $pageMapBuilder
            ->addSearchResultData($pageMapTransfer, GMTimeSlotIndexMap::ID_TIME_SLOT, $timeslotData[GMTimeSlotIndexMap::ID_TIME_SLOT])
            ->addSearchResultData($pageMapTransfer, GMTimeSlotIndexMap::TIME_SLOT_START_DATE, $timeslotData[GMTimeSlotIndexMap::TIME_SLOT_START_DATE])
            ->addSearchResultData($pageMapTransfer, GMTimeSlotIndexMap::TIME_SLOT_END_DATE, $timeslotData[GMTimeSlotIndexMap::TIME_SLOT_END_DATE]);

        return $pageMapTransfer;
    }
}
