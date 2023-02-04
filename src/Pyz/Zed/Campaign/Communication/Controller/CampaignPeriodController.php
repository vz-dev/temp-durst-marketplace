<?php
/**
 * Durst - project - CampaignPeriodController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 08.06.21
 * Time: 12:11
 */

namespace Pyz\Zed\Campaign\Communication\Controller;

use Exception;
use Generated\Shared\Transfer\CampaignPeriodTransfer;
use Nette\Utils\DateTime;
use Pyz\Zed\Campaign\Business\CampaignFacadeInterface;
use Pyz\Zed\Campaign\Business\Exception\CampaignAdvertisingMaterialException;
use Pyz\Zed\Campaign\Business\Exception\CampaignPeriodException;
use Pyz\Zed\Campaign\Communication\CampaignCommunicationFactory;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CampaignPeriodController
 * @package Pyz\Zed\Campaign\Communication\Controller
 * @method CampaignCommunicationFactory getFactory()
 * @method CampaignFacadeInterface getFacade()
 */
class CampaignPeriodController extends AbstractController
{
    public const URL_LISTING = '/campaign/campaign-period/index';
    public const URL_CREATE = '/campaign/campaign-period/create';
    public const URL_EDIT = '/campaign/campaign-period/edit';
    public const URL_VIEW = '/campaign/campaign-period/view';
    public const URL_ACTIVATE = '/campaign/campaign-period/activate';
    public const URL_DEACTIVATE = '/campaign/campaign-period/deactivate';

    public const PARAM_ID_CAMPAIGN_PERIOD = 'id-campaign-period';

    protected const CREATE_CAMPAIGN_PERIOD_SUCCESS = 'Der Zeitraum "%s" wurde erfolgreich angelegt.';
    protected const EDIT_CAMPAIGN_PERIOD_SUCCESS = 'Der Zeitraum "%s" wurde erfolgreich geändert.';
    protected const ACTIVATE_CAMPAIGN_PERIOD_SUCCESS = 'Der Zeitraum mit der ID %d wurde erfolgreich aktiviert.';
    protected const DEACTIVATE_CAMPAIGN_PERIOD_SUCCESS = 'Der Zeitraum mit der ID %d wurde erfolgreich deaktiviert.';

    protected const CREATE_CAMPAIGN_PERIOD_ERROR = 'Leider konnte der Zeitraum "%s" nicht erstellt werden.';
    protected const EDIT_CAMPAIGN_PERIOD_ERROR = 'Bei der Änderung des Zeitraums "%s" ist leider ein Fehler aufgetreten.';
    protected const ACTIVATE_CAMPAIGN_PERIOD_ERROR = 'Leider konnte der Zeitraum mit der ID %d nicht aktiviert werden.';
    protected const DEACTIVATE_CAMPAIGN_PERIOD_ERROR = 'Leider konnte der Zeitraum mit der ID %d nicht deaktiviert werden.';

    protected const EDIT_CAMPAIGN_PERIOD_EXPIRED_ERROR = 'Der Zeitraum "%s" kann nicht mehr editiert werden.';

    /**
     * @return array
     */
    public function indexAction(): array
    {
        $table = $this
            ->getFactory()
            ->createCampaignPeriodTable();

        return $this
            ->viewResponse(
                [
                    'campaignPeriods' => $table->render()
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
            ->createCampaignPeriodTable();

        return $this
            ->jsonResponse(
                $table
                    ->fetchData()
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function periodsAction(Request $request): JsonResponse
    {
        $idCampaignPeriod = $request
            ->get(
                static::PARAM_ID_CAMPAIGN_PERIOD
            );

        if (
            is_numeric($idCampaignPeriod) ||
            $idCampaignPeriod > 0
        ) {
            $idCampaignPeriod = $this
                ->castId(
                    $idCampaignPeriod
                );
        } else {
            $idCampaignPeriod = null;
        }

        $periods = $this
            ->getFacade()
            ->getDatesWithCampaigns(
                $idCampaignPeriod
            );

        return $this
            ->jsonResponse(
                $periods
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
            ->createCampaignPeriodDataProvider();

        $form = $this
            ->getFactory()
            ->createCampaignPeriodForm(
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
            $campaignPeriod = $this
                ->getFormData(
                    $form
                );

            try {
                $success = $this
                    ->validateCampaignPeriod(
                        $campaignPeriod
                    );
            } catch (CampaignPeriodException $campaignPeriodException) {
                $this
                    ->addErrorMessage(
                        $campaignPeriodException
                            ->getMessage()
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            if ($success !== true) {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::CREATE_CAMPAIGN_PERIOD_ERROR,
                            $campaignPeriod
                                ->getCampaignName()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            try {
                $date = new DateTime($campaignPeriod->getCampaignEndDate() . '23:59:59');
                $endDate = $date->format('Y-m-d H:i:s');
                $campaignPeriod->setCampaignEndDate($endDate);

                $campaignPeriod = $this
                    ->getFacade()
                    ->saveCampaignPeriod(
                        $campaignPeriod
                    );
            } catch (CampaignPeriodException | CampaignAdvertisingMaterialException $exception) {
                $this
                    ->addErrorMessage(
                        $exception
                            ->getMessage()
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            $this
                ->addSuccessMessage(
                    sprintf(
                        static::CREATE_CAMPAIGN_PERIOD_SUCCESS,
                        $campaignPeriod
                            ->getCampaignName()
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
        $idCampaignPeriod = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_PERIOD)
            );

        $dataProvider = $this
            ->getFactory()
            ->createCampaignPeriodDataProvider();

        $campaignPeriodTransfer = $dataProvider
            ->getData($idCampaignPeriod);

        if ($campaignPeriodTransfer->getBookable() !== true) {
            $this
                ->addErrorMessage(
                    sprintf(
                        static::EDIT_CAMPAIGN_PERIOD_EXPIRED_ERROR,
                        $campaignPeriodTransfer
                            ->getCampaignName()
                    )
                );

            return $this
                ->redirectResponse(
                    Url::generate(
                        static::URL_VIEW,
                        [
                            static::PARAM_ID_CAMPAIGN_PERIOD => $idCampaignPeriod
                        ]
                    )
                    ->build()
                );
        }

        $form = $this
            ->getFactory()
            ->createCampaignPeriodForm(
                $campaignPeriodTransfer,
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
            $campaignPeriod = $this
                ->getFormData(
                    $form
                );

            try {
                $success = $this
                    ->validateCampaignPeriod(
                        $campaignPeriod
                    );
            } catch (CampaignPeriodException $campaignPeriodException) {
                $this
                    ->addErrorMessage(
                        $campaignPeriodException
                            ->getMessage()
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            if ($success !== true) {
                $this
                    ->addErrorMessage(
                        sprintf(
                            static::EDIT_CAMPAIGN_PERIOD_ERROR,
                            $campaignPeriod
                                ->getCampaignName()
                        )
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            try {
                $campaignPeriod = $this
                    ->getFacade()
                    ->saveCampaignPeriod(
                        $campaignPeriod
                    );
            } catch (CampaignPeriodException | CampaignAdvertisingMaterialException $exception) {
                $this
                    ->addErrorMessage(
                        $exception
                            ->getMessage()
                    );

                return $this
                    ->redirectResponse(
                        static::URL_LISTING
                    );
            }

            $this
                ->addSuccessMessage(
                    sprintf(
                        static::EDIT_CAMPAIGN_PERIOD_SUCCESS,
                        $campaignPeriod
                            ->getCampaignName()
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
    public function viewAction(Request $request)
    {
        $idCampaignPeriod = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_PERIOD)
            );

        try {
            $campaignPeriodTransfer = $this
                ->getFacade()
                ->getCampaignPeriodById(
                    $idCampaignPeriod
                );
        } catch (CampaignPeriodException $exception) {
            $this
                ->addErrorMessage(
                    $exception
                        ->getMessage()
                );

            return $this
                ->redirectResponse(
                    static::URL_LISTING
                );
        }

        return $this
            ->viewResponse(
                [
                    'campaignPeriod' => $campaignPeriodTransfer
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activateAction(Request $request): RedirectResponse
    {
        $idCampaignPeriod = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_PERIOD)
            );

        try {
            $success = $this
                ->getFacade()
                ->activateCampaignPeriod(
                    $idCampaignPeriod
                );

            if ($success === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::ACTIVATE_CAMPAIGN_PERIOD_SUCCESS,
                            $idCampaignPeriod
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
                        static::ACTIVATE_CAMPAIGN_PERIOD_ERROR,
                        $idCampaignPeriod
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
                    static::ACTIVATE_CAMPAIGN_PERIOD_ERROR,
                    $idCampaignPeriod
                )
            );

        return $this
            ->redirectResponse(
                static::URL_ACTIVATE
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deactivateAction(Request $request): RedirectResponse
    {
        $idCampaignPeriod = $this
            ->castId(
                $request
                    ->get(static::PARAM_ID_CAMPAIGN_PERIOD)
            );

        try {
            $success = $this
                ->getFacade()
                ->deactivateCampaignPeriod(
                    $idCampaignPeriod
                );

            if ($success === true) {
                $this
                    ->addSuccessMessage(
                        sprintf(
                            static::DEACTIVATE_CAMPAIGN_PERIOD_SUCCESS,
                            $idCampaignPeriod
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
                        static::DEACTIVATE_CAMPAIGN_PERIOD_ERROR,
                        $idCampaignPeriod
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
                    static::DEACTIVATE_CAMPAIGN_PERIOD_ERROR,
                    $idCampaignPeriod
                )
            );

        return $this
            ->redirectResponse(
                static::URL_ACTIVATE
            );
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return \Generated\Shared\Transfer\CampaignPeriodTransfer
     */
    protected function getFormData(FormInterface $form): CampaignPeriodTransfer
    {
        return $form
            ->getData();
    }

    /**
     * @param \Generated\Shared\Transfer\CampaignPeriodTransfer $campaignPeriodTransfer
     * @return bool
     */
    protected function validateCampaignPeriod(CampaignPeriodTransfer $campaignPeriodTransfer): bool
    {
        $validators = $this
            ->getFacade()
            ->getCampaignPeriodValidators();

        foreach ($validators as $validator) {
            $success = $validator
                ->isValid(
                    $campaignPeriodTransfer
                );

            if ($success !== true) {
                return false;
            }
        }

        return true;
    }
}
