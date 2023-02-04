<?php
/**
 * Durst - project - PasswordManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 10:56
 */

namespace Pyz\Zed\Integra\Business\Model\Encryption;

interface PasswordManagerInterface
{
    /**
     * @param string $password
     *
     * @return string
     */
    public function encryptPassword(string $password): string;

    /**
     * @param string $encryptedPassword
     *
     * @return string
     */
    public function decryptPassword(string $encryptedPassword): string;
}
