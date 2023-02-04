<?php

namespace Pyz\Zed\Edifact\Business\Config;

use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\Edifact\EdifactConstants;
use Pyz\Zed\GraphMasters\Business\GraphMastersFacadeInterface;
use Pyz\Zed\Merchant\Business\MerchantFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;

final class EdifactExportVersionConfig
{
    /**
     * @var EdifactExportVersionConfig|null
     */
    private static $instance = null;

    /**
     * @var TourFacadeInterface
     */
    private $tourFacade;

    /**
     * @var MerchantFacadeInterface
     */
    private $merchantFacade;

    /**
     * @var GraphMastersFacadeInterface
     */
    private $graphMastersFacade;

    /**
     * @var string|null
     */
    private $exportVersion = null;

    /**
     * @param TourFacadeInterface $tourFacade
     * @param MerchantFacadeInterface $merchantFacade
     * @param GraphMastersFacadeInterface $graphMastersFacade
     * @return self
     */
    public static function getInstance(
        TourFacadeInterface $tourFacade,
        MerchantFacadeInterface $merchantFacade,
        GraphMastersFacadeInterface $graphMastersFacade
    ): self {
        if (self::$instance === null) {
            self::$instance = new self($tourFacade, $merchantFacade, $graphMastersFacade);
        }

        return self::$instance;
    }

    /**
     * @param int $idTour
     * @param bool $isGraphmastersTour
     * @throws PropelException
     */
    public function setExportVersionForTour(int $idTour, bool $isGraphmastersTour = false): void
    {
        if ($isGraphmastersTour === true) {
            $graphmastersTourTransfer = $this
                ->graphMastersFacade
                ->getTourById($idTour);

            $fkBranch = $graphmastersTourTransfer->getFkBranch();
        } else {
            $concreteTourTransfer = $this
                ->tourFacade
                ->getConcreteTourById($idTour);

            $fkBranch = $concreteTourTransfer->getFkBranch();
        }

        $branchTransfer = $this
            ->merchantFacade
            ->getBranchById($fkBranch);

        $this->exportVersion = $branchTransfer->getEdiExportVersion();
    }

    /**
     * @return string|null
     */
    public function getExportVersion(): ?string
    {
        return (
            $this->exportVersion !== null &&
            in_array($this->exportVersion, EdifactConstants::VALID_EDIFACT_EXPORT_VERSIONS)
        )
            ? $this->exportVersion
            : EdifactConstants::EDIFACT_EXPORT_VERSION_DEFAULT;
    }

    /**
     * @param TourFacadeInterface $tourFacade
     * @param MerchantFacadeInterface $merchantFacade
     * @param GraphMastersFacadeInterface $graphMastersFacade
     */
    private function __construct(
        TourFacadeInterface $tourFacade,
        MerchantFacadeInterface $merchantFacade,
        GraphMastersFacadeInterface $graphMastersFacade
    ) {
        $this->tourFacade = $tourFacade;
        $this->merchantFacade = $merchantFacade;
        $this->graphMastersFacade = $graphMastersFacade;
    }
}
