<?php
/**
 * Durst - project - MailBusinessFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 03.03.20
 * Time: 14:44
 */

namespace Pyz\Zed\Mail\Business;

use Pyz\Zed\Mail\Business\Model\Provider\SwiftMailer;
use Pyz\Zed\Mail\MailDependencyProvider;
use Spryker\Zed\Mail\Business\MailBusinessFactory as SprykerMailBusinessFactory;

class MailBusinessFactory extends SprykerMailBusinessFactory
{
    /**
     * {@inheritDoc}
     *
     * @return \Pyz\Zed\Mail\Business\Model\Provider\SwiftMailer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function createMailer(): SwiftMailer
    {
        return new SwiftMailer(
            $this->createRenderer(),
            $this->getMailer()
        );
    }

    /**
     * @return mixed|\Spryker\Zed\Mail\Dependency\Mailer\MailToMailerInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMailer()
    {
        return $this
            ->getProvidedDependency(MailDependencyProvider::MAILER);
    }
}
