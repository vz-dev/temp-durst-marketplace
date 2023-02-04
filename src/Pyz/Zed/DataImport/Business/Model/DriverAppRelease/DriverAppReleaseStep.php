<?php

namespace Pyz\Zed\DataImport\Business\Model\DriverAppRelease;

use Orm\Zed\DriverApp\Persistence\DstDriverAppReleaseQuery;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

class DriverAppReleaseStep implements DataImportStepInterface
{
    protected const COL_VERSION = 'version';

    /**
     * @param DataSetInterface $dataSet
     *
     * @return void
     * @throws PropelException|AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = DstDriverAppReleaseQuery::create()
            ->filterByVersion($dataSet[self::COL_VERSION])
            ->findOneOrCreate();

        $entity->fromArray($dataSet->getArrayCopy());

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }
}
