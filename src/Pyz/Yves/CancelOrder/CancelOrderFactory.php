<?php
/**
 * Durst - project - CancelOrderFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.09.21
 * Time: 15:29
 */

namespace Pyz\Yves\CancelOrder;

use Pyz\Client\Sales\SalesClientInterface;
use Pyz\Yves\CancelOrder\Form\FormFactory;
use Spryker\Yves\Kernel\AbstractFactory;

class CancelOrderFactory extends AbstractFactory
{
    /**
     * @return \Pyz\Yves\CancelOrder\Form\FormFactory
     */
    public function createCancelOrderFormFactory(): FormFactory
    {
        return new FormFactory();
    }

    /**
     * @return \Pyz\Client\Sales\SalesClientInterface
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function getSalesClient(): SalesClientInterface
    {
        return $this
            ->getProvidedDependency(
                CancelOrderDependencyProvider::CLIENT_SALES
            );
    }
}
