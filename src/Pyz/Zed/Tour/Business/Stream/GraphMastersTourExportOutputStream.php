<?php

namespace Pyz\Zed\Tour\Business\Stream;

use Pyz\Zed\Edifact\Business\EdifactFacadeInterface;
use Pyz\Zed\Tour\Business\Parser\TourExportParser;
use Pyz\Zed\Tour\TourConfig;

class GraphMastersTourExportOutputStream extends TourExportOutputStream
{
    protected const REASON_PHRASE = 'Export Graphmasters tour';

    /**
     * @param string $path
     * @param TourExportParser $tourExportParser
     * @param EdifactFacadeInterface $edifactLogger
     * @param TourConfig $config
     */
    public function __construct(
        string $path,
        TourExportParser $tourExportParser,
        EdifactFacadeInterface $edifactLogger,
        TourConfig $config
    ) {
        parent::__construct($path, $tourExportParser, $edifactLogger, $config);

        $this->isGraphmastersTour = true;
    }
}
