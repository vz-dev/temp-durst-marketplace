<?php
/**
 * Durst - project - NoCustomerTermsFound.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 10:24
 */

namespace Pyz\Zed\TermsOfService\Business\Exception;


class NoCustomerTermsFound extends \Exception
{
    const MESSAGE = 'terms of service with the name %s could not be found';
}