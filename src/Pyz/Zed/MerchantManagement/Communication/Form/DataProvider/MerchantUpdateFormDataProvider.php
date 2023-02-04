<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form\DataProvider;

use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Pyz\Zed\MerchantManagement\Communication\Form\MerchantUpdateForm;

class MerchantUpdateFormDataProvider extends MerchantFormDataProvider
{

    /**
     * @return array
     */
    public function getOptions() : array
    {
        $options = parent::getOptions();

        $options[MerchantUpdateForm::OPTION_STATUS_CHOICES] = $this->getStatusSelectChoices();

        return $options;
    }

    /**
     * @return array
     */
    protected function getStatusSelectChoices() : array
    {
        return array_combine(
            SpyMerchantTableMap::getValueSet(SpyMerchantTableMap::COL_STATUS),
            SpyMerchantTableMap::getValueSet(SpyMerchantTableMap::COL_STATUS)
        );
    }

}
