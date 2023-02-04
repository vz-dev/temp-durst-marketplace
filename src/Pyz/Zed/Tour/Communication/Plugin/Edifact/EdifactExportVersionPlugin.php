<?php

namespace Pyz\Zed\Tour\Communication\Plugin\Edifact;

use Generated\Shared\Transfer\ProcessResultTransfer;
use Propel\Runtime\Exception\PropelException;
use Pyz\Zed\Edifact\Business\EdifactFacade;
use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Communication\TourCommunicationFactory;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException;
use SprykerMiddleware\Zed\Process\Dependency\Plugin\Hook\PreProcessorHookPluginInterface;

/**
 * @method TourCommunicationFactory getFactory()
 */
class EdifactExportVersionPlugin extends AbstractPlugin implements PreProcessorHookPluginInterface
{

    protected const PLUGIN_NAME = 'EdifactExportVersionPlugin';

    /**
     * @var EdifactFacade
     */
    protected $edifactFacade;

    /**
     * @var bool
     */
    protected $isGraphmastersTour;

    public function __construct(
        EdifactFacadeInterface $edifactFacade,
        bool $isGraphmastersTour = false
    ) {
        $this->edifactFacade = $edifactFacade;
        $this->isGraphmastersTour = $isGraphmastersTour;
    }

    /**
     * @param ProcessResultTransfer|null $processResultTransfer
     *
     * @return void
     *
     * @throws ContainerKeyNotFoundException
     * @throws PropelException
     */
    public function process(?ProcessResultTransfer $processResultTransfer = null): void
    {
        $idTour = (int) $processResultTransfer
            ->getProcessConfiguration()
            ->getInputStreamPlugin()
            ->getPath();

        $this
            ->edifactFacade
            ->setExportVersionForTour($idTour, $this->isGraphmastersTour);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::PLUGIN_NAME;
    }
}
