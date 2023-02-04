<?php
/**
 * Durst - project - DriverBranchRequestHandler.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 2019-06-04
 * Time: 13:49
 */

namespace Pyz\Yves\AppRestApi\Handler;

use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Pyz\Client\Merchant\MerchantClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DriverBranchRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DriverBranchResponseInterface as Response;
use Pyz\Yves\AppRestApi\Validator\SchemaValidatorTrait;
use Spryker\Shared\Log\LoggerTrait;
use stdClass;

class DriverBranchRequestHandler implements RequestHandlerInterface
{
    use SchemaValidatorTrait;
    use LoggerTrait;

    /**
     * @var \Pyz\Yves\AppRestApi\AppRestApiConfig
     */
    protected $config;

    /**
     * @var \Pyz\Client\Merchant\MerchantClientInterface
     */
    protected $merchantClient;

    /**
     * DriverBranchRequestHandler constructor.
     *
     * @param \Pyz\Yves\AppRestApi\AppRestApiConfig $config
     * @param \Pyz\Client\Merchant\MerchantClientInterface $merchantClient
     */
    public function __construct(
        AppRestApiConfig $config,
        MerchantClientInterface $merchantClient
    ) {
        $this->config = $config;
        $this->merchantClient = $merchantClient;
    }

    /**
     * @param string $json
     *
     * @return \stdClass
     */
    public function handleJson(string $json, string $version = 'v1'): stdClass
    {
        $requestObject = json_decode($json);

        $this
            ->validate(
                $requestObject,
                $this
                    ->config
                    ->getDriverBranchRequestSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        $responseObject = $this->createStdClass();

        $this
            ->hydrate($requestObject, $responseObject);

        $this
            ->validate(
                $responseObject,
                $this
                    ->config
                    ->getDriverBranchResponseSchemaPath()
            );

        if ($this->isValid !== true) {
            return $this
                ->errors;
        }

        return $responseObject;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     *
     * @return void
     */
    protected function hydrate(stdClass $requestObject, stdClass $responseObject): void
    {
        $merchantTransfer = $this
            ->getMerchant($requestObject);

        $branches = $this
            ->getBranches($merchantTransfer);

        $this->hydrateBranches($branches, $responseObject);

        $responseObject->{Response::KEY_AUTH_VALID} = true;
    }

    /**
     * @param iterable $branches
     * @param \stdClass $responseObject
     */
    protected function hydrateBranches(iterable $branches, stdClass $responseObject): void
    {
        $responseObject->{Response::KEY_BRANCHES} = [];

        foreach ($branches as $branch) {
            $responseObject->{Response::KEY_BRANCHES}[] = $this
                ->hydrateBranch($branch);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\BranchTransfer $branchTransfer
     * @return \stdClass
     */
    protected function hydrateBranch(
        BranchTransfer $branchTransfer
    ): stdClass
    {
        $branch = new stdClass();
        $branch->{Response::KEY_BRANCHES_ID} = $branchTransfer->getIdBranch();
        $branch->{Response::KEY_BRANCHES_NAME} = $branchTransfer->getName();

        return $branch;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @return iterable
     */
    protected function getBranches(MerchantTransfer $merchantTransfer): iterable
    {
        return $this
            ->merchantClient
            ->getBranchesForMerchant($merchantTransfer);
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function getMerchant(stdClass $requestObject): MerchantTransfer
    {
        return $this
            ->merchantClient
            ->getMerchantByMerchantPin($requestObject->{Request::KEY_MERCHANT_PIN});
    }
}
