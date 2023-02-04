<?php
/**
 * Durst - project - GMSettingsHydrator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 13.01.22
 * Time: 17:55
 */

namespace Pyz\Yves\AppRestApi\Handler\Hydrator\Graphmasters;


use Pyz\Client\AppRestApi\AppRestApiClientInterface;
use Pyz\Yves\AppRestApi\AppRestApiConfig;
use Pyz\Yves\AppRestApi\Handler\Hydrator\HydratorInterface;
use Pyz\Yves\AppRestApi\Handler\Json\Request\GraphmastersSettingsRequestInterface as Request;
use Pyz\Yves\AppRestApi\Handler\Json\Response\GraphmastersSettingsResponseInterface as Response;
use stdClass;

class GMSettingsHydrator implements HydratorInterface
{
    /**
     * @var AppRestApiConfig
     */
    protected $config;

    /**
     * @var AppRestApiClientInterface
     */
    protected $client;

    /**
     * GMSettingsHydrator constructor.
     * @param AppRestApiConfig $config
     * @param AppRestApiClientInterface $client
     */
    public function __construct(
        AppRestApiConfig $config,
        AppRestApiClientInterface $client
    ) {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     * @param string $version
     */
    public function hydrate(stdClass $requestObject, stdClass $responseObject, string $version='v1'): void
    {
        $responseObject
            ->{Response::KEY_SETTINGS} = $this
                ->client
                ->getGMSettings($requestObject->{Request::KEY_ID_BRANCH});

    }
}
