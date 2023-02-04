<?php
/**
 * Durst - project - NotifyMerchantGoods.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 05.11.19
 * Time: 10:47
 */

namespace Pyz\Zed\Tour\Communication\Plugin\Command;


use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Pyz\Zed\Tour\Communication\Plugin\Mail\MerchantNotifyGoodsTypePlugin;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\StateMachine\Dependency\Plugin\CommandPluginInterface;

/**
 * Class NotifyMerchantGoods
 * @package Pyz\Zed\Tour\Communication\Plugin\Command
 * @method \Pyz\Zed\Tour\Communication\TourCommunicationFactory getFactory()
 * @method \Pyz\Zed\Tour\Business\TourFacadeInterface getFacade()
 */
class NotifyMerchantGoods extends AbstractPlugin implements CommandPluginInterface
{
    public const COMMAND_NAME = 'WholesaleTour/NotifyMerchantGoods';

    /**
     * This method is called when event have concrete command assigned.
     *
     * @param \Generated\Shared\Transfer\StateMachineItemTransfer $stateMachineItemTransfer
     *
     * @return void
     * @api
     *
     */
    public function run(StateMachineItemTransfer $stateMachineItemTransfer)
    {
        $concreteTourTransfer = $this
            ->getFacade()
            ->getConcreteTourById($stateMachineItemTransfer->getIdentifier());

        $this
            ->getFactory()
            ->getMailFacade()
            ->handleMail($this->getMailTransfer($concreteTourTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\ConcreteTourTransfer $concreteTourTransfer
     * @return \Generated\Shared\Transfer\MailTransfer
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    protected function getMailTransfer(ConcreteTourTransfer $concreteTourTransfer)
    {
        $branchTransfer = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getBranchById($concreteTourTransfer->getFkBranch());

        return (new MailTransfer())
            ->setType(MerchantNotifyGoodsTypePlugin::MAIL_TYPE)
            ->setBranch($branchTransfer)
            ->setTour($concreteTourTransfer);
    }
}
