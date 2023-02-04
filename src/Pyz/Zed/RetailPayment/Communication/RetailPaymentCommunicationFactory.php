<?php
/**
 * Copyright (c) 2018. Durststrecke GmbH. All rights reserved.
 */

/**
 * Durst - Marketplace-Platform - RetailPaymentCommunicationFactory.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 02.03.18
 * Time: 11:41
 */

namespace Pyz\Zed\RetailPayment\Communication;

use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\RetailPayment\RetailPaymentDependencyProvider;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

class RetailPaymentCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\Merchant\Business\MerchantFacadeInterface
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getMerchantFacade() : MerchantFacadeInterface
    {
        return $this
            ->getProvidedDependency(RetailPaymentDependencyProvider::FACADE_MERCHANT);
    }
}
