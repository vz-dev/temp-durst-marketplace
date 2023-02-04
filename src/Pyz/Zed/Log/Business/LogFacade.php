<?php
/**
 * Durst - project - LogFacade.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 16:35
 */

namespace Pyz\Zed\Log\Business;

use Spryker\Zed\Log\Business\LogFacade as SprykerLogFacade;

/**
 * Class LogFacade
 * @package Pyz\Zed\Log\Business
 * @method LogBusinessFactory getFactory()
 */
class LogFacade extends SprykerLogFacade implements LogFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @param string $subject
     * @param string $errorMessage
     * @return void
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function sendErrorMail(
        string $subject,
        string $errorMessage
    ): void
    {
        $this
            ->getFactory()
            ->createMailLogger()
            ->mailError(
                $subject,
                $errorMessage
            );
    }
}
