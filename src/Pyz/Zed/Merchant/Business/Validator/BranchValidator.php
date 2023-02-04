<?php
/**
 * Durst - project - BranchValidator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 02.12.21
 * Time: 12:11
 */

namespace Pyz\Zed\Merchant\Business\Validator;

use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\CartPreCheckResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyBranchTableMap;
use Pyz\Zed\Merchant\Business\Exception\BranchInactiveException;
use Pyz\Zed\Merchant\Persistence\MerchantQueryContainerInterface;

class BranchValidator
{
    public const ERROR_BRANCH_INVALID = 'You cannot order at a merchant that does not exist or is inactive';
    public const ERROR_CODE_BRANCH_INVALID = 9876;

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
     * @param \Generated\Shared\Transfer\CartChangeTransfer $cartChangeTransfer
     * @return mixed
     */
    public function validateBranchIsActive(CartChangeTransfer $cartChangeTransfer)
    {
        $cartChangeTransfer->requireBranch();

        $idBranch = $cartChangeTransfer->getBranch()->getIdBranch();

        $response = $this->createResponseTransfer();

        if ($this->isBranchActive($idBranch) !== true){
            $response->setIsSuccess(false);
            $message = (new MessageTransfer())
                ->setValue(self::ERROR_BRANCH_INVALID);
            $response->addMessage($message);

            return $response;
        }

        $response->setIsSuccess(true);

        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     * @return bool
     */
    public function validateBranchIsActiveCheckout(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) : bool
    {

        if ($this->isBranchActive($quoteTransfer->getFkBranch()) !== true) {
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError(
                    $this->createErrorTransfer(
                        self::ERROR_BRANCH_INVALID,
                        self::ERROR_CODE_BRANCH_INVALID
                    )
                );

            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @param int $code
     * @return \Generated\Shared\Transfer\CheckoutErrorTransfer
     */
    protected function createErrorTransfer(string $message, int $code) : CheckoutErrorTransfer
    {
        return (new CheckoutErrorTransfer())
            ->setMessage($message)
            ->setErrorCode($code);
    }

    /**
     * @param int $idBranch
     * @return bool
     * @throws \Pyz\Zed\Merchant\Business\Exception\BranchInactiveException
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     */
    protected function isBranchActive(int $idBranch) : bool
    {
        $entity = $this
            ->queryContainer
            ->queryBranchById($idBranch)
            ->filterByStatus(SpyBranchTableMap::COL_STATUS_ACTIVE)
            ->findOne();

        if($entity === null) {

            /**
             * For now we will throw an exception. This is only necessary for the webservices as
             * messages won't be displayed. Therefore, it is much easier for debugging to throw an exception.
             * As soon as the b2b-shop is developed we need to return a response object to inform the customer
             * what went wrong.
             */

            throw new BranchInactiveException(
                sprintf(
                    BranchInactiveException::MESSAGE,
                    $idBranch
                )
            );

            /** @noinspection PhpUnreachableStatementInspection */
            return false;
        }

        return true;
    }

    /**
     * @return \Generated\Shared\Transfer\CartPreCheckResponseTransfer
     */
    protected function createResponseTransfer() : CartPreCheckResponseTransfer
    {
        return new CartPreCheckResponseTransfer();
    }
}
