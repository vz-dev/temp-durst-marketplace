<?php
/**
 * Durst - project - CancelController.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 14.09.21
 * Time: 11:43
 */

namespace Pyz\Yves\CancelOrder\Controller;

use Generated\Shared\Transfer\CancelOrderCustomerRequestTransfer;
use Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer;
use Generated\Shared\Transfer\JwtTransfer;
use Pyz\Client\CancelOrder\CancelOrderClientInterface;
use Pyz\Yves\Application\Controller\AbstractController;
use Pyz\Yves\CancelOrder\CancelOrderFactory;
use Pyz\Yves\CancelOrder\Form\CancelOrderForm;
use Pyz\Yves\CancelOrder\Plugin\Provider\CancelOrderControllerProvider;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CancelController
 * @package Pyz\Yves\CancelOrder\Controller
 *
 * @method CancelOrderFactory getFactory()
 * @method CancelOrderClientInterface getClient()
 */
class CancelController extends AbstractController
{
    protected const URL_PARAM_TOKEN = 't';

    protected const CANCEL_ORDER_ERROR = 'cancel-order-error';
    protected const CANCEL_ORDER_SUCCESS = 'cancel-order-success';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     * @throws \Spryker\Yves\Kernel\Exception\Container\ContainerKeyNotFoundException
     */
    public function cancelAction(Request $request)
    {
        $transfer = $this
            ->getParsedToken(
                $request
            );

        // there is no JWT or sales order, something happened to the token
        if (
            $transfer->getJwt() === null ||
            $transfer->getSalesOrder() === null
        ) {
            $this
                ->addErrorMessage(
                    static::CANCEL_ORDER_ERROR
                );

            return $this
                ->redirectResponseInternal(
                    CancelOrderControllerProvider::ROUTE_CANCEL_ORDER_FAILED,
                    [
                        static::URL_PARAM_TOKEN => $request
                            ->get(
                                static::URL_PARAM_TOKEN
                            )
                    ]
                );
        }

        // check, if there is an error on the response or JWT transfer
        $error = $this
            ->getErrorFromTransfer(
                $transfer
            );

        if ($error !== null) {
            $this
                ->addErrorMessage(
                    $error
                );

            return $this
                ->redirectResponseInternal(
                    CancelOrderControllerProvider::ROUTE_CANCEL_ORDER_FAILED,
                    [
                        static::URL_PARAM_TOKEN => $request
                            ->get(
                                static::URL_PARAM_TOKEN
                            )
                    ]
                );
        }

        $form = $this
            ->getFactory()
            ->createCancelOrderFormFactory()
            ->createCancelOrderForm(
                [
                    CancelOrderForm::OPTION_TOKEN => $transfer
                        ->getJwt()
                        ->getToken()
                ]
            )
            ->handleRequest(
                $request
            );

        if (
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            // check, if the given mail is used to sign the token
            $check = $this
                ->verifySigner(
                    $form
                );

            if ($check->getErrorMessage() !== null) {
                $this
                    ->addErrorMessage(
                        $check
                            ->getErrorMessage()
                    );

                return $this
                    ->redirectResponseInternal(
                        CancelOrderControllerProvider::ROUTE_CANCEL_ORDER_FAILED,
                        [
                            static::URL_PARAM_TOKEN => $request
                                ->get(
                                    static::URL_PARAM_TOKEN
                                )
                        ]
                    );
            }

            // all seems good, cancel the given order
            $this
                ->getClient()
                ->cancelOrderByCustomer(
                    (new CancelOrderCustomerRequestTransfer())
                        ->setToken(
                            $transfer
                                ->getJwt()
                                ->getToken()
                        )
                );

            $this
                ->addSuccessMessage(
                    static::CANCEL_ORDER_SUCCESS
                );

            return $this
                ->redirectResponseInternal(
                    CancelOrderControllerProvider::ROUTE_CANCEL_ORDER_SUCCESS,
                    [
                        static::URL_PARAM_TOKEN => $request
                            ->get(
                                static::URL_PARAM_TOKEN
                            )
                    ]
                );
        }

        return $this
            ->viewResponse(
                [
                    'customer' => $transfer,
                    'form' => $form->createView()
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function failAction(Request $request): array
    {
        $transfer = $this
            ->getParsedToken(
                $request
            );

        return $this
            ->viewResponse(
                [
                    'customer' => $transfer,
                    'error' => $this->getErrorFromTransfer($transfer),
                    'again' => $this->redirectResponseInternal(
                        CancelOrderControllerProvider::ROUTE_CANCEL_ORDER_CANCEL,
                        [
                            static::URL_PARAM_TOKEN => $request
                                ->get(
                                    static::URL_PARAM_TOKEN
                                )
                        ]
                    )
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function successAction(Request $request): array
    {
        $transfer = $this
            ->getParsedToken(
                $request
            );

        return $this
            ->viewResponse(
                [
                    'customer' => $transfer,
                    'error' => $this->getErrorFromTransfer($transfer)
                ]
            );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    protected function getParsedToken(
        Request $request
    ): CancelOrderCustomerResponseTransfer
    {
        $token = $request
            ->get(
                static::URL_PARAM_TOKEN
            );

        $clientRequest = (new CancelOrderCustomerRequestTransfer())
            ->setToken(
                $token
            );

        return $this
            ->getClient()
            ->parseToken(
                $clientRequest
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer $cancelOrderCustomerResponseTransfer
     * @return string|null
     */
    protected function getErrorFromTransfer(
        CancelOrderCustomerResponseTransfer $cancelOrderCustomerResponseTransfer
    ): ?string
    {
        if ($cancelOrderCustomerResponseTransfer->getErrorMessage() !== null) {
            return $cancelOrderCustomerResponseTransfer
                ->getErrorMessage();
        }

        $jwtTransfer = $cancelOrderCustomerResponseTransfer
            ->getJwt();

        if ($jwtTransfer->getErrors()->count() > 0) {
            return $jwtTransfer
                ->getErrors()
                ->offsetGet(0)
                ->getMessage();
        }

        return null;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @return \Generated\Shared\Transfer\CancelOrderCustomerResponseTransfer
     */
    protected function verifySigner(
        FormInterface $form
    ): CancelOrderCustomerResponseTransfer
    {
        $data = $form
            ->getData();

        $clientRequest = (new CancelOrderCustomerRequestTransfer())
            ->setToken(
                $data[CancelOrderForm::FIELD_TOKEN]
            )
            ->setSigner(
                $data[CancelOrderForm::FIELD_MAIL]
            );

        return $this
            ->getClient()
            ->verifySigner(
                $clientRequest
            );
    }
}
