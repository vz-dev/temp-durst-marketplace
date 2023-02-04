<?php
/**
 * Durst - project - CredentialsTable.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 06.11.20
 * Time: 11:05
 */

namespace Pyz\Zed\Integra\Communication\Table;

use Orm\Zed\Integra\Persistence\Map\PyzIntegraCredentialsTableMap;
use Orm\Zed\Integra\Persistence\PyzIntegraCredentials;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\Integra\Communication\Controller\IndexController;
use Pyz\Zed\Integra\Persistence\IntegraQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class CredentialsTable extends AbstractTable
{
    protected const COL_ID = '#';
    protected const COL_ACTIVE = 'aktiv?';
    protected const COL_BRANCH = 'Branch';
    protected const COL_ACTION = 'Action';

    /**
     * @var IntegraQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * CredentialsTable constructor.
     *
     * @param IntegraQueryContainerInterface $queryContainer
     */
    public function __construct(IntegraQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            PyzIntegraCredentialsTableMap::COL_ID_INTEGRA_CREDENTIALS => static::COL_ID,
            PyzIntegraCredentialsTableMap::COL_USE_INTEGRA => static::COL_ACTIVE,
            PyzIntegraCredentialsTableMap::COL_FK_BRANCH => static::COL_BRANCH,
            static::COL_ACTION => static::COL_ACTION,
        ]);

        $config->setRawColumns([
            PyzIntegraCredentialsTableMap::COL_USE_INTEGRA,
            static::COL_ACTION,
        ]);

        $config->setSearchable([
        ]);

        $config->setSortable([
            PyzIntegraCredentialsTableMap::COL_ID_INTEGRA_CREDENTIALS,
            PyzIntegraCredentialsTableMap::COL_FK_BRANCH,
        ]);

        return $config;
    }

    /**
     * @param TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this
            ->queryContainer
            ->queryIntegraCredentials()
            ->useSpyBranchQuery()
            ->endUse()
            ->withColumn(SpyBranchTableMap::COL_NAME, static::COL_BRANCH);

        /** @var PyzIntegraCredentials[] $credentials */
        $credentials = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($credentials as $credential) {
            $results[] = [
                PyzIntegraCredentialsTableMap::COL_ID_INTEGRA_CREDENTIALS => $credential->getIdIntegraCredentials(),
                PyzIntegraCredentialsTableMap::COL_USE_INTEGRA => $this->formatBool($credential->getUseIntegra()),
                PyzIntegraCredentialsTableMap::COL_FK_BRANCH => $credential->getVirtualColumn(self::COL_BRANCH),
                static::COL_ACTION => $this->formatActionButtons($credential->getIdIntegraCredentials()),
            ];
        }

        return $results;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function formatBool($value): string
    {
        if ($value) {
            return '<i style="color: #108548" class="fa fa-check"></i>';
        }

        return '<i style="color: #ed5565" class="fa fa-times red"></i>';
    }

    /**
     * @param int $idCredentials
     *
     * @return string
     */
    protected function formatActionButtons(int $idCredentials): string
    {
        $buttons = [];
        $buttons[] = $this
            ->generateEditButton(
                sprintf(
                    '%s?%s=%d',
                    IndexController::URL_EDIT,
                    IndexController::PARAM_ID_CREDENTIALS,
                    $idCredentials
                ),
                'Edit'
            );

        $buttons[] = $this
            ->generateRemoveButton(
                sprintf(
                    '%s?%s=%d',
                    IndexController::URL_REMOVE,
                    IndexController::PARAM_ID_CREDENTIALS,
                    $idCredentials
                ),
                'Delete'
            );

        return implode('', $buttons);
    }
}
