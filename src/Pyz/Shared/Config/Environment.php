<?php

namespace Pyz\Shared\Config;

use Spryker\Shared\Config\Environment as SprykerEnvironment;

class Environment extends SprykerEnvironment
{
    const DOCKER_DEV = 'docker.dev';
    const DOCKER_DEVTEST = 'docker.devtest';
    const DOCKER_CI = 'docker.ci';

    /**
     * @return bool
     */
    public static function isDevelopment(): bool
    {
        return
            (self::$environment === self::DEVELOPMENT)
            || (self::$environment === self::DOCKER_DEV)
        ;
    }

    /**
     * @return bool
     */
    public static function isNotDevelopment(): bool
    {
        return
            (self::$environment !== self::DEVELOPMENT)
            && (self::$environment !== self::DOCKER_DEV)
        ;
    }

    /**
     * @return bool
     */
    public static function isDocker(): bool
    {
        return
            (self::$environment === self::DOCKER_DEV)
            || (self::$environment === self::DOCKER_DEVTEST)
            || (self::$environment === self::DOCKER_CI)
        ;
    }

    /**
     * @return bool
     */
    public static function isTesting()
    {
        return
            (self::$environment === self::TESTING)
            || (self::$environment === self::DOCKER_DEVTEST)
            || (self::$environment === self::DOCKER_CI)
        ;
    }

    /**
     * @return bool
     */
    public static function isNotTesting()
    {
        return
            (self::$environment !== self::TESTING)
            && (self::$environment !== self::DOCKER_DEVTEST)
            && (self::$environment !== self::DOCKER_CI)
        ;
    }
}
