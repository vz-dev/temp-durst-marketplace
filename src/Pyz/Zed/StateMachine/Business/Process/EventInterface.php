<?php
/**
 * Durst - project - EventInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 15:45
 */

namespace Pyz\Zed\StateMachine\Business\Process;

use Spryker\Zed\StateMachine\Business\Process\EventInterface as SprykerEventInterface;

interface EventInterface extends SprykerEventInterface
{
    /**
     * @return string
     */
    public function getRelativeTimeout(): string;

    /**
     * @param string $relativeTimeout
     */
    public function setRelativeTimeout(string $relativeTimeout): void;

    /**
     * @return bool
     */
    public function hasRelativeTimeout(): bool;

    /**
     * @return string
     */
    public function getUtcDateTime(): string;

    /**
     * @param string $utcDateTime
     * @return void
     */
    public function setUtcDateTime(string $utcDateTime): void;

    /**
     * @return bool
     */
    public function hasUtcDateTime(): bool;
}
