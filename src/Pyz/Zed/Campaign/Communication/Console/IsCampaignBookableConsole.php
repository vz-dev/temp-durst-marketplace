<?php
/**
 * Durst - project - IsCampaignBookableConsole.php.
 *
 * Initial version by:
 * User: Zhaklina Basha, <zhaklina.basha@durst.shop>
 * Date: 16.08.21
 * Time: 12:47
 */

namespace Pyz\Zed\Campaign\Communication\Console;

use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IsCampaignBookableConsole
 * @package Pyz\Zed\Campaign\Communication\Console
 * @method \Pyz\Zed\Campaign\Business\CampaignFacadeInterface getFacade()
 * @method \Pyz\Zed\MerchantPrice\Business\MerchantPriceFacadeInterface getMerchantFacade()
 */
class IsCampaignBookableConsole extends Console
{
    /**
     * @var MerchantQueryContainerInterface
     */
    protected $merchantQuery;

    public const COMMAND_NAME = 'campaign:add:is-bookable';
    public const DESCRIPTION = 'This command will add the is_bookable in the entity of the campaing period';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['generate:is-bookable', 'da:c:cts']);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->getFacade()
            ->saveIsBookableForCampaign();
    }
}
