<?php

namespace Pyz\Zed\DataImport\Business\Model\GraphmastersSettings;

use Orm\Zed\GraphMasters\Persistence\DstGraphmastersSettingsQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class GraphmastersSettingsStep implements DataImportStepInterface
{
    protected const COL_FK_BRANCH = 'fk_branch';

    /**
     * @param DataSetInterface $dataSet
     * @throws PropelException
     * @throws AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $settingsEntity = DstGraphmastersSettingsQuery::create()
            ->filterByFkBranch($dataSet[self::COL_FK_BRANCH])
            ->findOneOrCreate();

        $settingsEntity->fromArray($dataSet->getArrayCopy());
        $settingsEntity->setIsActive(true);

        if ($settingsEntity->isNew() || $settingsEntity->isModified()) {
            $settingsEntity->save();
        }
    }
}
