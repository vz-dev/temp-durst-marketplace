<?php
/**
 * Durst - project - DiscountToTaxBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 15.09.20
 * Time: 10:36
 */

namespace Pyz\Zed\Discount\Dependency\Facade;


use DateTime;
use Pyz\Zed\Tax\Business\TaxFacadeInterface;

class DiscountToTaxBridge implements DiscountToTaxBridgeInterface
{
    /**
     * @var \Pyz\Zed\Tax\Business\TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * DiscountToTaxBridge constructor.
     * @param \Pyz\Zed\Tax\Business\TaxFacadeInterface $taxFacade
     */
    public function __construct(
        TaxFacadeInterface $taxFacade
    )
    {
        $this->taxFacade = $taxFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \DateTime $date
     * @return float
     */
    public function getDefaultTaxRateForDate(DateTime $date): float
    {
        return $this
            ->taxFacade
            ->getDefaultTaxRateForDate(
                $date
            );
    }
}
