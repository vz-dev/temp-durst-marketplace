<?php
/**
 * Durst - project - Event.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 15:45
 */

namespace Pyz\Zed\StateMachine\Business\Process;

use Spryker\Zed\StateMachine\Business\Process\Event as SprykerEvent;

class Event extends SprykerEvent implements EventInterface
{
    /**
     * @var string
     */
    protected $relativeTimeout;

    /**
     * @var string
     */
    protected $utcDateTime;

    /**
     * @return string
     */
    public function getRelativeTimeout(): string
    {
        return $this->relativeTimeout;
    }

    /**
     * @param string|null $relativeTimeout
     */
    public function setRelativeTimeout(?string $relativeTimeout): void
    {
        $this->relativeTimeout = $relativeTimeout;
    }

    /**
     * @return bool
     */
    public function hasRelativeTimeout(): bool
    {
        return ($this->relativeTimeout !== null && $this->relativeTimeout !== '');
    }

    /**
     * @return string
     */
    public function getUtcDateTime(): string
    {
        return $this->utcDateTime;
    }

    /**
     * @param string|null $utcDateTime
     */
    public function setUtcDateTime(?string $utcDateTime): void
    {
        $this->utcDateTime = $utcDateTime;
    }

    /**
     * @return bool
     */
    public function hasUtcDateTime(): bool
    {
        return ($this->utcDateTime !== null &&
            $this->utcDateTime !== '');
    }

    /**
     * @return string
     */
    public function getEventTypeLabel()
    {
        if ($this->isOnEnter()) {
            return ' (on enter)';
        }

        if ($this->isManual()) {
            return ' (manual)';
        }

        if ($this->hasTimeout()) {
            return ' (timeout)';
        }

        if ($this->hasRelativeTimeout()) {
            return ' (relative timeout)';
        }

        if ($this->hasUtcDateTime()) {
            return ' (UTC datetime)';
        }

        return '';
    }
}
