<?php
/**
 * Durst - project - SearchStub.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.18
 * Time: 15:18
 */

namespace Pyz\Client\AppRestApi\Search;

use ArrayObject;
use DateTime;
use DateTimeZone;
use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\ConcreteTimeSlotTransfer;
use Pyz\Client\AppRestApi\AppRestApiConfig as Config;
use Pyz\Client\DeliveryArea\Plugin\Query\MatchTimeSlotIdsQuery;
use Pyz\Client\DeliveryArea\Plugin\Query\TimeSlotFutureDatesMatchBranchIdsQuery;
use Pyz\Client\DeliveryArea\Plugin\Query\TimeSlotMatchZipCodeAndBranchIdsQuery;
use Pyz\Client\DeliveryArea\Plugin\ResultFormatter\ConcreteTimeSlotStartDateResultFormatter;
use Pyz\Client\DeliveryArea\Plugin\ResultFormatter\ConcreteTimeSlotTransferResultFormatter;
use Pyz\Client\GraphMasters\Plugin\Query\GMTimeSlotQuery;
use Pyz\Client\GraphMasters\Plugin\ResultFormatter\GMTimeSlotResultFormatter;
use Pyz\Client\Merchant\Plugin\Query\MatchBranchIdQuery;
use Pyz\Client\Merchant\Plugin\ResultFormatter\BranchTransferResultFormatter;
use Pyz\Client\Search\SearchClientInterface;

class SearchStub implements SearchStubInterface
{
    /**
     * @var SearchClientInterface
     */
    protected $searchClient;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SearchStub constructor.
     *
     * @param SearchClientInterface $searchClient
     * @param Config $config
     */
    public function __construct(
        SearchClientInterface $searchClient,
        $config
    ) {
        $this->searchClient = $searchClient;
        $this->config = $config;
    }

    /**
     * @param array $timeSlotIds
     *
     * @return array
     */
    public function getTimeSlotsForIds(array $timeSlotIds): array
    {
        $results = $this
            ->searchClient
            ->searchTimeSlotIndex(
                new MatchTimeSlotIdsQuery($timeSlotIds, Config::SEARCH_LIMIT),
                [
                    new ConcreteTimeSlotTransferResultFormatter()
                ]
            );

        return $results[ConcreteTimeSlotTransferResultFormatter::NAME];
    }

    /**
     * @param AppApiRequestTransfer $transfer
     *
     * @return BranchTransfer
     */
    public function getBranchById(AppApiRequestTransfer $transfer): BranchTransfer
    {
        $results = $this
            ->searchClient
            ->search(
                new MatchBranchIdQuery(
                    $transfer->getIdBranch()
                ),
                [
                    new BranchTransferResultFormatter(),
                ]
            );

        return $results[BranchTransferResultFormatter::NAME][0];
    }

    /**
     * @param AppApiRequestTransfer $transfer
     * @param bool $fetchFullyBookedTimeSlots
     *
     * @return AppApiResponseTransfer
     */
    public function getTimeSlotsForBranches(
        AppApiRequestTransfer $transfer,
        bool $fetchFullyBookedTimeSlots = false
    ): AppApiResponseTransfer {
        $results = $this
            ->searchClient
            ->searchTimeSlotIndex(
                new TimeSlotMatchZipCodeAndBranchIdsQuery(
                    $transfer->getZipCode(),
                    $transfer->getBranchIds(),
                    $transfer->getUseDayLimit() === true ? null : Config::SEARCH_LIMIT,
                    $transfer->getRequestedProductsAmount(),
                    $transfer->getRequestedProductsWeight(),
                    $this->getTimeZoneFromConfig(),
                    $fetchFullyBookedTimeSlots
                ),
                [
                    new ConcreteTimeSlotTransferResultFormatter(),
                ]
            );

        $timeSlotTransfers = $results[ConcreteTimeSlotTransferResultFormatter::NAME];

        if ($transfer->getUseDayLimit()) {
            $timeSlotTransfers = $this->applyDayLimit($timeSlotTransfers, $transfer->getBranchIds());
        }

        return (new AppApiResponseTransfer())
            ->setTimeSlots(new ArrayObject($timeSlotTransfers));
    }

    /**
     * @return DateTimeZone
     */
    protected function getTimeZoneFromConfig(): DateTimeZone
    {
        return (new DateTimeZone(
            $this->config->getProjectTimeZone()
        ));
    }

    /**
     * @param ConcreteTimeSlotTransfer[] $timeSlotTransfers
     * @param array $branchIds
     * @return ConcreteTimeSlotTransfer[]
     */
    protected function applyDayLimit(array $timeSlotTransfers, array $branchIds): array
    {
        $dayLimit = $this->config->getTimeSlotsDayLimit();

        $futureDates = $this->findFutureDatesWithinDayLimit($branchIds, $dayLimit);

        $dates = $this->timeSlotForTodayExists($timeSlotTransfers)
            ? array_merge(
                [(new DateTime('today'))->format('Y-m-d')],
                array_slice($futureDates, 0, -1)
            )
            : $futureDates;

        $timeSlotTransfers = $this->filterTimeSlotsByDates($timeSlotTransfers, $dates);

        return $timeSlotTransfers;
    }

    /**
     * @param array $branchIds
     * @param int $dayLimit
     * @return array
     */
    protected function findFutureDatesWithinDayLimit(array $branchIds, int $dayLimit): array
    {
        $searchResults = $this
            ->searchClient
            ->searchTimeSlotIndex(
                new TimeSlotFutureDatesMatchBranchIdsQuery($branchIds, $this->getTimeZoneFromConfig()),
                [new ConcreteTimeSlotStartDateResultFormatter()]
            );

        $startDateTimestamps = $searchResults[ConcreteTimeSlotStartDateResultFormatter::NAME];

        $dates = [];

        foreach ($startDateTimestamps as $startDateTimestamp) {
            $date = (new DateTime())->setTimestamp($startDateTimestamp)->format('Y-m-d');

            if (in_array($date, $dates) === false) {
                $dates[] = $date;
            }
        }

        return array_slice($dates, 0, $dayLimit);
    }

    /**
     * @param ConcreteTimeSlotTransfer[] $timeSlotTransfers
     * @param array $dates
     * @return ConcreteTimeSlotTransfer[]
     */
    protected function filterTimeSlotsByDates(array $timeSlotTransfers, array $dates): array
    {
        return array_filter($timeSlotTransfers, function($timeSlotTransfer) use ($dates) {
            $date = (new DateTime())->setTimestamp($timeSlotTransfer->getStartTimeRaw())->format('Y-m-d');

            return in_array($date, $dates);
        });
    }

    /**
     * @param ConcreteTimeSlotTransfer[] $timeSlotTransfers
     * @return bool
     */
    protected function timeSlotForTodayExists(array $timeSlotTransfers): bool
    {
        $today = (new DateTime('today'))->format('Y-m-d');

        foreach ($timeSlotTransfers as $timeSlotTransfer) {
            $date = (new DateTime())->setTimestamp($timeSlotTransfer->getStartTimeRaw())->format('Y-m-d');

            if ($date === $today) {
                return true;
            }
        }

        return false;
    }

    public function getGMTimeSlots(
        AppApiRequestTransfer $requestTransfer
    ): AppApiRequestTransfer
    {
        $results = $this
            ->searchClient
            ->searchGMTimeSlotIndex(
                new GMTimeSlotQuery(
                    $requestTransfer->getGraphmasterSettings(),
                    $this->config->getGMTimeSlotsDayLimit()
                ),
                [
                    new GMTimeSlotResultFormatter(),
                ]
            );

        $requestTransfer
            ->setTimeSlots($results['gm_time_slot_result_formatter']);

        return $requestTransfer;
    }
}
