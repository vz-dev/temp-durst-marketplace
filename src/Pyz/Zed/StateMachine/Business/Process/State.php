<?php
/**
 * Durst - project - State.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 15:42
 */

namespace Pyz\Zed\StateMachine\Business\Process;

use Spryker\Zed\StateMachine\Business\Process\State as SprykerState;

class State extends SprykerState implements StateInterface
{
    /**
     * @return bool
     */
    public function hasRelativeTimeoutEvent(): bool
    {
        $transitions = $this->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
                $event = $transition->getEvent();
                if ($event->hasRelativeTimeout() === true) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return \Pyz\Zed\StateMachine\Business\Process\EventInterface[]
     */
    public function getRelativeTimeoutEvents(): array
    {
        $events = [];

        $transitions = $this->getOutgoingTransitions();
        foreach ($transitions as $transition) {
            if ($transition->hasEvent()) {
                /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
                $event = $transition->getEvent();
                if ($event->hasRelativeTimeout() === true) {
                    $events[] = $transition->getEvent();
                }
            }
        }

        return $events;
    }

    /**
     * @return \Pyz\Zed\StateMachine\Business\Process\EventInterface[]
     */
    public function getUtcDateTimeEvents(): array
    {
        $events = [];
        $transitions = $this
            ->getOutgoingTransitions();

        foreach ($transitions as $transition) {
            if ($transition->hasEvent() === true) {
                /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
                $event = $transition
                    ->getEvent();

                if ($event->hasUtcDateTime() === true) {
                    $events[] = $transition
                        ->getEvent();
                }
            }
        }

        return $events;
    }

    /**
     * @return bool
     */
    public function hasUtcDateTimeEvent(): bool
    {
        $transitions = $this
            ->getOutgoingTransitions();

        foreach ($transitions as $transition) {
            if ($transition->hasEvent() === true) {
                /** @var \Pyz\Zed\StateMachine\Business\Process\EventInterface $event */
                $event = $transition
                    ->getEvent();

                if ($event->hasUtcDateTime() === true) {
                    return true;
                }
            }
        }

        return false;
    }
}
