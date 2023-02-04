<?php
/**
 * Durst - project - GraphmastersOpeningTimesStep.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 06.04.22
 * Time: 15:45
 */

namespace Pyz\Zed\DataImport\Business\Model\GraphmastersOpeningTimes;

use DateTime;
use DateTimeZone;
use Orm\Zed\GraphMasters\Persistence\DstGraphmastersOpeningTimeQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class GraphmastersOpeningTimesStep implements DataImportStepInterface
{
    protected const COL_FK_GRAPHMASTERS_SETTINGS = 'fk_graphmasters_settings';
    protected const COL_ID_OPENING_TIME = 'id_opening_time';
    protected const COL_WEEKDAY = 'weekday';
    protected const COL_START_TIME = 'start_time';
    protected const COL_END_TIME = 'end_time';

    /**
     * @param DataSetInterface $dataSet
     *
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $dateTime = new DateTime('now', new DateTimeZone('Europe/Berlin'));

        list($startHour, $startMin) = explode(':', $dataSet[self::COL_START_TIME]);
        $start = (clone $dateTime)->setTime($startHour, $startMin);

        list($endHour, $endMin) = explode(':', $dataSet[self::COL_END_TIME]);
        $end = (clone $dateTime)->setTime($endHour, $endMin);

        $openingTimeEntity = DstGraphmastersOpeningTimeQuery::create()
            ->filterByFkGraphmastersSettings($dataSet[self::COL_FK_GRAPHMASTERS_SETTINGS])
            ->filterByIdGraphmastersOpeningTime($dataSet[self::COL_ID_OPENING_TIME])
            ->findOneOrCreate();

        $openingTimeEntity->fromArray($dataSet->getArrayCopy());
        $openingTimeEntity
            ->setStartTime($start)
            ->setEndTime($end);

        if ($openingTimeEntity->isNew() || $openingTimeEntity->isModified()) {
            $openingTimeEntity->save();
        }
    }
}
