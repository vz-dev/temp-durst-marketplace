<?php
/**
 * Created by PhpStorm.
 * User: mbicker
 * Date: 15.01.18
 * Time: 16:40
 */

namespace Pyz\Zed\TermsOfService\Communication\Controller;


use Pyz\Zed\TermsOfService\Business\TermsOfServiceFacadeInterface;
use Pyz\Zed\TermsOfService\Communication\Form\TermsOfServiceForm;
use Pyz\Zed\TermsOfService\Communication\TermsOfServiceCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AController
 * @package Pyz\Zed\TermsOfService\Communication\Controller
 * @method TermsOfServiceFacadeInterface getFacade()
 * @method TermsOfServiceCommunicationFactory getFactory()
 */
class FormController extends AbstractController
{
    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Pyz\Zed\TermsOfService\Business\Exception\TermsOfServiceAlreadyAcceptedException
     * @throws \Spryker\Zed\Kernel\Exception\Container\ContainerKeyNotFoundException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function indexAction(Request $request)
    {
        $idCurrentMerchant = $this
            ->getFactory()
            ->getMerchantFacade()
            ->getCurrentMerchant()
            ->getIdMerchant();

        $termsOfService = $this
            ->getFacade()
            ->getUnacceptedTermsOfServiceByIdMerchant($idCurrentMerchant);

        $form = $this
            ->getFactory()
            ->createTermsOfServiceForm(
                [],
                [TermsOfServiceForm::OPTION_BUTTON_LABEL => $termsOfService->getButtonText()]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this
                ->getFacade()
                ->acceptTermsOfServiceByIdForMerchantById($termsOfService->getIdTermsOfService(), $idCurrentMerchant);

            return $this->redirectResponse('/');
        }

        return $this
            ->viewResponse([
                'form' => $form->createView(),
                'hintText' => $termsOfService->getHintText(),
                'text' => $termsOfService->getText(),
                'title' => 'Durst Händler-Bereich - Bitte Hinweis beachten',
            ]);
    }
}