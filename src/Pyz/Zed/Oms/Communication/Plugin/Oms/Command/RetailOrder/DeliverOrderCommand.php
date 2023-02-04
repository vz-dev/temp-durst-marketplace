<?php
/**
 * Durst - merchant_center - DeliverOrderCommand.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 07.09.18
 * Time: 11:23
 */

namespace Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder;


use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Pyz\Zed\MerchantCenter\Communication\Plugin\Mail\MerchantOrderDeliverMailTypePlugin;
use Pyz\Zed\Oms\Communication\OmsCommunicationFactory;
use Pyz\Zed\Oms\OmsConfig;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\AbstractCommand;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * Class DeliverOrderCommand
 * @package Pyz\Zed\Oms\Communication\Plugin\Oms\Command\RetailOrder
 * @method OmsCommunicationFactory getFactory()
 * @method OmsConfig getConfig()
 */
class DeliverOrderCommand extends AbstractCommand implements CommandByOrderInterface
{
    const EVENT_ID = 'deliver';

    /**
     * @param array $orderItems
     * @param SpySalesOrder $order
     * @param ReadOnlyArrayObject $data
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $order, ReadOnlyArrayObject $data)
    {
        return [];
    }

}