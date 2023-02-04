<?php
/**
 * Durst - project - RequestHandlerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 10:17
 */

namespace Pyz\Yves\AppRestApi\Handler;


interface RequestHandlerInterface
{
    /**
     * @param string $json
     * @param string $version
     * @return \stdClass
     */
    public function handleJson(string $json, string $version = 'v1') : \stdClass;
}
