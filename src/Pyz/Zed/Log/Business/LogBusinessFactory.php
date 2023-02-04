<?php
/**
 * Durst - project - LogBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 20.03.20
 * Time: 15:31
 */

namespace Pyz\Zed\Log\Business;

use Pyz\Zed\Log\Business\Log\MailLogger;
use Pyz\Zed\Log\Business\Log\MailLoggerInterface;
use Pyz\Zed\Log\Dependency\Facade\LogToMailBridge;
use Pyz\Zed\Log\LogConfig;
use Pyz\Zed\Log\LogDependencyProvider;
use Spryker\Zed\Log\Business\LogBusinessFactory as SprykerLogBusinessFactory;

/**
 * Class LogBusinessFactory
 * @package Pyz\Zed\Log\Business
 * @method LogConfig getConfig()
 */
class LogBusinessFactory extends SprykerLogBusinessFactory
{
    /**
     * @return \Pyz\Zed\Log\Business\Log\MailLoggerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMailLogger(): MailLoggerInterface
    {
        return new MailLogger(
            $this->getMailFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \Pyz\Zed\Log\Dependency\Facade\LogToMailBridge
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMailFacade(): LogToMailBridge
    {
        return $this
            ->getProvidedDependency(LogDependencyProvider::FACADE_MAIL);
    }
}
