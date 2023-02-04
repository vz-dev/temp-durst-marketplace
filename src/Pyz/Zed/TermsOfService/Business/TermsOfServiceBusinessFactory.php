<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 11.01.18
 * Time: 15:43
 */

namespace Pyz\Zed\TermsOfService\Business;


use Pyz\Zed\TermsOfService\Business\Model\TermsOfService;
use Pyz\Zed\TermsOfService\Persistence\TermsOfServiceQueryContainerInterface;
use Pyz\Zed\TermsOfService\TermsOfServiceConfig;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class TermsOfServiceBusinessFactory
 * @package Pyz\Zed\TermsOfService\Business
 * @method TermsOfServiceQueryContainerInterface getQueryContainer()
 * @method TermsOfServiceConfig getConfig()
 */
class TermsOfServiceBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return TermsOfService
     */
    public function createTermsOfServiceModel()
    {
        return new TermsOfService(
            $this->getQueryContainer(),
            $this->getConfig()
        );
    }
}