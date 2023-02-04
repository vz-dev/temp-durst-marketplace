<?php
/**
 * Durst - project - PasswordManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 10:56
 */

namespace Pyz\Zed\Integra\Business\Model\Encryption;

use Pyz\Zed\Integra\IntegraConfig;

class PasswordManager implements PasswordManagerInterface
{
    /**
     * @var IntegraConfig
     */
    protected $config;

    /**
     * PasswordManager constructor.
     *
     * @param IntegraConfig $config
     */
    public function __construct(IntegraConfig $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $password
     *
     * @return string
     */
    public function encryptPassword(string $password): string
    {
        $encryptedPassword = openssl_encrypt(
            $password,
            $this->config->getEncryptionCipherMethod(),
            $this->config->getEncryptionKey(),
            0,
            $this->config->getEncryptionIv()
        );

        return base64_encode($encryptedPassword);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $encryptedPassword
     *
     * @return string
     */
    public function decryptPassword(string $encryptedPassword): string
    {
        return openssl_decrypt(
            base64_decode($encryptedPassword),
            $this->config->getEncryptionCipherMethod(),
            $this->config->getEncryptionKey(),
            0,
            $this->config->getEncryptionIv()
        );
    }
}
