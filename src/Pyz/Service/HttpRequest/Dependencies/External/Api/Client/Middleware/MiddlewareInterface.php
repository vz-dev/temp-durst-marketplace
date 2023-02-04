<?php
/**
 * Durst - project - MiddlewareInterface.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 11:29
 */

namespace Pyz\Service\HttpRequest\Dependencies\External\Api\Client\Middleware;


interface MiddlewareInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return callable
     */
    public function getCallable(): callable;
}
