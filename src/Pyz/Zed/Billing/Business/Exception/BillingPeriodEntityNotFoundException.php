<?php
/**
 * Durst - project - BillingPeriodEntityNotFoundException.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.03.20
 * Time: 08:52
 */

namespace Pyz\Zed\Billing\Business\Exception;

use Exception;

class BillingPeriodEntityNotFoundException extends Exception
{
    protected const MESSAGE = 'The billing period with the id %d could not be found';
    protected const FK_BRANCH = 'The billing period with the id %d for the branch with id %d could not be found';
    protected const INVOICE_INFORMATION = 'Not all information needed for the csv file could be found for billing period with id %d';
    protected const PERIOD = 'The given time %s does not belong in any billing period for branch with id %d';
    protected const BRANCH = 'no billing periods found for branch with id %d';

    /**
     * @param string $time
     * @param int $fkBranch
     *
     * @return static
     */
    public static function period(string $time, int $fkBranch): self
    {
        return new BillingPeriodEntityNotFoundException(
            sprintf(
                static::PERIOD,
                $time,
                $fkBranch
            )
        );
    }

    /**
     * @param int $idBranch
     *
     * @return static
     */
    public static function branch(int $idBranch): self
    {
        return new BillingPeriodEntityNotFoundException(
            sprintf(
                static::BRANCH,
                $idBranch
            )
        );
    }

    /**
     * @param int $idBillingPeriod
     *
     * @return static
     */
    public static function create(int $idBillingPeriod): self
    {
        return new BillingPeriodEntityNotFoundException(
            sprintf(
                self::MESSAGE,
                $idBillingPeriod
            )
        );
    }

    /**
     * @param int $idBillingPeriod
     * @param int $fkBranch
     *
     * @return static
     */
    public static function createFkBranch(int $idBillingPeriod, int $fkBranch): self
    {
        return new BillingPeriodEntityNotFoundException(
            sprintf(
                self::FK_BRANCH,
                $idBillingPeriod,
                $fkBranch
            )
        );
    }

    /**
     * @param int $idBillingPeriod
     *
     * @return static
     */
    public static function createInvoiceInformation(int $idBillingPeriod): self
    {
        return new BillingPeriodEntityNotFoundException(
            sprintf(
                self::INVOICE_INFORMATION,
                $idBillingPeriod
            )
        );
    }
}
