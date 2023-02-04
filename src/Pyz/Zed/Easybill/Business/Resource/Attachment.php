<?php
/**
 * Durst - project - Attachment.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 13.01.20
 * Time: 10:33
 */

namespace Pyz\Zed\Easybill\Business\Resource;

class Attachment extends AbstractResource implements AttachmentInterface
{
    protected const URL = '/attachment';

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return $this->config->getEasybillApiUrl(static::URL);
    }
}
