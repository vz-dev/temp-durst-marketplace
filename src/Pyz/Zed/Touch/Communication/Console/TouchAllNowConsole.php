<?php
/**
 * Durst - project - TouchAllNowConsole.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 07.12.18
 * Time: 13:21
 */

namespace Pyz\Zed\Touch\Communication\Console;


use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TouchAllNowConsole
 * @package Pyz\Zed\Touch\Communication\Console
 * @method \Pyz\Zed\Touch\Business\TouchFacadeInterface getFacade()
 */
class TouchAllNowConsole extends Console
{
    public const COMMAND_NAME = 'touch:all:now';
    public const DESCRIPTION = 'This command will update the time stamps of all touch entities to "now"';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);
        $this->setAliases(['touch:all', 't:a:n']);

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
            ->touchAllNow();
    }
}