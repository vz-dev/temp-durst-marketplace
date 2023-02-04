<?php
/**
 * Durst - project - ConnectionException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 18:51
 */

namespace Pyz\Zed\Integra\Business\Exception;

use RuntimeException;

class ConnectionException extends RuntimeException
{
    protected const FTP = 'could not establish ftp connection to host %s';
    protected const MISSING_CREDENTIALS = 'missing credentials $s';
    protected const LOGIN = 'login rejected for user %s';
    protected const PASSIVE = 'could not activate passive mode';
    protected const CD = 'could not change directory to %s';
    protected const PUT = 'could not execute put local:"%s" remote:"%s"';
    protected const PASV_ADDRESS = 'could not set option pasv address';
    protected const RESPONSE_ERROR = 'response error: %s';
    protected const RESPONSE_ERROR_CODE = ' - code: %d';
    protected const UKNOWN_SERVICE = 'unknown service %s';

    /**
     * @param string $host
     *
     * @return static
     */
    public static function ftp(string $host): self
    {
        return new ConnectionException(
            sprintf(
                static::FTP,
                $host
            )
        );
    }

    /**
     * @param array $credentials
     *
     * @return static
     */
    public static function missingCredentials(array $credentials): self
    {
        return new ConnectionException(
            sprintf(
                static::MISSING_CREDENTIALS,
                implode($credentials, ', ')
            )
        );
    }

    /**
     * @param string $user
     *
     * @return static
     */
    public static function login(string $user): self
    {
        return new ConnectionException(
            sprintf(
                static::LOGIN,
                $user
            )
        );
    }

    /**
     * @return static
     */
    public static function passive(): self
    {
        return new ConnectionException(static::PASSIVE);
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public static function cd(string $path): self
    {
        return new ConnectionException(
            sprintf(
                static::CD,
                $path
            )
        );
    }

    /**
     * @param string $remotePath
     * @param string $localPath
     *
     * @return static
     */
    public static function put(string $remotePath, string $localPath):  self
    {
        return new ConnectionException(
            sprintf(
                static::PUT,
                $localPath,
                $remotePath
            )
        );
    }

    /**
     * @return static
     */
    public static function pasvAddress(): self
    {
        return new ConnectionException(static::PASV_ADDRESS);
    }

    /**
     * @param string $errorMessage
     * @param int|null $code
     *
     * @return static
     */
    public static function responseError(string $errorMessage, ?int $code = null): self
    {
        $message = sprintf(
            static::RESPONSE_ERROR,
            $errorMessage
        );

        if ($code === null) {
            $message .= sprintf(
                static::RESPONSE_ERROR_CODE,
                $code
            );
        }

        return new ConnectionException(
            $message,
            $code
        );
    }

    /**
     * @param string $service
     *
     * @return static
     */
    public static function unkownService(string $service): self
    {
        return new ConnectionException(
            sprintf(
                static::UKNOWN_SERVICE,
                $service
            )
        );
    }
}
