<?php
/**
 * Durst - project - CategoryStyleStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.05.18
 * Time: 10:18
 */

namespace Pyz\Zed\DataImport\Business\Model\CategoryStyle;


use Orm\Zed\Category\Persistence\SpyCategoryAttributeQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class CategoryStyleStep implements DataImportStepInterface
{
    const KEY_CATEGORY_KEY = 'category_key';
    const KEY_IMAGE_URL = 'image_url';
    const KEY_COLOR_CODE = 'color_code';
    const KEY_LOCALE = 'locale';
    const KEY_PRIORITY = 'priority';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = SpyCategoryAttributeQuery::create()
            ->useCategoryQuery()
                ->filterByCategoryKey($dataSet[self::KEY_CATEGORY_KEY])
            ->endUse()
            ->useLocaleQuery()
                ->filterByLocaleName($dataSet[self::KEY_LOCALE])
            ->endUse()
            ->findOne();

        if($entity === null){
            return;
        }

        $entity->setColorCode($dataSet[self::KEY_COLOR_CODE]);
        $entity->setPriority($dataSet[self::KEY_PRIORITY]);
        $entity->setImageUrl($dataSet[self::KEY_IMAGE_URL]);

        $entity->save();
    }
}