<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\AkeneoPimMiddlewareConnector\Business\Importer;

interface ImporterInterface
{
    /**
     * @param array $data
     *
     * @return void
     */
    public function import(array $data): void;
}
