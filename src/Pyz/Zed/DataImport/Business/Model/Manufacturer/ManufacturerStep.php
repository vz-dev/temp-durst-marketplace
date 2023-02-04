<?php
/**
 * Durst - project - ManufacturerStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 11.06.18
 * Time: 15:17
 */

namespace Pyz\Zed\DataImport\Business\Model\Manufacturer;


use Orm\Zed\Product\Persistence\SpyManufacturerQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class ManufacturerStep implements DataImportStepInterface
{
    const KEY_CODE = 'code';
    const KEY_NAME = 'name';
    const KEY_STREET = 'street';
    const KEY_ZIP = 'zip';
    const KEY_CITY = 'city';
    const KEY_COUNTRY = 'country';
    const KEY_WEBSITE = 'website';
    const KEY_LOGO_URL = 'logo_url';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = SpyManufacturerQuery::create()
            ->filterByCode($dataSet[self::KEY_CODE])
            ->findOneOrCreate();

        $entity->setName($dataSet[self::KEY_NAME]);
        $entity->setAddress2($dataSet[self::KEY_STREET]);
        $entity->setAddress3(
            sprintf(
                '%s %s',
                $dataSet[self::KEY_ZIP],
                $dataSet[self::KEY_CITY]

            )
        );
        $entity->setCountry($dataSet[self::KEY_COUNTRY]);
        $entity->setHomepage($dataSet[self::KEY_WEBSITE]);
        $entity->setLogoUrl($dataSet[self::KEY_LOGO_URL]);

        if($entity->isModified() || $entity->isNew()){
            $entity->save();
        }
    }
}