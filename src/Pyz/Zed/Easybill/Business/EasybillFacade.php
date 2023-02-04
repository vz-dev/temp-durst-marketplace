<?php

namespace Pyz\Zed\Easybill\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Pyz\Zed\Easybill\Business\EasybillBusinessFactory getFactory()
 */
class EasybillFacade extends AbstractFacade implements EasybillFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function createInvoice(): bool
    {
        return $this
            ->getFactory()
            ->createResourceManager()
            ->createInvoice();
    }

    /**
     * {@inheritDoc}
     *
     * @param string $reference
     *
     * @return void
     */
    public function addReferenceToInvoiceQueue(string $reference): void
    {
        $this
            ->getFactory()
            ->createInvoiceQueueManager()
            ->addReferenceToInvoiceQueue($reference);
    }
}
