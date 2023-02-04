<?php
/**
 * Durst - project - AccountingToLogBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 24.03.20
 * Time: 17:16
 */

namespace Pyz\Zed\Accounting\Dependency\Facade;


use Pyz\Zed\Log\Business\LogFacadeInterface;

class AccountingToLogBridge implements AccountingToLogBridgeInterface
{
    /**
     * @var \Pyz\Zed\Log\Business\LogFacadeInterface
     */
    protected $logFacade;

    /**
     * AccountingToLogBridge constructor.
     * @param \Pyz\Zed\Log\Business\LogFacadeInterface $logFacade
     */
    public function __construct(
        LogFacadeInterface $logFacade
    )
    {
        $this->logFacade = $logFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $subject
     * @param string $errorMessage
     * @return void
     */
    public function sendErrorMail(string $subject, string $errorMessage): void
    {
        $this
            ->logFacade
            ->sendErrorMail(
                $subject,
                $errorMessage
            );
    }
}
