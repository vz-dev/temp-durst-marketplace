<?php
/**
 * Durst - project - CampaignCommunicationFactory.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:20
 */

namespace Pyz\Zed\Campaign\Communication;

use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Communication\Form\CampaignAdvertisingMaterialType;
use Pyz\Zed\Campaign\Communication\Form\CampaignPeriodForm;
use Pyz\Zed\Campaign\Communication\Form\DataProvider\CampaignAdvertisingMaterialDataProvider;
use Pyz\Zed\Campaign\Communication\Form\DataProvider\CampaignPeriodDataProvider;
use Pyz\Zed\Campaign\Communication\Table\CampaignAdvertisingMaterialTable;
use Pyz\Zed\Campaign\Communication\Table\CampaignPeriodBranchOrderTable;
use Pyz\Zed\Campaign\Communication\Table\CampaignPeriodTable;
use Pyz\Zed\Campaign\Persistence\CampaignQueryContainerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Symfony\Component\Form\FormInterface;

/**
 * Class CampaignCommunicationFactory
 * @package Pyz\Zed\Campaign\Communication
 * @method CampaignQueryContainerInterface getQueryContainer()
 * @method CampaignFacadeInterface getFacade()
 */
class CampaignCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Pyz\Zed\Campaign\Communication\Table\CampaignPeriodTable
     */
    public function createCampaignPeriodTable(): CampaignPeriodTable
    {
        return new CampaignPeriodTable(
            $this
                ->getQueryContainer(),
            $this
                ->getFacade()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Communication\Table\CampaignAdvertisingMaterialTable
     */
    public function createCampaignAdvertisingMaterialTable(): CampaignAdvertisingMaterialTable
    {
        return new CampaignAdvertisingMaterialTable(
            $this
                ->getQueryContainer()
        );
    }

    /**
     * @return \Pyz\Zed\Campaign\Communication\Table\CampaignPeriodBranchOrderTable
     */
    public function createCampaignPeriodBranchOrderTable(): CampaignPeriodBranchOrderTable
    {
        return new CampaignPeriodBranchOrderTable(
            $this
                ->getQueryContainer(),
            $this
                ->getFacade()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCampaignPeriodForm(
        CampaignPeriodTransfer $campaignPeriodTransfer,
        array $options
    ): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CampaignPeriodForm::class,
                $campaignPeriodTransfer,
                $options
            );
    }

    /**
     * @return \Pyz\Zed\Campaign\Communication\Form\DataProvider\CampaignPeriodDataProvider
     */
    public function createCampaignPeriodDataProvider(): CampaignPeriodDataProvider
    {
        return new CampaignPeriodDataProvider(
            $this
                ->getFacade()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCampaignAdvertisingMaterialForm(
        CampaignAdvertisingMaterialTransfer $advertisingMaterialTransfer,
        array $options
    ): FormInterface
    {
        return $this
            ->getFormFactory()
            ->create(
                CampaignAdvertisingMaterialType::class,
                $advertisingMaterialTransfer,
                $options
            );
    }

    /**
     * @return \Pyz\Zed\Campaign\Communication\Form\DataProvider\CampaignAdvertisingMaterialDataProvider
     */
    public function createCampaignAdvertisingMaterialDataProvider(): CampaignAdvertisingMaterialDataProvider
    {
        return new CampaignAdvertisingMaterialDataProvider(
            $this
                ->getFacade()
        );
    }
}
