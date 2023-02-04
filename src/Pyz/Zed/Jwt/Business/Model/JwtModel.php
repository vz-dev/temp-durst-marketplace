<?php
/**
 * Durst - project - JwtModel.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 30.08.21
 * Time: 11:37
 */

namespace Pyz\Zed\Jwt\Business\Model;

use ArrayObject;
use DateTime;
use Exception;
use Generated\Shared\Transfer\JwtErrorTransfer;
use Generated\Shared\Transfer\JwtParameterTransfer;
use Generated\Shared\Transfer\JwtTransfer;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Pyz\Shared\Jwt\JwtConstants;
use Pyz\Zed\Jwt\Business\Exception\JwtBasicValidationException;
use Pyz\Zed\Jwt\Business\Exception\JwtException;
use Pyz\Zed\Jwt\Business\Exception\JwtSignNotMatchException;
use Pyz\Zed\Jwt\JwtConfig;

/**
 * Class JwtModel
 * @package Pyz\Zed\Jwt\Business\Model
 */
class JwtModel implements JwtInterface
{
    /**
     * @var \Lcobucci\JWT\Builder
     */
    protected $builder;

    /**
     * @var \Lcobucci\JWT\Parser
     */
    protected $parser;

    /**
     * @var \Lcobucci\JWT\Signer\Hmac\Sha512
     */
    protected $signer;

    /**
     * @var \Pyz\Zed\Jwt\JwtConfig
     */
    protected $config;

    /**
     * @var array|\Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface[]
     */
    protected $baseValidators;

    /**
     * @param \Lcobucci\JWT\Builder $builder
     * @param \Lcobucci\JWT\Parser $parser
     * @param \Lcobucci\JWT\Signer\Hmac\Sha512 $signer
     * @param \Pyz\Zed\Jwt\JwtConfig $config
     * @param array|\Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface[] $baseValidators
     */
    public function __construct(
        Builder $builder,
        Parser $parser,
        Sha512 $signer,
        JwtConfig $config,
        array $baseValidators
    )
    {
        $this->builder = $builder;
        $this->parser = $parser;
        $this->signer = $signer;
        $this->config = $config;
        $this->baseValidators = $baseValidators;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     * @throws \Exception
     */
    public function prepareToken(JwtTransfer $jwtTransfer): JwtTransfer
    {
        $this->builder
            ->setIssuer(
                $jwtTransfer
                    ->getIssuer()
            )
            ->setAudience(
                $jwtTransfer
                    ->getAudience()
            )
            ->setId(
                $jwtTransfer
                    ->getId()
            )
            ->setIssuedAt(
                $this
                    ->getIssuedAt(
                        $jwtTransfer
                    )
            )
            ->setNotBefore(
                $this
                    ->getNotBefore(
                        $jwtTransfer
                    )
            )
            ->setExpiration(
                $this
                    ->getExpiration(
                        $jwtTransfer
                    )
            )
            ->setSubject(
                $jwtTransfer
                    ->getSubject()
            );

        foreach ($jwtTransfer->getAdditionalParameters() as $additionalParameter) {
            $this
                ->builder
                ->set(
                    $additionalParameter
                        ->getKey(),
                    $additionalParameter
                        ->getValue()
                );
        }

        $this
            ->builder
            ->sign(
                $this
                    ->signer,
                $jwtTransfer
                    ->getSign()
            );

        $token = $this
            ->builder
            ->getToken();

        $jwtTransfer
            ->setToken(
                $token
                    ->__toString()
            );

        return $jwtTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Lcobucci\JWT\Token
     */
    public function getParsedToken(JwtTransfer $jwtTransfer): Token
    {
        return $this
            ->parser
            ->parse(
                $jwtTransfer
                    ->getToken()
            );
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function validateJwt(JwtTransfer $jwtTransfer): JwtTransfer
    {
        $jwtTransfer
            ->setErrors(
                new ArrayObject()
            );

        try {
            $this
                ->checkBaseValidation(
                    $jwtTransfer
                );
        } catch (Exception|JwtException $exception) {
            $error = (new JwtErrorTransfer())
                ->setCode(
                    $exception
                        ->getCode()
                )
                ->setMessage(
                    $exception
                        ->getMessage()
                );

            $jwtTransfer
                ->addError(
                    $error
                );
        }

        $validators = $this
            ->getValidators(
                $jwtTransfer
            );

        /* @var $validator \Pyz\Zed\Jwt\Business\Validator\JwtValidatorInterface */
        foreach ($validators as $validator) {
            try {
                if ($validator->isValid($jwtTransfer) !== true) {
                    $error = (new JwtErrorTransfer())
                        ->setCode(
                            $validator
                                ->getErrorCode()
                        )
                        ->setMessage(
                            $validator
                                ->getErrorMessage()
                        );

                    $jwtTransfer
                        ->addError(
                            $error
                        );
                }
            } catch (Exception|JwtException $exception) {
                $error = (new JwtErrorTransfer())
                    ->setCode(
                        $exception
                            ->getCode()
                    )
                    ->setMessage(
                        $exception
                            ->getMessage()
                    );

                $jwtTransfer
                    ->addError(
                        $error
                    );
            }
        }

        return $jwtTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @param string $sign
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function verifyJwt(
        JwtTransfer $jwtTransfer,
        string $sign
    ): JwtTransfer
    {
        try {
            $parsedToken = $this
                ->getParsedToken(
                    $jwtTransfer
                );

            $success = $parsedToken
                ->verify(
                    $this->signer,
                    $sign
                );

            if ($success !== true) {
                throw new JwtSignNotMatchException();
            }
        } catch (Exception|JwtException $exception) {
            $error = (new JwtErrorTransfer())
                ->setCode(
                    $exception
                        ->getCode()
                )
                ->setMessage(
                    $exception
                        ->getMessage()
                );

            $jwtTransfer
                ->addError(
                    $error
                );
        }

        return $jwtTransfer;
    }

    /**
     * @param string|null $token
     * @param string|null $sign
     * @return bool
     */
    public function verifySignByToken(
        ?string $token = null,
        ?string $sign = null
    ): bool
    {
        if (
            $token === null ||
            $sign === null
        ) {
            return false;
        }

        try {
            $parsedToken = $this
                ->parser
                ->parse(
                    $token
                );

            return $parsedToken
                ->verify(
                    $this->signer,
                    $sign
                );
        } catch (Exception|JwtException $exception) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param string $token
     * @return \Generated\Shared\Transfer\JwtTransfer
     */
    public function tokenToTransfer(string $token): JwtTransfer
    {
        $transfer = (new JwtTransfer())
            ->setToken(
                $token
            );

        $parsedToken = $this
            ->parser
            ->parse(
                $token
            );

        $claims = $parsedToken
            ->getClaims();

        /* @var $claim \Lcobucci\JWT\Claim */
        foreach ($claims as $claim) {
            $name = $claim
                ->getName();
            $value = $claim
                ->getValue();

            switch ($name) {
                case JwtConstants::KEY_AUDIENCE:
                    $transfer
                        ->setAudience(
                            $value
                        );
                    break;
                case JwtConstants::KEY_EXPIRATION:
                    $transfer
                        ->setExpiration(
                            (new DateTime())
                                ->setTimestamp($value)
                                ->format(JwtConstants::TRANSFER_TIME_FORMAT)
                        );
                    break;
                case JwtConstants::KEY_ID:
                    $transfer
                        ->setId(
                            $value
                        );
                    break;
                case JwtConstants::KEY_ISSUED_AT:
                    $transfer
                        ->setIssuedAt(
                            (new DateTime())
                                ->setTimestamp($value)
                                ->format(JwtConstants::TRANSFER_TIME_FORMAT)
                        );
                    break;
                case JwtConstants::KEY_ISSUER:
                    $transfer
                        ->setIssuer(
                            $value
                        );
                    break;
                case JwtConstants::KEY_NOT_BEFORE:
                    $transfer
                        ->setNotBefore(
                            (new DateTime())
                                ->setTimestamp($value)
                                ->format(JwtConstants::TRANSFER_TIME_FORMAT)
                        );
                    break;
                case JwtConstants::KEY_SUBJECT:
                    $transfer
                        ->setSubject(
                            $value
                        );
                    break;
                default:
                    $parameter = (new JwtParameterTransfer())
                        ->setKey($name)
                        ->setValue($value);
                    $transfer
                        ->addAdditionalParameter(
                            $parameter
                        );
                    break;
            }
        }

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return string
     * @throws \Exception
     */
    protected function getIssuedAt(JwtTransfer $jwtTransfer): string
    {
        $issuedAt = $jwtTransfer
            ->getIssuedAt();

        if (is_string($issuedAt)) {
            $issuedAt = new DateTime($issuedAt);
        }

        return $issuedAt
            ->format(
                JwtConstants::JWT_TIME_FORMAT
            );
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return string
     * @throws \Exception
     */
    protected function getNotBefore(JwtTransfer $jwtTransfer): string
    {
        $notBefore = $jwtTransfer
            ->getNotBefore();

        if (is_string($notBefore)) {
            $notBefore = new DateTime($notBefore);
        }

        return $notBefore
            ->format(
                JwtConstants::JWT_TIME_FORMAT
            );
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return string
     * @throws \Exception
     */
    protected function getExpiration(JwtTransfer $jwtTransfer): string
    {
        $expiration = $jwtTransfer
            ->getExpiration();

        if (is_string($expiration)) {
            $expiration = new DateTime($expiration);
        }

        return $expiration
            ->format(
                JwtConstants::JWT_TIME_FORMAT
            );
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return \ArrayObject
     */
    protected function getValidators(JwtTransfer $jwtTransfer): ArrayObject
    {
        $validators = $this
            ->baseValidators;

        foreach ($jwtTransfer->getValidators() as $validator) {
            $validators[] = $validator;
        }

        return new ArrayObject($validators);
    }

    /**
     * @param \Generated\Shared\Transfer\JwtTransfer $jwtTransfer
     * @return bool
     * @throws \Pyz\Zed\Jwt\Business\Exception\JwtBasicValidationException
     */
    protected function checkBaseValidation(JwtTransfer $jwtTransfer): bool
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

        $parsedToken = $this
            ->getParsedToken(
                $jwtTransfer
            );

        $success = $parsedToken
            ->validate(
                $validationData
            );

        if ($success !== true) {
            throw new JwtBasicValidationException();
        }

        return true;
    }
}
