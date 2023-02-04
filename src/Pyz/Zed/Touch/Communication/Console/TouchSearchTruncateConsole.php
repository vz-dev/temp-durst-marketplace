<?php
/**
 * Durst - project - TruncateTouchSearchConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:32
 */

namespace Pyz\Zed\Touch\Communication\Console;


use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TouchSearchTruncateConsole
 * @package Pyz\Zed\Touch\Communication\Console
 * @method \Pyz\Zed\Touch\Business\TouchFacadeInterface getFacade()
 */
class TouchSearchTruncateConsole extends Console
{
    public const COMMAND_NAME = 'touch:search:truncate';
    public const DESCRIPTION = 'This command will truncate the whole touch_search table';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['t:s:t']);

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
            ->removeAllTouchSearchEntries();
    }
}