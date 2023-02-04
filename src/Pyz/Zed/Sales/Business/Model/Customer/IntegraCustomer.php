<?php
/**
 * Durst - project - IntegraCustomer.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 19.11.20
 * Time: 14:04
 */

namespace Pyz\Zed\Sales\Business\Model\Customer;


use Generated\Shared\Transfer\SaveOrderTransfer;
use Pyz\Zed\Sales\Dependency\Facade\SalesToCustomerInterface;

class IntegraCustomer implements IntegraCustomerInterface
{
    /**
     * @var SalesToCustomerInterface
     */
    protected $customerFacade;

    /**
     * IntegraCustomer constructor.
     * @param SalesToCustomerInterface $customerFacade
     */
    public function __construct(
        SalesToCustomerInterface $customerFacade
    )
    {
        $this->customerFacade = $customerFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param SaveOrderTransfer $orderTransfer
     * @param string|null $customerId
     * @return void
     */
    public function saveIntegraCustomerId(
        SaveOrderTransfer $orderTransfer,
        ?string $customerId
    ): void
    {
        $this
            ->customerFacade
            ->saveIntegraCustomerId(
                $orderTransfer,
                $customerId
            );
    }
}
