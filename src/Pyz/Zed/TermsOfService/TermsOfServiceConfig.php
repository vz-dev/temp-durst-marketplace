<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 15.01.18
 * Time: 16:56
 */

namespace Pyz\Zed\TermsOfService;


use Pyz\Shared\TermsOfService\TermsOfServiceConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class TermsOfServiceConfig extends AbstractBundleConfig
{
    const URL_TOS_FORM = '/terms-of-service/form';

    const ROUTE_WILD_CARD = '*';

    /**
     * @var array
     */
    protected $termsOfServiceIgnorable = [
        [
            'bundle' => 'merchant-auth',
            'controller' => 'login',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-auth',
            'controller' => 'logout',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-auth',
            'controller' => 'logout',
            'action' => 'success'
        ],
        [
            'bundle' => 'merchant-management',
            'controller' => '*',
            'action' => '*'
        ],
        [
            'bundle' => 'terms-of-service',
            'controller' => 'form',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'order',
            'action' => 'driver-detail'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'deposit-tool',
            'action' => 'index'
        ],
        [
            'bundle' => 'merchant-center',
            'controller' => 'order',
            'action' => 'driver-detail-frame'
        ],
    ];

    /**
     * @return array
     */
    public function getTermsOfServiceIgnorable()
    {
        return $this->termsOfServiceIgnorable;
    }

    /**
     * @return string
     */
    public function getTermsOfServiceFormUrl()
    {
        return static::URL_TOS_FORM;
    }

    /**
     * @return string
     */
    public function getCustomerTermsName() : string
    {
        return $this
            ->get(TermsOfServiceConstants::CUSTOMER_TERMS_NAME);
    }
}