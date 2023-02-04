<?php
/**
 * Durst - project - StateInterface.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-07-19
 * Time: 15:43
 */

namespace Pyz\Zed\StateMachine\Business\Process;

use Spryker\Zed\StateMachine\Business\Process\StateInterface as SprykerStateInterface;

interface StateInterface extends SprykerStateInterface
{
    /**
     * @return \Spryker\Zed\StateMachine\Business\Process\EventInterface[]
     */
    public function getRelativeTimeoutEvents();

    /**
     * @return bool
     */
    public function hasRelativeTimeoutEvent();

    /**
     * @return \Pyz\Zed\StateMachine\Business\Process\EventInterface[]
     */
    public function getUtcDateTimeEvents(): array;

    /**
     * @return bool
     */
    public function hasUtcDateTimeEvent(): bool;
}
