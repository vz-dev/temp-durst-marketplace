<?php
/**
 * Durst - project - WebserviceNotFoundInConfigException.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-11-05
 * Time: 09:45
 */

namespace Pyz\Service\SoapRequest\Dependencies\External\Api\Exception;


use Exception;

class WebserviceNotFoundInConfigException extends Exception
{
    public const MESSAGE = '"%s" - webservice could not be found in the config';
}
