<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-11-27
 * Time: 16:28
 */

namespace Pyz\Zed\Tour\Communication\Console;

use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TourEdiConsole
 * @package Pyz\Zed\Tour\Communication\Console
 * @method TourFacadeInterface getFacade()
 */
class TourEdiConsole extends Console
{
    public const COMMAND_NAME = 'tour:edi:export';
    protected const COMMAND_DESCRIPTION = 'Creates an EDI tour export and uploads it to path / URL.';

    public const OPTION_TOUR_NAME = 'tourid';
    protected const OPTION_TOUR_DESCRIPTION = 'Id of tour to export';

    public const OPTION_UPLOAD_URL_NAME = 'uploadurl';
    protected const OPTION_UPLOAD_URL_DESCRIPTION = 'Url where the export should be uploaded to.';

    public const OPTION_IS_GRAPHMASTERS_TOUR_NAME = 'graphmasters';
    public const OPTION_IS_GRAPHMASTERS_TOUR_SHORTCUT = 'g';
    protected const OPTION_IS_GRAPHMASTERS_TOUR_DESCRIPTION = 'Treat the specified tour ID as a Graphmasters tour ID';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::COMMAND_DESCRIPTION);
        $this->addArgument(self::OPTION_TOUR_NAME, InputArgument::REQUIRED, self::OPTION_TOUR_DESCRIPTION);
        $this->addArgument(self::OPTION_UPLOAD_URL_NAME, InputArgument::REQUIRED, self::OPTION_UPLOAD_URL_DESCRIPTION);
        $this->addOption(self::OPTION_IS_GRAPHMASTERS_TOUR_NAME, self::OPTION_IS_GRAPHMASTERS_TOUR_SHORTCUT, InputOption::VALUE_NONE, self::OPTION_IS_GRAPHMASTERS_TOUR_DESCRIPTION);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info(self::COMMAND_DESCRIPTION);

        return $this
            ->getFacade()
            ->ediExportTourById(
                $input->getArgument(self::OPTION_TOUR_NAME),
                $input->getArgument(self::OPTION_UPLOAD_URL_NAME),
                6000,
                $input->hasOption(self::OPTION_IS_GRAPHMASTERS_TOUR_NAME)
            );
    }
}
