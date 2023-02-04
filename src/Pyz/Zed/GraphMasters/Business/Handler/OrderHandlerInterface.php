<?php

namespace Pyz\Zed\GraphMasters\Business\Handler;

use Generated\Shared\Transfer\GraphMastersApiOrderUpdateTransfer;

interface OrderHandlerInterface
{
    /**
     * @param GraphMastersApiOrderUpdateTransfer $requestTransfer
     */
    public function importOrder(GraphMastersApiOrderUpdateTransfer $requestTransfer): void;
}
