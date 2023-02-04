<?php
/**
 * Durst - project - InvalidHttpMethodException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.19
 * Time: 10:59
 */

namespace Pyz\Service\HttpRequest\Dependencies\External\Api\Exception\HttpException;


use Pyz\Service\HttpRequest\Dependencies\External\Api\Client\HttpClient;
use Pyz\Service\HttpRequest\Dependencies\External\Api\Exception\HttpException;

class InvalidHttpMethodException extends HttpException
{
    protected const MESSAGE = 'HTTP method "%s" not supported. Valid methods are "%s"';

    /**
     * InvalidHttpMethodException constructor.
     * @param string|null $invalidMethod
     */
    public function __construct(
        ?string $invalidMethod
    )
    {
        $message = sprintf(
            self::MESSAGE,
            $invalidMethod,
            implode(', ', HttpClient::SUPPORTED_METHODS)
        );

        parent::__construct($message);
    }
}
