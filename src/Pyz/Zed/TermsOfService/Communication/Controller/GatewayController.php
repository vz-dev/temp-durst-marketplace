<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 08.05.18
 * Time: 10:04
 */

namespace Pyz\Zed\TermsOfService\Communication\Controller;

use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\TermsOfService\Communication\Controller
 * @method TermsOfServiceFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @return TransferInterface
     */
    public function getCustomerTermsAction()
    {
        return $this
            ->getFacade()
            ->getCustomerTerms();
    }
}