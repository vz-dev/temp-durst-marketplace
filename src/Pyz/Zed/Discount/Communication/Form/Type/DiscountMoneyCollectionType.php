<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2019-04-03
 * Time: 14:16
 */

namespace Pyz\Zed\Discount\Communication\Form\Type;


use Spryker\Zed\Money\Communication\Form\Type\MoneyCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DiscountMoneyCollectionType extends MoneyCollectionType
{
    /**
     * @param array $options
     * @return array
     */
    protected function overwriteCollectionDefaultEntryType(array $options): array
    {
        if ($options['entry_type'] === TextType::class) {
            $options['entry_type'] = DiscountMoneyType::class;
        }

        return $options;
    }
}