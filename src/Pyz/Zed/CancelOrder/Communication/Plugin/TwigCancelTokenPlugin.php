<?php
/**
 * Durst - project - TwigCancelTokenPlugin.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 07.09.21
 * Time: 09:16
 */

namespace Pyz\Zed\CancelOrder\Communication\Plugin;

use Exception;
use Generated\Shared\Transfer\OrderTransfer;
use Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Spryker\Shared\Twig\TwigFunction;

/**
 * Class TwigCancelTokenPlugin
 * @package Pyz\Zed\CancelOrder\Communication\Plugin
 */
class TwigCancelTokenPlugin extends TwigFunction
{
    public const TWIG_NAME = 'cancelToken';

    /**
     * @var \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface
     */
    protected $facade;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Pyz\Zed\CancelOrder\CancelOrderConfig
     */
    protected $config;

    /**
     * @param \Pyz\Zed\CancelOrder\Business\CancelOrderFacadeInterface $facade
     * @param \Twig_Environment $environment
     * @param \Pyz\Zed\CancelOrder\CancelOrderConfig $config
     */
    public function __construct(
        CancelOrderFacadeInterface $facade,
        \Twig_Environment $environment,
        CancelOrderConfig $config
    )
    {
        $this->facade = $facade;
        $this->environment = $environment;
        $this->config = $config;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    protected function getFunctionName(): string
    {
        return static::TWIG_NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Closure
     */
    protected function getFunction(): \Closure
    {
        return function (?OrderTransfer $orderTransfer) {
            $error = '';
            $link = null;

            try {
                $jwtTransfer = $this
                    ->facade
                    ->generateTokenForIssuer(
                        $orderTransfer
                            ->getIdSalesOrder(),
                        $this
                            ->config
                            ->getIssuerFridge()
                    );

                if ($jwtTransfer->getErrors()->count() > 0) {
                    throw new Exception(
                        $jwtTransfer
                            ->getErrors()
                            ->offsetGet(0)
                            ->getMessage()
                    );
                }

                $this
                    ->facade
                    ->checkTransfer(
                        $jwtTransfer
                    );

                $link = sprintf(
                    $this
                        ->config
                        ->getFridgeCancelUrl(),
                    $jwtTransfer
                        ->getToken(),
                    sprintf(
                        '/%s/%s?%s=%s',
                        'sales',
                        'detail',
                        'id-sales-order',
                        $orderTransfer
                            ->getIdSalesOrder()
                    )
                );

            } catch (Exception $exception) {
                $error = $exception
                    ->getMessage();
            }

            return $this
                ->environment
                ->render(
                    '@CancelOrder/Form/Button/cancel-button.twig',
                    [
                        'link' => $link,
                        'error' => $error
                    ]
                );
        };
    }
}
