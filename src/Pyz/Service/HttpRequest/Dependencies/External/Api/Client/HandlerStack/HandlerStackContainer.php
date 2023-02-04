<?php
/**
 * Durst - project - HandlerStackContainer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 10:36
 */

namespace Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HandlerStack;


use GuzzleHttp\HandlerStack;
use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\Middleware\MiddlewareInterface;

class HandlerStackContainer
{
    /**
     * @var \GuzzleHttp\HandlerStack
     */
    protected static $handlerStack;

    /**
     * @return \GuzzleHttp\HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        if (!static::$handlerStack) {
            static::$handlerStack = HandlerStack::create();
        }

        return static::$handlerStack;
    }

    /**
     * @param \Pyz\Service\HttpRequest\Dependencies\External\Api\Client\Middleware\MiddlewareInterface $middleware
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $handlerStack = $this
            ->getHandlerStack();
        $handlerStack
            ->push(
                $middleware->getCallable(),
                $middleware->getName()
            );
    }
}
