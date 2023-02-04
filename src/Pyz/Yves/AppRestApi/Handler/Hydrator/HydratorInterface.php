<?php
/**
 * Durst - project - HydratorInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 14:19
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator;

use stdClass;

interface HydratorInterface
{
    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @param string $version
     *
     * @return void
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1');
}
