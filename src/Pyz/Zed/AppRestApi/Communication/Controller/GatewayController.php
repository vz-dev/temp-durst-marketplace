<?php
/**
 * Durst - project - GatewayController.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 24.04.18
 * Time: 14:03
 */

namespace Pyz\Zed\AppRestApi\Communication\Controller;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\AppApiResponseTransfer;
use Pyz\Zed\AppRestApi\Communication\AppRestApiCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * Class GatewayController
 * @package Pyz\Zed\AppRestApi\Communication\Controller
 * @method AppRestApiCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
}