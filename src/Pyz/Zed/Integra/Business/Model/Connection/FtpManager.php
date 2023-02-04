<?php
/**
 * Durst - project - FtpManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 18:43
 */

namespace Pyz\Zed\Integra\Business\Model\Connection;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;
use Pyz\Zed\Integra\Business\Exception\ConnectionException;

class FtpManager implements FtpManagerInterface
{
    protected const USE_PASV_ADDRESS = false;
    protected const FTP_PUT_MODE = FTP_BINARY;
    protected const TIMEOUT = 90;
    protected const PORT = 21;
    protected const REMOTE_FILENAME = 'orders.csv';

    public const TRANSFER_TYPE_CLOSED = 'close-orders';
    public const TRANSFER_TYPE_OPEN = 'open-orders';

    /**
     * @param IntegraCredentialsTransfer $credentials
     * @param string $filename
     * @param string|null $type
     */
    public function sendFile(IntegraCredentialsTransfer $credentials, string $filename, ?string $type=null): void
    {
        $this->checkRequirements($credentials);

        $stream = $this->connect($credentials);
        try {
            $this->login($credentials, $stream);
            $this->setAddressOption($stream);
            $this->setPassiveMode($stream);
            $this->setDirectory($stream, $credentials, $type);
            $this->putFile($stream, $credentials, $filename);
        } finally {
            ftp_close($stream);
        }
    }

    /**
     * @param $stream
     * @param IntegraCredentialsTransfer $credentials
     * @param string $localFilename
     *
     * @return void
     */
    protected function putFile($stream, IntegraCredentialsTransfer $credentials, string $localFilename): void
    {
        $success = ftp_put(
            $stream,
            $this->getRemoteFileNameFromLocal($localFilename),
            $localFilename,
            self::FTP_PUT_MODE,
            0
        );

        if ($success === false) {
            throw ConnectionException::put($credentials->getOpenOrderCsvPath(), $localFilename);
        }
    }

    /**
     * @param $stream
     * @param IntegraCredentialsTransfer $credentials
     * @param string|null $type
     *
     */
    protected function setDirectory($stream, IntegraCredentialsTransfer $credentials, ?string $type): void
    {
        switch ($type) {
            case static::TRANSFER_TYPE_CLOSED:
                $dirPath = $credentials->getClosedOrderCsvPath();
                break;
            default:
                $dirPath = $credentials->getOpenOrderCsvPath();
        }

        if ($dirPath === null) {
            return;
        }

        $success = ftp_chdir($stream, $dirPath);

        if ($success === false) {
            ConnectionException::cd($dirPath);
        }
    }

    /**
     * @param $stream
     *
     * @return void
     */
    protected function setPassiveMode($stream): void
    {
        $success = ftp_pasv($stream, true);

        if ($success === false) {
            throw ConnectionException::passive();
        }
    }

    /**
     * @param $stream
     *
     * @return void
     */
    protected function setAddressOption($stream): void
    {
        $success = ftp_set_option(
            $stream,
            FTP_USEPASVADDRESS,
            static::USE_PASV_ADDRESS
        );

        if ($success !== true) {
            throw ConnectionException::pasvAddress();
        }
    }

    /**
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return resource
     */
    protected function connect(IntegraCredentialsTransfer $credentials)
    {
        $stream = ftp_connect(
            $credentials->getFtpHost(),
            static::PORT,
            static::TIMEOUT
        );

        if ($stream === false) {
            throw ConnectionException::ftp($credentials->getFtpHost());
        }

        return $stream;
    }

    /**
     * @param IntegraCredentialsTransfer $credentials
     * @param $stream
     *
     * @return void
     */
    protected function login(IntegraCredentialsTransfer $credentials, $stream): void
    {
        if (ftp_login($stream, $credentials->getFtpUser(), $credentials->getFtpPassword()) !== true) {
            throw ConnectionException::login($credentials->getFtpUser());
        }
    }

    /**
     * @param IntegraCredentialsTransfer $credentials
     *
     * @return void
     */
    protected function checkRequirements(IntegraCredentialsTransfer $credentials): void
    {
        $missingCred = [];
        if ($credentials->getFtpHost() === null) {
            $missingCred[] = 'host';
        }
        if ($credentials->getFtpUser() === null) {
            $missingCred[] = 'user';
        }
        if ($credentials->getFtpPassword() === null) {
            $missingCred[] = 'password';
        }

        if (count($missingCred) > 0) {
            throw ConnectionException::missingCredentials($missingCred);
        }
    }

    /**
     * @param string $localName
     * @return string
     */
    protected function getRemoteFileNameFromLocal(string $localName) : string
    {
        if(pathinfo($localName, PATHINFO_EXTENSION) === 'csv'){
            return static::REMOTE_FILENAME;
        }

        return basename($localName);
    }
}
