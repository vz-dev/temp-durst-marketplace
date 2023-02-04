<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class CurrentPassword extends Constraint
{

    /**
     * @var string
     */
    protected $message = 'Incorrect current password provided.';

    /**
     * @var \Pyz\Zed\Merchant\Business\MerchantFacade
     */
    protected $merchantFacade;

    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacade
     */
    public function getFacadeMerchant()
    {
        return $this->merchantFacade;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

}
