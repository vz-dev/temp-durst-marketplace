<?php
/**
 * Durst - project - MerchantHydrator.php.
 *
 * Initial version by:
 * User: Mathias Bicker, <mathias.bicker@durst.shop>
 * Date: 09.05.18
 * Time: 14:22
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Voucher;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Generated\Shared\Transfer\BranchTransfer;
use Generated\Shared\Transfer\DeliveryAreaTransfer;
use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\VoucherKeyRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\VoucherKeyResponseInterface as Response;

class MerchantHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * MerchantHydrator constructor.
     * @param AppRestApiClientInterface $client
     */
    public function __construct(AppRestApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     * @return mixed|void
     * @throws \Spryker\Shared\ZedRequest\Client\Exception\RequestException
     */
    public function hydrate(\stdClass $requestObject, \stdClass $responseObject, string $version = 'v1')
    {
        $requestTransfer = (new AppApiRequestTransfer())
            ->setCode($requestObject->{Request::KEY_VOUCHER_CODE});

        $responseTransfer = $this
            ->client
            ->getBranchByCode($requestTransfer);

        $branch = $responseTransfer->getBranch();

        if($branch === null){
            $responseObject->{Response::KEY_VOUCHER_VALID} = false;

            return;
        }

        $merchantObject = $this->hydrateMerchant($branch, $responseObject);

        $responseObject->{Response::KEY_VOUCHER_VALID} = true;

        $requestTransfer->setIdBranch($branch->getIdBranch());

        $responseTransfer = $this
            ->client
            ->getDeliveryAreasByIdBranch($requestTransfer);

        $this->hydrateDeliveryAreas($merchantObject,  $responseTransfer->getDeliveryAreas());
    }

    /**
     * @param BranchTransfer $branchTransfer
     * @param \stdClass $responseObject
     * @return \stdClass
     */
    protected function hydrateMerchant(BranchTransfer $branchTransfer, \stdClass $responseObject) : \stdClass
    {
        $merchantObject = new \stdClass();

        $merchantObject->{Response::KEY_MERCHANT_ID} = $branchTransfer->getIdBranch();
        $merchantObject->{Response::KEY_MERCHANT_NAME} = $branchTransfer->getName();
        $merchantObject->{Response::KEY_MERCHANT_CITY} = $branchTransfer->getCity();
        $responseObject->{Response::KEY_MERCHANT} = $merchantObject;

        return $merchantObject;
    }

    /**
     * @param \stdClass $merchantObject
     * @param DeliveryAreaTransfer[]|\ArrayObject $deliverAreaTransfers
     */
    protected function hydrateDeliveryAreas(\stdClass $merchantObject, $deliverAreaTransfers)
    {
        $merchantObject->{Response::KEY_MERCHANT_DELIVERY_AREAS} = [];
        foreach ($deliverAreaTransfers as $deliverAreaTransfer) {
            $merchantObject->{Response::KEY_MERCHANT_DELIVERY_AREAS}[] = $deliverAreaTransfer->getZip();
        }
    }
}
