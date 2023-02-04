<?php
/**
 * Durst - project - InvoiceToOmsBridge.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.03.20
 * Time: 09:32
 */

namespace Pyz\Zed\Invoice\Dependency\Facade;


use Generated\Shared\Transfer\DurstCompanyTransfer;
use Pyz\Zed\Oms\Business\OmsFacadeInterface;

class InvoiceToOmsBridge implements InvoiceToOmsBridgeInterface
{
    /**
     * @var \Pyz\Zed\Oms\Business\OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * InvoiceToOmsBridge constructor.
     * @param \Pyz\Zed\Oms\Business\OmsFacadeInterface $omsFacade
     */
    public function __construct(
        OmsFacadeInterface $omsFacade
    )
    {
        $this->omsFacade = $omsFacade;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Generated\Shared\Transfer\DurstCompanyTransfer
     */
    public function createDurstCompanyTransfer(): DurstCompanyTransfer
    {
        return $this
            ->omsFacade
            ->createDurstCompanyTransfer();
    }
}
