<?php
/**
 * Durst - project - CodeGenerator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 09:32
 */

namespace Pyz\Zed\Merchant\Business\Code;

use Pyz\Zed\Merchant\Business\Exception\Code\CannotFindFreeCodeException;
use Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException;
use Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class CodeGenerator implements CodeGeneratorInterface
{
    public const AMOUNT_OF_DIGITS = 8;
    public const PATTERN = '/^\d{8}$/';
    public const MIN = 1;
    public const MAX = 99999999;

    /**
     * @var \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface $queryContainer
     */
    public function __construct(
        MerchantQueryContainerInterface $queryContainer
    )
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param string $code
     * @return bool
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeExistsException
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CodeMalformedException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function checkCode(string $code): bool
    {
        if($this->codeExists($code) === true) {
            throw new CodeExistsException(
                sprintf(
                    CodeExistsException::MESSAGE,
                    $code
                )
            );
        }

        if($this->isCodeMalformed($code) !== false) {
            throw new CodeMalformedException(
                sprintf(
                    CodeMalformedException::MESSAGE,
                    self::AMOUNT_OF_DIGITS
                )
            );
        }

        return true;
    }

    /**
     * @return string
     * @throws \Pyz\Zed\Merchant\Business\Exception\Code\CannotFindFreeCodeException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    public function generateCode(): string
    {
        $code = $this->getRandomCode();
        $counter = 0;
        while($this->codeExists($code) === true){
            $code = $this->getRandomCode();
            $counter++;

            if($counter >= 1000){
                throw new CannotFindFreeCodeException(
                    sprintf(
                        CannotFindFreeCodeException::MESSAGE,
                        self::AMOUNT_OF_DIGITS
                    )
                );
            }
        }

        return $code;
    }

    /**
     * @param string $code
     * @return bool
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function codeExists(string $code): bool
    {
        return $this
                ->queryContainer
                ->queryBranchByCode($code)
                ->count() > 0;
    }

    /**
     * @param string $code
     * @return bool
     */
    protected function isCodeMalformed(string $code) : bool
    {
        return preg_match(self::PATTERN, $code) !== 1;
    }

    /**
     * @return string
     */
    protected function getRandomCode() : string
    {
        $randomNumber = rand(self::MIN, self::MAX);
        return sprintf('%\'.0' . self::AMOUNT_OF_DIGITS . 'd', $randomNumber);
    }
}
