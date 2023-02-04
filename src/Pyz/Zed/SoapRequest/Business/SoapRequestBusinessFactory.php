<?php
/**
 * Durst - project - SoapRequestBusinessFactory.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-02
 * Time: 15:48
 */

namespace Pyz\Zed\SoapRequest\Business;


use Pyz\Zed\SoapRequest\Business\Model\SoapRequest;
use Pyz\Zed\SoapRequest\Business\Model\SoapRequestInterface;
use Pyz\Zed\SoapRequest\Persistence\SoapRequestQueryContainerInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * Class SoapRequestBusinessFactory
 * @package Pyz\Zed\SoapRequest\Business
 * @method SoapRequestQueryContainerInterface getQueryContainer()
 */
class SoapRequestBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return SoapRequestInterface
     */
    public function createSoapRequestModel(): SoapRequestInterface
    {
        return new SoapRequest(
            $this->getQueryContainer()
        );
    }
}
