<?php
/**
 * Durst - project - SoapRequestServiceFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 28.10.20
 * Time: 14:22
 */

namespace Pyz\Service\SoapRequest;


use Pyz\Service\SoapRequest\Dependencies\External\Api\Client\SoapClient;
use Pyz\Service\SoapRequest\Dependencies\External\Api\Client\SoapClientInterface;
use Spryker\Service\Kernel\AbstractServiceFactory;

/**
 * Class SoapRequestServiceFactory
 * @package Pyz\Service\SoapRequest
 * @method SoapRequestConfig getConfig()
 */
class SoapRequestServiceFactory extends AbstractServiceFactory
{
    /**
     * @return SoapClientInterface
     */
    public function createSoapClient(): SoapClientInterface
    {
        return new SoapClient(
            $this->getConfig()
        );
    }
}
