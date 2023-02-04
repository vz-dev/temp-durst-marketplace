<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-02-07
 * Time: 09:48
 */

namespace Pyz\Zed\DataImport\Business\Model\License;

use Orm\Zed\Sales\Persistence\DstLicense;
use Orm\Zed\Sales\Persistence\DstLicenseQuery;
use Orm\Zed\Sales\Persistence\Map\DstLicenseTableMap;
use Propel\Runtime\Map\TableMap;
use Pyz\Shared\SoftwarePackage\SoftwarePackageConstants;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class LicenseKeyStep implements DataImportStepInterface
{
    protected const LICENSE_MAP = [
        0 => [
            'key' => DstLicenseTableMap::COL_LICENSE_ID,
            'type' => 'int'
        ],
        1 => [
            'key' => DstLicenseTableMap::COL_FK_SOFTWARE_PACKAGE,
            'type' => 'int'
        ],
        2 => [
            'key' => DstLicenseTableMap::COL_UNITS,
            'type' => 'int'
        ],
        3 => [
            'key' => DstLicenseTableMap::COL_LICENSE_KEY,
            'type' => 'string'
        ],
        5 => [
            'key' => DstLicenseTableMap::COL_STATUS,
            'type' => 'string'
        ],
        14 => [
            'key' => DstLicenseTableMap::COL_VALID_FROM,
            'type' => 'date'
        ],
        15 => [
            'key' => DstLicenseTableMap::COL_VALID_TO,
            'type' => 'date_or_null'
        ]
    ];

    /**
     * @var array
     */
    protected $keys;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @param DataSetInterface $dataSet
     * @return void
     * @throws \Exception
     */
    public function execute(DataSetInterface $dataSet)
    {
        try {
            $this->keys = DstLicenseTableMap::getFieldNames(TableMap::TYPE_FIELDNAME);
            $this->columns = DstLicenseTableMap::getFieldNames(TableMap::TYPE_COLNAME);

            $dataArray = $this->map($dataSet);

            if (count($dataArray) > 0) {
                $idLicenseKey = array_search('license_id', $this->keys);
                $licenseKey = array_search('license_key', $this->keys);

                $idLicense = $dataArray[$this->keys[$idLicenseKey]];
                $license = $dataArray[$this->keys[$licenseKey]];

                $licenseKeyEntity = $this
                    ->findEntity($idLicense, $license);

                $licenseKeyEntity
                    ->fromArray($dataArray);

                $licenseKeyEntity
                    ->save();
            }
        } catch (\Exception $exception) {
             echo $exception->getMessage();

             throw $exception;
        }
    }

    /**
     * @param DataSetInterface $dataSet
     * @return array
     */
    protected function map(DataSetInterface $dataSet): array
    {
        $dataSetArray = $dataSet
            ->getArrayCopy();

        $result = [];

        $isValidRow = $this
            ->isValidRow($dataSetArray);

        if ($isValidRow !== true) {
            return $result;
        }

        foreach ($dataSetArray as $key => $value) {
            if (isset(static::LICENSE_MAP[$key]['key']) === true) {
                $columnName = static::LICENSE_MAP[$key]['key'];
                $columnKey = array_search($columnName, $this->columns);
                $newKey = $this->keys[$columnKey];

                if (in_array(static::LICENSE_MAP[$key]['type'], ['date', 'date_or_null']) === true) {
                    $value = $this
                        ->formatDate($value);
                }

                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * @param array $dataArray
     * @return bool
     */
    protected function isValidRow(array $dataArray): bool
    {
        $isValid = true;

        foreach ($dataArray as $key => $value) {
            if (isset(static::LICENSE_MAP[$key]['key']) !== true) {
                continue;
            }

            if (static::LICENSE_MAP[$key]['type'] === 'date') {
                $validValue = $this
                    ->validateDate($value);
            } else {
                $validValue = isset($value);
            }

            $isValid = $isValid && $validValue;
        }

        return $isValid;
    }

    /**
     * @param string $date
     * @return bool
     */
    protected function validateDate(string $date): bool
    {
        if (empty($date)) {
            return false;
        }

        $dateToCheck = \DateTime::createFromFormat(SoftwarePackageConstants::IMPORT_LICENSE_KEY_DATE_FORMAT, $date);

        return (($dateToCheck instanceof \DateTime) && (checkdate($dateToCheck->format('m'), $dateToCheck->format('d'), $dateToCheck->format('Y')) === true));
    }

    /**
     * @param string $date
     * @return \DateTime|null
     */
    protected function formatDate(string $date): ?\DateTime
    {
        if ($this->validateDate($date) === true) {
            return \DateTime::createFromFormat(SoftwarePackageConstants::IMPORT_LICENSE_KEY_DATE_FORMAT, $date);
        }

        return null;
    }

    /**
     * @param string $idLicense
     * @param string $license
     * @return DstLicense
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function findEntity(string $idLicense, string $license): DstLicense
    {
        return DstLicenseQuery::create()
            ->filterByLicenseId($idLicense)
            ->filterByLicenseKey($license)
            ->findOneOrCreate();
    }
}