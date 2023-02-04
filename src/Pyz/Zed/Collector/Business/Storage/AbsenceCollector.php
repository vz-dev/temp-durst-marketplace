<?php
/**
 * Durst - project - AbsenceCollector.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 28.12.21
 * Time: 20:21
 */

namespace Pyz\Zed\Collector\Business\Storage;


use Pyz\Shared\Absence\AbsenceConstants;
use Pyz\Zed\Absence\Persistence\AbsenceQueryContainerInterface;
use Spryker\Service\UtilDataReader\UtilDataReaderServiceInterface;
use Spryker\Zed\Collector\Business\Collector\Storage\AbstractStoragePdoCollector;

class AbsenceCollector extends AbstractStoragePdoCollector
{
    /**
     * @var AbsenceQueryContainerInterface
     */
    protected $absenceQuery;

    const KEY_ID_ABSENCE = 'id_absence';
    const KEY_FK_BRANCH = 'fk_branch';
    const KEY_START_DATE = 'start_date';
    const KEY_END_DATE = 'end_date';
    const KEY_DESCRIPTION = 'description';

    public function __construct(
        UtilDataReaderServiceInterface $utilDataReaderService,
        AbsenceQueryContainerInterface $absenceQuery
    ) {
        parent::__construct($utilDataReaderService);

        $this->absenceQuery = $absenceQuery;
    }

    /**
     * @param string $touchKey
     * @param array $collectItemData
     *
     * @return array
     */
    protected function collectItem($touchKey, array $collectItemData)
    {
        return [
            self::KEY_ID_ABSENCE => $collectItemData[self::KEY_ID_ABSENCE],
            self::KEY_FK_BRANCH => $collectItemData[self::KEY_FK_BRANCH],
            self::KEY_START_DATE => $collectItemData[self::KEY_START_DATE],
            self::KEY_END_DATE => $collectItemData[self::KEY_END_DATE],
            self::KEY_DESCRIPTION => $collectItemData[self::KEY_DESCRIPTION],
        ];
    }

    /**
     * @return string
     */
    protected function collectResourceType()
    {
        return AbsenceConstants::ABSENCE_RESOURCE_TYPE;
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
}
