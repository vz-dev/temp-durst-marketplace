<?php
/**
 * Durst - project - DriverWriterStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-20
 * Time: 13:54
 */

namespace Pyz\Zed\DataImport\Business\Model\Driver;

use DateTime;
use Orm\Zed\Driver\Persistence\DstDriverQuery;
use Orm\Zed\Merchant\Persistence\SpyBranch;
use Orm\Zed\Merchant\Persistence\SpyBranchQuery;
use Orm\Zed\Tour\Persistence\DstDrivingLicence;
use Orm\Zed\Tour\Persistence\DstDrivingLicenceQuery;
use Pyz\Zed\DataImport\Business\Exception\InvalidDataException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class DriverWriterStep implements DataImportStepInterface
{
    protected const COL_EMAIL = 'email';
    protected const COL_DRIVING_LICENCE = 'driving_licence';
    protected const COL_BRANCH = 'branch';

    protected const DATE_FORMAT = 'd.m.Y';

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = DstDriverQuery::create()
            ->filterBySpyBranch(
                $this->getBrachEntityByName($dataSet[self::COL_BRANCH])
            )
            ->filterByDstDrivingLicence(
                $this->getDrivingLicenceEntityByCode($dataSet[self::COL_DRIVING_LICENCE])
            )
            ->filterByEmail($dataSet[self::COL_EMAIL])
            ->findOneOrCreate();

        $entity->fromArray($dataSet->getArrayCopy());

        if ($entity->isNew() || $entity->isModified()) {
            $entity->save();
        }
    }

    /**
     * @param string $code
     *
     * @throws \Pyz\Zed\DataImport\Business\Exception\InvalidDataException
     *
     * @return \Orm\Zed\Tour\Persistence\DstDrivingLicence
     */
    protected function getDrivingLicenceEntityByCode(string $code): DstDrivingLicence
    {
        $entity = DstDrivingLicenceQuery::create()
            ->findOneByCode($code);

        if ($entity === null) {
            throw new InvalidDataException(
                sprintf(
                    'driving licence with code %s could not be found',
                    $code
                )
            );
        }

        return $entity;
    }

    /**
     * @param string $name
     *
     * @throws \Pyz\Zed\DataImport\Business\Exception\InvalidDataException
     *
     * @return \Orm\Zed\Merchant\Persistence\SpyBranch
     */
    protected function getBrachEntityByName(string $name): SpyBranch
    {
        $entity = SpyBranchQuery::create()
            ->findOneByName($name);

        if ($entity === null) {
            throw new InvalidDataException(
                sprintf(
                    'branch with name %s could not be found',
                    $name
                )
            );
        }

        return $entity;
    }
}
