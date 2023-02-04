<?php

namespace Pyz\Zed\MerchantManagement\Communication;

use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\DataProviderFactory;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\DeliveryAreaFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\DeliveryAreaUpdateFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\DepositFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DeliveryAreaForm;
use Pyz\Zed\MerchantManagement\Communication\Form\DeliveryAreaUpdateForm;
use Pyz\Zed\MerchantManagement\Communication\Form\FormFactory;
use Pyz\Zed\MerchantManagement\Communication\Table\TableFactory;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\MerchantFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\DataProvider\MerchantUpdateFormDataProvider;
use Pyz\Zed\MerchantManagement\Communication\Form\MerchantForm;
use Pyz\Zed\MerchantManagement\Communication\Form\MerchantUpdateForm;


/**
 * @method \Pyz\Zed\MerchantManagement\MerchantManagementConfig getConfig()
 */
class MerchantManagementCommunicationFactory extends AbstractMerchantManagementCommunicationFactory
{
    /**
     * @return FormFactory
     */
    public function createFormFactory()
    {
        return new FormFactory();
    }

    /**
     * @return TableFactory
     */
    public function createTableFactory()
    {
        return new TableFactory();
    }
}
