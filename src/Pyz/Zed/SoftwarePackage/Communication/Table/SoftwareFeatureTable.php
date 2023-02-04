<?php
/**
 * Durst - project - SoftwareFeatureTable.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 02.11.18
 * Time: 10:05
 */

namespace Pyz\Zed\SoftwarePackage\Communication\Table;


use Orm\Zed\Sales\Persistence\Map\DstSoftwareFeatureTableMap;
use Pyz\Zed\SoftwarePackage\Persistence\SoftwarePackageQueryContainerInterface;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class SoftwareFeatureTable extends AbstractTable
{
    public const ACTION = 'Action';

    public const LABEL_ACTION_EDIT = 'Edit';
    public const LABEL_ACTION_DELETE = 'Delete';

    public const LABEL_HEADER_ID = 'ID';
    public const LABEL_HEADER_CODE = 'Code';
    public const LABEL_HEADER_NAME = 'Name';
    public const LABEL_HEADER_DESCRIPTION = 'Beschreibung';


    const UPDATE_SOFTWARE_FEATURE_URL = '/software-package/software-feature/edit';
    const DELETE_SOFTWARE_FEATURE_URL = '/software-package/software-feature/delete';

    const PARAM_ID_SOFTWARE_FEATURE = 'id-software-feature';

    /**
     * @var SoftwarePackageQueryContainerInterface
     */
    protected $softwarePackageQueryContainer;

    /**
     * PaymentMethodsTable constructor.
     * @param SoftwarePackageQueryContainerInterface $softwarePackageQueryContainer
     */
    public function __construct(SoftwarePackageQueryContainerInterface $softwarePackageQueryContainer)
    {
        $this->softwarePackageQueryContainer = $softwarePackageQueryContainer;
    }


    /**
     * @param TableConfiguration $config
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE => static::LABEL_HEADER_ID,
            DstSoftwareFeatureTableMap::COL_CODE => static::LABEL_HEADER_CODE,
            DstSoftwareFeatureTableMap::COL_NAME => static::LABEL_HEADER_NAME,
            DstSoftwareFeatureTableMap::COL_DESCRIPTION => static::LABEL_HEADER_DESCRIPTION,
            self::ACTION => self::ACTION,
        ]);

        $config->setRawColumns([self::ACTION]);

        $config->setSortable([
            DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE,
            DstSoftwareFeatureTableMap::COL_NAME,
        ]);

        $config->setSearchable([
            DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE,
            DstSoftwareFeatureTableMap::COL_NAME,
        ]);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->softwarePackageQueryContainer->querySoftwareFeature();
        $queryResults = $this->runQuery($query, $config);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = [
                DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE => $item[DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE],
                DstSoftwareFeatureTableMap::COL_CODE => $item[DstSoftwareFeatureTableMap::COL_CODE],
                DstSoftwareFeatureTableMap::COL_NAME => $item[DstSoftwareFeatureTableMap::COL_NAME],
                DstSoftwareFeatureTableMap::COL_DESCRIPTION => $item[DstSoftwareFeatureTableMap::COL_DESCRIPTION],
                self::ACTION => implode(' ', $this->createActionButtons($item)),
            ];
        }

        return $results;
    }

    /**
     * @param array $feature
     * @return array
     */
    public function createActionButtons(array $feature)
    {
        $urls = [];

        $urls[] = $this->generateEditButton(
            Url::generate(self::UPDATE_SOFTWARE_FEATURE_URL, [
                self::PARAM_ID_SOFTWARE_FEATURE => $feature[DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE],
            ]),
            static::LABEL_ACTION_EDIT
        );

        $urls[] = $this->generateRemoveButton(self::DELETE_SOFTWARE_FEATURE_URL, static::LABEL_ACTION_DELETE, [
            self::PARAM_ID_SOFTWARE_FEATURE => $feature[DstSoftwareFeatureTableMap::COL_ID_SOFTWARE_FEATURE],
        ]);

        return $urls;
    }
}