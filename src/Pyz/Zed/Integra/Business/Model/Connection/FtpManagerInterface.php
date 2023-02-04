<?php
/**
 * Durst - project - FtpManagerInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.11.20
 * Time: 18:43
 */

namespace Pyz\Zed\Integra\Business\Model\Connection;

use Generated\Shared\Transfer\IntegraCredentialsTransfer;

interface FtpManagerInterface
{
    /**
     * @param IntegraCredentialsTransfer $credentials
     * @param string $filename
     * @param string|null $type
     */
    public function sendFile(IntegraCredentialsTransfer $credentials, string $filename, ?string $type=null): void;
}
