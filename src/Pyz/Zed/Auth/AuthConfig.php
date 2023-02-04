<?php

/**
 * This file is part of the Spryker Demoshop.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\Auth;

use Generated\Shared\Transfer\SequenceNumberSettingsTransfer;
use Pyz\Shared\Auth\AuthConstants;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Zed\Auth\AuthConfig as SprykerAuthConfig;

class AuthConfig extends SprykerAuthConfig
{
    public const DEFAULT_JWT_ISSUER = 'http://www.durst.de';
    public const DEFAULT_JWT_AUDIENCE = 'http://www.durst.de';

    public const DEFAULT_PROJECT_TIME_ZONE = 'Europe/Berlin';

    public const JWT_DRIVER_TOKEN_NAME_REFERENCE = 'Jwt-Driver';

    /**
     * @return array
     */
    public function getIgnorable()
    {
        $this->addIgnorable('heartbeat', 'index', 'index');
        $this->addIgnorable('_profiler', 'wdt', '*');

        return parent::getIgnorable();
    }

    /**
     * @return string
     */
    public function getProjectTimeZone(): string
    {
        return $this
            ->get(ApplicationConstants::PROJECT_TIMEZONE, self::DEFAULT_PROJECT_TIME_ZONE);
    }

    /**
     * @return string
     */
    public function getJwtIssuer(): string
    {
        return $this
            ->get(AuthConstants::JWT_ISSUER, self::DEFAULT_JWT_ISSUER);
    }

    /**
     * @return string
     */
    public function getJwtAudience(): string
    {
        return $this
            ->get(AuthConstants::JWT_AUDIENCE, self::DEFAULT_JWT_AUDIENCE);
    }

    /**
     * @return \Generated\Shared\Transfer\SequenceNumberSettingsTransfer
     */
    public function getJwtDriverTokenReferenceDefaults(): SequenceNumberSettingsTransfer
    {
        $sequenceNumberSettingsTransfer = new SequenceNumberSettingsTransfer();

        $name = self::JWT_DRIVER_TOKEN_NAME_REFERENCE . '-' . '%d';

        $sequenceNumberSettingsTransfer
            ->setName($name)
            ->setPrefix('');

        return $sequenceNumberSettingsTransfer;
    }
}
