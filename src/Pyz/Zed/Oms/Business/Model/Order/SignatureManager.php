<?php
/**
 * Durst - project - SignatureManager.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-05-16
 * Time: 10:16
 */

namespace Pyz\Zed\Oms\Business\Model\Order;

use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;

class SignatureManager implements SignatureManagerInterface
{
    /**
     * @var \Pyz\Zed\Sales\Business\SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * SignatureManager constructor.
     *
     * @param \Pyz\Zed\Sales\Business\SalesFacadeInterface $salesFacade
     */
    public function __construct(SalesFacadeInterface $salesFacade)
    {
        $this->salesFacade = $salesFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $signature
     */
    public function addSignatureToOrder(
        OrderTransfer $orderTransfer,
        string $signature
    ): void {
        $orderTransfer
            ->setSignatureFilePath(
                $this
                    ->salesFacade
                    ->storeBase64StringAsFile($signature)
            );

        $this
            ->salesFacade
            ->updateOrder($orderTransfer, $orderTransfer->getIdSalesOrder());
    }
}
