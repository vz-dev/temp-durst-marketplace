<?php
/**
 * Durst - project - Base64ToFileHelperInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-16
 * Time: 08:39
 */

namespace Pyz\Zed\Sales\Business\Helper;


interface Base64ToFileHelperInterface
{
    /**
     * Saves the passed base 64 string in a location defined by the config.
     * A unique filename will be generated randomly.
     * If the passed string is not validly base 64 coded an exception will be thrown
     * @see \Pyz\Zed\Sales\Business\Exception\InvalidBase64StringException
     *
     * @param string $base64String
     * @return string The complete
     */
    public function storeStringAsFile(string $base64String): string;
}