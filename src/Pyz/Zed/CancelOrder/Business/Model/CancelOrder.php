<?php
/**
 * Durst - project - CancelOrder.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 17:05
 */

namespace Pyz\Zed\CancelOrder\Business\Model;

use DateInterval;
use DateTime;
use Exception;
use Generated\Shared\Transfer\CancelOrderTransfer;
use Generated\Shared\Transfer\ConcreteTourTransfer;
use Generated\Shared\Transfer\JwtErrorTransfer;
use Generated\Shared\Transfer\JwtParameterTransfer;
use Generated\Shared\Transfer\JwtTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Lcobucci\JWT\ValidationData;
use Orm\Zed\CancelOrder\Persistence\DstCancelOrder;
use Propel\Runtime\Exception\PropelException;
use Pyz\Shared\CancelOrder\CancelOrderConstants;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderConcreteTourNotFoundException;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderIssuerNotValidException;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderNotFoundException;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderSalesOrderNotFoundException;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderTokenExpiredException;
use Pyz\Zed\CancelOrder\Business\Exception\CancelOrderTokenNotValidException;
use Pyz\Zed\CancelOrder\Business\Hydrator\CancelOrderHydratorInterface;
use Pyz\Zed\CancelOrder\CancelOrderConfig;
use Pyz\Zed\CancelOrder\Persistence\CancelOrderQueryContainerInterface;
use Pyz\Zed\Jwt\Business\JwtFacadeInterface;
use Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface;
use Pyz\Zed\Sales\Business\SalesFacadeInterface;
use Pyz\Zed\Tour\Business\TourFacadeInterface;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Class CancelOrder
 * @package Pyz\Zed\CancelOrder\Business\Model
 */
class CancelOrder implements CancelOrderInterface
{
    /**
     * @var JwtFacadeInterface
     */
    protected $jwtFacade;

    /**
     * @var SalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var TourFacadeInterface
     */
    protected $tourFacade;

    /**
     * @var CancelOrderQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var CancelOrderConfig
     */
    protected $config;

    /**
     * @var CancelOrderHydratorInterface[]
     */
    protected $hydrators;

    /**
     * @var JwtValidatorInterface[]
     */
    protected $validators;

    /**
     * @param JwtFacadeInterface $jwtFacade
     * @param SalesFacadeInterface $salesFacade
     * @param TourFacadeInterface $tourFacade
     * @param CancelOrderQueryContainerInterface $queryContainer
     * @param CancelOrderConfig $config
     * @param CancelOrderHydratorInterface[] $hydrators
     * @param JwtValidatorInterface[] $validators
     */
    public function __construct(
        JwtFacadeInterface $jwtFacade,
        SalesFacadeInterface $salesFacade,
        TourFacadeInterface $tourFacade,
        CancelOrderQueryContainerInterface $queryContainer,
        CancelOrderConfig $config,
        array $hydrators,
        array $validators
    )
    {
        $this->jwtFacade = $jwtFacade;
        $this->salesFacade = $salesFacade;
        $this->tourFacade = $tourFacade;
        $this->queryContainer = $queryContainer;
        $this->config = $config;
        $this->hydrators = $hydrators;
        $this->validators = $validators;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param DateTime|null $manualExpireDate
     * @return JwtTransfer
     * @throws CancelOrderConcreteTourNotFoundException
     * @throws CancelOrderIssuerNotValidException
     * @throws CancelOrderSalesOrderNotFoundException
     */
    public function generateToken(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        $orderTransfer = $this
            ->getSalesOrderById(
                $idSalesOrder
            );

        return $this
            ->generateTokenForIssuer(
                $idSalesOrder,
                $orderTransfer
                    ->getCancelIssuer(),
                $manualExpireDate
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param string|null $issuer
     * @param DateTime|null $manualExpireDate
     * @return JwtTransfer
     * @throws CancelOrderConcreteTourNotFoundException
     * @throws CancelOrderIssuerNotValidException
     * @throws CancelOrderSalesOrderNotFoundException
     */
    public function generateTokenForIssuer(
        int $idSalesOrder,
        ?string $issuer = null,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        $this
            ->isIssuerValid(
                $issuer
            );

        $orderTransfer = $this
            ->getSalesOrderById(
                $idSalesOrder
            );

        $jwtTransfer = $this
            ->createJwtTransferForSalesOrderAndIssuer(
                $orderTransfer,
                $issuer,
                $manualExpireDate
            );

        $jwtTransfer = $this
            ->jwtFacade
            ->prepareToken(
                $jwtTransfer
            );

        return $this
            ->jwtFacade
            ->validateJwt($jwtTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return JwtTransfer
     */
    public function getJwtFromToken(
        string $token
    ): JwtTransfer
    {
        $jwtTransfer = $this
            ->jwtFacade
            ->tokenToTransfer(
                $token
            );

        return $jwtTransfer
            ->setValidators(
                $this
                    ->validators
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param JwtTransfer $jwtTransfer
     * @return bool
     */
    public function isValid(
        JwtTransfer $jwtTransfer
    ): bool
    {
        try {
            $this
                ->validateTransfer(
                    $jwtTransfer
                );
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool
    {
        return $this
            ->jwtFacade
            ->verifySignByToken(
                $token,
                $sign
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param JwtTransfer $jwtTransfer
     * @throws CancelOrderSalesOrderNotFoundException
     * @throws CancelOrderTokenExpiredException
     * @throws CancelOrderTokenNotValidException
     */
    public function checkTransfer(
        JwtTransfer $jwtTransfer
    ): void
    {
        $this
            ->validateTransfer(
                $jwtTransfer
            );
    }

    /**
     * @param JwtTransfer $jwtTransfer
     * @return JwtTransfer
     */
    public function executeJwtValidators(
        JwtTransfer $jwtTransfer
    ): JwtTransfer
    {
        return $this
            ->jwtFacade
            ->validateJwt(
                $jwtTransfer
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return bool
     * @throws PropelException
     * @throws CancelOrderSalesOrderNotFoundException
     * @throws CancelOrderTokenExpiredException
     * @throws CancelOrderTokenNotValidException
     */
    public function saveCancelOrder(
        string $token
    ): bool
    {
        $transfer = $this
            ->getJwtFromToken(
                $token
            );

        $this
            ->validateTransfer(
                $transfer
            );

        $parsedToken = $this
            ->jwtFacade
            ->getParsedToken(
                $transfer
            );

        $cancelOrderEntity = (new DstCancelOrder())
            ->setFkSalesOrderAddressBilling(
                $parsedToken
                    ->getClaim(
                        CancelOrderConstants::KEY_ID_BILLING
                    )
            )
            ->setFkSalesOrderAddressShipping(
                $parsedToken
                    ->getClaim(
                        CancelOrderConstants::KEY_ID_SHIPPING
                    )
            )
            ->setFkConcreteTour(
                $parsedToken
                    ->getClaim(
                        CancelOrderConstants::KEY_ID_CONCRETE_TOUR
                    )
            )
            ->setFkDriver(
                $parsedToken
                    ->getClaim(
                        CancelOrderConstants::KEY_ID_DRIVER
                    )
            )
            ->setFkSalesOrder(
                $transfer
                    ->getId()
            )
            ->setEmail(
                $transfer
                    ->getSign()
            )
            ->setToken(
                $transfer
                    ->getToken()
            )
            ->save();

        return ($cancelOrderEntity > 0);
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idCancelOrder
     * @return CancelOrderTransfer
     * @throws CancelOrderNotFoundException
     */
    public function getCancelOrderById(
        int $idCancelOrder
    ): CancelOrderTransfer
    {
        $cancelOrder = $this
            ->queryContainer
            ->queryCancelOrder()
            ->findPk(
                $idCancelOrder
            );

        if ($cancelOrder === null) {
            throw new CancelOrderNotFoundException(
                sprintf(
                    CancelOrderNotFoundException::MESSAGE,
                    $idCancelOrder
                )
            );
        }

        return $this
            ->entityToTransfer(
                $cancelOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @return CancelOrderTransfer|null
     * @throws AmbiguousComparisonException
     */
    public function getCancelOrderByIdSalesOrder(
        int $idSalesOrder
    ): ?CancelOrderTransfer
    {
        $cancelOrder = $this
            ->queryContainer
            ->queryCancelOrder()
            ->filterByFkSalesOrder(
                $idSalesOrder
            )
            ->findOne();

        if ($cancelOrder === null) {
            return null;
        }

        return $this
            ->entityToTransfer(
                $cancelOrder
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param DateTime|null $manualExpireDate
     * @return string|null
     */
    public function getTokenForCustomerMail(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): ?string
    {
        try {
            $jwt = $this
                ->generateTokenForIssuer(
                    $idSalesOrder,
                    $this
                        ->config
                        ->getIssuerCustomer(),
                    $manualExpireDate
                );

            $this
                ->validateTransfer(
                    $jwt
                );

            $this
                ->verifyToken(
                    $jwt
                );

            if ($jwt->getErrors()->count() === 0) {
                return $jwt
                    ->getToken();
            }
        } catch (Exception $exception) {
            return null;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param DateTime|null $manualExpireDate
     * @return JwtTransfer
     */
    public function getJwtTransferForFridge(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        try {
            $transfer = $this
                ->generateTokenForIssuer(
                    $idSalesOrder,
                    $this
                        ->config
                        ->getIssuerFridge(),
                    $manualExpireDate
                );

            $this
                ->validateTransfer(
                    $transfer
                );

            $this
                ->verifyToken(
                    $transfer
                );

            return $transfer;
        } catch (Exception $exception) {
            $error = (new JwtErrorTransfer())
                ->setCode(
                    $exception
                        ->getCode()
                )
                ->setMessage(
                    $exception
                        ->getMessage()
                );

            return (new JwtTransfer())
                ->addError(
                    $error
                );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param int $idSalesOrder
     * @param DateTime|null $manualExpireDate
     * @return JwtTransfer
     */
    public function getJwtTransferForDriver(
        int $idSalesOrder,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        try {
            $transfer = $this
                ->generateTokenForIssuer(
                    $idSalesOrder,
                    $this
                        ->config
                        ->getIssuerDriver(),
                    $manualExpireDate
                );

            $this
                ->validateTransfer(
                    $transfer
                );

            $this
                ->verifyToken(
                    $transfer
                );

            return $transfer;
        } catch (Exception $exception) {
            $error = (new JwtErrorTransfer())
                ->setCode(
                    $exception
                        ->getCode()
                )
                ->setMessage(
                    $exception
                        ->getMessage()
                );

            return (new JwtTransfer())
                ->addError(
                    $error
                );
        }
    }

    /**
     * @param OrderTransfer $orderTransfer
     * @param string $issuer
     * @param DateTime|null $manualExpireDate
     * @return JwtTransfer
     * @throws CancelOrderConcreteTourNotFoundException
     */
    protected function createJwtTransferForSalesOrderAndIssuer(
        OrderTransfer $orderTransfer,
        string $issuer,
        ?DateTime $manualExpireDate = null
    ): JwtTransfer
    {
        if($orderTransfer->getFkTour() !== null)
        {
            $tour = $this
                ->getConcreteTourById(
                    $orderTransfer->getFkTour()
                );

            $preparationStart = $tour
                ->getPreparationStart();

            if (is_string($preparationStart)) {
                $preparationStart = new DateTime($preparationStart);
            }
        }else{
            $preparationStart = (new DateTime($orderTransfer->getGmStartTime()))->sub(new DateInterval('PT6H'));
        }

        $expireToken = $manualExpireDate;

        if ($manualExpireDate === null) {
            $expireToken = clone $preparationStart;
            $expireToken
                ->modify(
                    $this
                        ->config
                        ->getCancelLeadTime()
                );
        }

        $jwtTransfer = new JwtTransfer();

        $jwtTransfer
            ->setAudience(
                $orderTransfer
                    ->getBranch()
                    ->getName()
            )
            ->setExpiration(
                $expireToken
            )
            ->setId(
                $orderTransfer
                    ->getIdSalesOrder()
            )
            ->setIssuedAt(
                $orderTransfer
                    ->getCreatedAt()
            )
            ->setIssuer(
                $issuer
            )
            ->setNotBefore(
                $orderTransfer
                    ->getCreatedAt()
            )
            ->setSubject(
                $orderTransfer
                    ->getOrderReference()
            )
            ->addAdditionalParameter(
                (new JwtParameterTransfer())
                    ->setKey(
                        CancelOrderConstants::KEY_ID_BILLING
                    )
                    ->setValue(
                        $orderTransfer
                            ->getBillingAddress()
                            ->getIdSalesOrderAddress()
                    )
            )
            ->addAdditionalParameter(
                (new JwtParameterTransfer())
                    ->setKey(
                        CancelOrderConstants::KEY_ID_SHIPPING
                    )
                    ->setValue(
                        $orderTransfer
                            ->getShippingAddress()
                            ->getIdSalesOrderAddress()
                    )
            );

            if($orderTransfer->getFkTour() !== null)
            {
                $jwtTransfer->addAdditionalParameter(
                    (new JwtParameterTransfer())
                        ->setKey(
                            CancelOrderConstants::KEY_ID_CONCRETE_TOUR
                        )
                        ->setValue(
                            $tour
                                ->getIdConcreteTour()
                        )
                );
            }

            $jwtTransfer->addAdditionalParameter(
                (new JwtParameterTransfer())
                    ->setKey(
                        CancelOrderConstants::KEY_ID_DRIVER
                    )
                    ->setValue(
                        $orderTransfer
                            ->getFkDriver()
                    )
            )
            ->addAdditionalParameter(
                (new JwtParameterTransfer())
                    ->setKey(
                        CancelOrderConstants::KEY_TOUR_START
                    )
                    ->setValue(
                        $preparationStart
                    )
            )
            ->addAdditionalParameter(
                (new JwtParameterTransfer())
                    ->setKey(
                        CancelOrderConstants::KEY_MESSAGE
                    )
                    ->setValue(
                        $orderTransfer
                            ->getCancelMessage()
                    )
            )
            ->setValidators(
                $this
                    ->validators
            )
            ->setSign(
                $orderTransfer
                    ->getEmail()
            );

        return $jwtTransfer;
    }

    /**
     * @param int $idSalesOrder
     * @return OrderTransfer
     * @throws CancelOrderSalesOrderNotFoundException
     */
    protected function getSalesOrderById(
        int $idSalesOrder
    ): OrderTransfer
    {
        $order = $this
            ->salesFacade
            ->getOrderByIdSalesOrder(
                $idSalesOrder
            );

        if ($order === null) {
            throw new CancelOrderSalesOrderNotFoundException(
                sprintf(
                    CancelOrderSalesOrderNotFoundException::MESSAGE,
                    $idSalesOrder
                )
            );
        }

        return $order;
    }

    /**
     * @param int $idConcreteTour
     * @return ConcreteTourTransfer
     * @throws CancelOrderConcreteTourNotFoundException
     */
    protected function getConcreteTourById(
        int $idConcreteTour
    ): ConcreteTourTransfer
    {
        $tour = $this
            ->tourFacade
            ->getConcreteTourById(
                $idConcreteTour
            );

        if ($tour === null) {
            throw new CancelOrderConcreteTourNotFoundException(
                sprintf(
                    CancelOrderConcreteTourNotFoundException::MESSAGE,
                    $idConcreteTour
                )
            );
        }

        return $tour;
    }

    /**
     * @param string|null $issuer
     * @return bool
     * @throws CancelOrderIssuerNotValidException
     */
    protected function isIssuerValid(
        ?string $issuer = null
    ): bool
    {
        $isValid = (in_array($issuer, $this->config->getPossibleIssuers()));

        if ($isValid !== true) {
            throw new CancelOrderIssuerNotValidException(
                sprintf(
                    CancelOrderIssuerNotValidException::MESSAGE,
                    $issuer
                )
            );
        }

        return true;
    }

    /**
     * @param JwtTransfer $jwtTransfer
     * @return ValidationData
     */
    protected function getValidationData(
        JwtTransfer $jwtTransfer
    ): ValidationData
    {
        $validationData = new ValidationData();

        $validationData
            ->setAudience(
                $jwtTransfer
                    ->getAudience()
            );
        $validationData
            ->setSubject(
                $jwtTransfer
                    ->getSubject()
            );
        $validationData
            ->setIssuer(
                $jwtTransfer
                    ->getIssuer()
            );
        $validationData
            ->setId(
                $jwtTransfer
                    ->getId()
            );
        $validationData
            ->setCurrentTime(
                time()
            );

        return $validationData;
    }

    /**
     * @param JwtTransfer $jwtTransfer
     * @return bool
     * @throws CancelOrderSalesOrderNotFoundException
     */
    protected function verifyToken(
        JwtTransfer $jwtTransfer
    ): bool
    {
        if ($jwtTransfer->getErrors()->count() > 0) {
            return false;
        }

        $salesOrder = $this
            ->getSalesOrderById(
                $jwtTransfer
                    ->getId()
            );

        $sign = $salesOrder
            ->getEmail();

        $jwtTransfer
            ->setSign(
                $sign
            );

        $jwtTransfer = $this
            ->jwtFacade
            ->verifyJwt(
                $jwtTransfer,
                $sign
            );

        if ($jwtTransfer->getErrors()->count() > 0) {
            return false;
        }

        return true;
    }

    /**
     * @param DstCancelOrder $cancelOrder
     * @return CancelOrderTransfer
     */
    protected function entityToTransfer(
        DstCancelOrder $cancelOrder
    ): CancelOrderTransfer
    {
        $transfer = (new CancelOrderTransfer())
            ->fromArray(
                $cancelOrder
                    ->toArray(),
                true
            );

        foreach ($this->hydrators as $hydrator) {
            $hydrator
                ->hydrateCancelOrder(
                    $transfer
                );
        }

        return $transfer;
    }

    /**
     * @param JwtTransfer $jwtTransfer
     * @throws CancelOrderSalesOrderNotFoundException
     * @throws CancelOrderTokenExpiredException
     * @throws CancelOrderTokenNotValidException
     */
    protected function validateTransfer(
        JwtTransfer $jwtTransfer
    ): void
    {
        $parsedToken = $this
            ->jwtFacade
            ->getParsedToken(
                $jwtTransfer
            );

        /**
         * Token is expired check
         */
        $isExpired = $parsedToken
            ->isExpired();

        if ($isExpired === true) {
            throw new CancelOrderTokenExpiredException(
                CancelOrderTokenExpiredException::MESSAGE
            );
        }

        /**
         * Token is not valid check (claims)
         */
        $isValid = $parsedToken
            ->validate(
                $this
                    ->getValidationData(
                        $jwtTransfer
                    )
            );

        if ($isValid !== true) {
            throw new CancelOrderTokenNotValidException(
                CancelOrderTokenNotValidException::MESSAGE
            );
        }

        /**
         * Sign is not the same between token and email from sales order
         */
        $isVerified = $this
            ->verifyToken(
                $jwtTransfer
            );

        if ($isVerified !== true) {
            throw new CancelOrderTokenNotValidException(
                CancelOrderTokenNotValidException::MESSAGE
            );
        }
    }
}
