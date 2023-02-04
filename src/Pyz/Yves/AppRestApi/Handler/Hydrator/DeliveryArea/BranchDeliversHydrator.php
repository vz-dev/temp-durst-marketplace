<?php
/**
 * Durst - project - BranchDeliversHydrator.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 16.03.21
 * Time: 10:34
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\DeliveryArea;


use Generated\Shared\Transfer\AppApiRequestTransfer;
use Pyz\Client\DeliveryArea\DeliveryAreaClientInterface;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\DeliveryAreaRequestInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Response\DeliveryAreaResponseInterface;
use stdClass;

class BranchDeliversHydrator implements HydratorInterface
{
    /**
     * @var \Pyz\Client\DeliveryArea\DeliveryAreaClientInterface
     */
    protected $client;

    /**
     * BranchDeliversHydrator constructor.
     * @param \Pyz\Client\DeliveryArea\DeliveryAreaClientInterface $client
     */
    public function __construct(
        DeliveryAreaClientInterface $client
    )
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     *
     * @param \stdClass $requestObject
     * @param \stdClass $responseObject
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version = 'v1')
    {
        $requestTransfer = $this
            ->createRequestTransfer($requestObject);

        $response = $this
            ->client
            ->getBranchDeliversZipCode(
                $requestTransfer
            );

        $responseObject
            ->{DeliveryAreaResponseInterface::ZIP_VALID} = $response->getZipValid();
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getZipCode(stdClass $requestObject): string
    {
        return $requestObject
            ->{DeliveryAreaRequestInterface::KEY_ZIP_CODE};
    }

    /**
     * @param \stdClass $requestObject
     * @return string
     */
    protected function getBranchCode(stdClass $requestObject): string
    {
        return $requestObject
           ->{DeliveryAreaRequestInterface::KEY_BRANCH_CODE};
    }

    /**
     * @param \stdClass $requestObject
     * @return \Generated\Shared\Transfer\AppApiRequestTransfer
     */
    protected function createRequestTransfer(stdClass $requestObject): AppApiRequestTransfer
    {
        $zipCode = $this
            ->getZipCode(
                $requestObject
            );
        $branchCode = $this
            ->getBranchCode(
                $requestObject
            );

        return (new AppApiRequestTransfer())
            ->setCode($branchCode)
            ->setZipCode($zipCode);
    }
}
