<?php
/**
 * Durst - project - CampaignAdvertisingMaterialController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:12
 */

namespace Pyz\Zed\Campaign\Communication\Controller;

use Exception;
use Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Communication\CampaignCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CampaignAdvertisingMaterialController
 * @package Pyz\Zed\Campaign\Communication\Controller
 * @method CampaignCommunicationFactory getFactory()
 * @method CampaignFacadeInterface getFacade()
 */
class CampaignAdvertisingMaterialController extends AbstractController
{
    public const URL_LISTING = '/campaign/campaign-advertising-material/index';
    public const URL_CREATE = '/campaign/campaign-advertising-material/create';
    public const URL_EDIT = '/campaign/campaign-advertising-material/edit';
    public const URL_ACTIVATE = '/campaign/campaign-advertising-material/activate';
    public const URL_DEACTIVATE = '/campaign/campaign-advertising-material/deactivate';

    public const PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL = 'id-campaign-advertising-material';

    protected const CREATE_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS = 'Das Werbemittel "%s" wurde erfolgreich angelegt.';
    protected const EDIT_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS = 'Das Werbemittel "%s" wurde erfolgreich geändert.';
    protected const ACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS = 'Das Werbemittel mit der ID %d wurde erfolgreich aktiviert.';
    protected const DEACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS = 'Das Werbemittel mit der ID %d wurde erfolgreich deaktiviert.';

    protected const CREATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR = 'Leider konnte das Werbemittel "%s" nicht erstellt werden.';
    protected const EDIT_CAMPAIGN_ADVERTISING_MATERIAL_ERROR = 'Bei der Änderung des Werbemittels "%s" ist leider ein Fehler aufgetreten.';
    protected const ACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR = 'Leider konnte das Werbemittel mit der ID %d nicht aktiviert werden.';
    protected const DEACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR = 'Leider konnte das Werbemittel mit der ID %d nicht deaktiviert werden.';

    /**
     * @return array
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialTable();

        return $this
            ->viewResponse(
                [
                    'advertisingMaterials' => $table->render()
                ]
            );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(): JsonResponse
    {
        $table = $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialTable();

        return $this
            ->jsonResponse(
                $table
                    ->fetchData()
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $dataProvider = $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialDataProvider();

        $form = $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialForm(
                $dataProvider
                    ->getData(null),
                $dataProvider
                    ->getOptions()
            )
            ->handleRequest(
                $request
            );

        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $campaignAdvertisingMaterialTransfer = $this
                ->getFormData(
                    $form
                );

            try {
                $campaignAdvertisingMaterialTransfer = $this
                    ->getFacade()
                    ->addCampaignAdvertisingMaterial(
                        $campaignAdvertisingMaterialTransfer
                    );
            } catch (Exception $ex) {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::CREATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                            $campaignAdvertisingMaterialTransfer
                                ->getCampaignAdvertisingMaterialName()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            if ($campaignAdvertisingMaterialTransfer->getIdCampaignAdvertisingMaterial() !== null) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::CREATE_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS,
                            $campaignAdvertisingMaterialTransfer
                                ->getCampaignAdvertisingMaterialName()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            $this
                ->addErrorMessage(
                    sprintf(
                        static::CREATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                        $campaignAdvertisingMaterialTransfer
                            ->getCampaignAdvertisingMaterialName()
                    )
                );

            return $this
                ->redirectResponse(
                    static::URL_LISTING
                );
        }

        return $this
            ->viewResponse(
                [
                    'form' => $form->createView()
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request)
    {
        $idCampaignAdvertisingMaterial = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL)
            );

        $dataProvider = $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialDataProvider();

        $form = $this
            ->getFactory()
            ->createCampaignAdvertisingMaterialForm(
                $dataProvider
                    ->getData($idCampaignAdvertisingMaterial),
                $dataProvider
                    ->getOptions()
            )
            ->handleRequest(
                $request
            );

        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $campaignAdvertisingMaterialTransfer = $this
                ->getFormData(
                    $form
                );

            try {
                $campaignAdvertisingMaterialTransfer = $this
                    ->getFacade()
                    ->addCampaignAdvertisingMaterial(
                        $campaignAdvertisingMaterialTransfer
                    );
            } catch (Exception $exception) {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::EDIT_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                            $campaignAdvertisingMaterialTransfer
                                ->getCampaignAdvertisingMaterialName()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            if ($campaignAdvertisingMaterialTransfer->getIdCampaignAdvertisingMaterial() !== null) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::EDIT_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS,
                            $campaignAdvertisingMaterialTransfer
                                ->getCampaignAdvertisingMaterialName()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            $this
                ->addErrorMessage(
                    sprintf(
                        static::EDIT_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                        $campaignAdvertisingMaterialTransfer
                            ->getCampaignAdvertisingMaterialName()
                    )
                );

            return $this
                ->redirectResponse(
                    static::URL_LISTING
                );
        }

        return $this
            ->viewResponse(
                [
                    'form' => $form->createView()
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activateAction(Request $request): RedirectResponse
    {
        $idCampaignAdvertisingMaterial = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL)
            );

        try {
            $success = $this
                ->getFacade()
                ->activateCampaignAdvertisingMaterial(
                    $idCampaignAdvertisingMaterial
                );

            if ($success === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::ACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS,
                            $idCampaignAdvertisingMaterial
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

        } catch (Exception $ex) {
            $this
                ->addErrorMessage(
                    sprintf(
                        static::ACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                        $idCampaignAdvertisingMaterial
                    )
                );

            return $this
                ->redirectResponse(
                    static::URL_LISTING
                );
        }

        $this
            ->addErrorMessage(
                sprintf(
                    static::ACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                    $idCampaignAdvertisingMaterial
                )
            );

        return $this
            ->redirectResponse(
                static::URL_LISTING
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deactivateAction(Request $request): RedirectResponse
    {
        $idCampaignAdvertisingMaterial = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_ADVERTISING_MATERIAL)
            );

        try {
            $success = $this
                ->getFacade()
                ->deactivateCampaignAdvertisingMaterial(
                    $idCampaignAdvertisingMaterial
                );

            if ($success === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::DEACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_SUCCESS,
                            $idCampaignAdvertisingMaterial
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

        } catch (Exception $ex) {
            $this
                ->addErrorMessage(
                    sprintf(
                        static::DEACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                        $idCampaignAdvertisingMaterial
                    )
                );

            return $this
                ->redirectResponse(
                    static::URL_LISTING
                );
        }

        $this
            ->addErrorMessage(
                sprintf(
                    static::DEACTIVATE_CAMPAIGN_ADVERTISING_MATERIAL_ERROR,
                    $idCampaignAdvertisingMaterial
                )
            );

        return $this
            ->redirectResponse(
                static::URL_LISTING
            );
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return \Generated\Shared\Transfer\CampaignAdvertisingMaterialTransfer
     */
    protected function getFormData(FormInterface $form): CampaignAdvertisingMaterialTransfer
    {
        return $form
            ->getData();
    }
}
