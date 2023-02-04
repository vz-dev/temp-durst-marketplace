<?php
/**
 * Durst - project - TermsOfServiceStep.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 29.05.18
 * Time: 10:34
 */

namespace Pyz\Zed\DataImport\Business\Model\TermsOfService;


use Orm\Zed\TermsOfService\Persistence\SpyTermsOfServiceQuery;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class TermsOfServiceStep implements DataImportStepInterface
{
    const KEY_NAME = 'name';
    const KEY_HINT_TEXT = 'hint_text';
    const KEY_BUTTON_TEXT = 'button_text';
    const KEY_TEXT = 'text';

    const KEY_CUSTOMER_TERMS = 'customer_terms';

    /**
     * @param DataSetInterface $dataSet
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function execute(DataSetInterface $dataSet)
    {
        $entity = SpyTermsOfServiceQuery::create()
            ->filterByName($dataSet[self::KEY_NAME])
            ->findOneOrCreate();

        $entity->setButtonText($dataSet[self::KEY_BUTTON_TEXT]);
        $entity->setHintText($dataSet[self::KEY_HINT_TEXT]);
        $entity->setText($dataSet[self::KEY_TEXT]);

        if($dataSet[self::KEY_NAME] === self::KEY_CUSTOMER_TERMS)
        {
            $entity->setActiveUntil(date("Y-m-d H:i:s", strtotime("+1 year")));
        }

        $entity->save();
    }
}
