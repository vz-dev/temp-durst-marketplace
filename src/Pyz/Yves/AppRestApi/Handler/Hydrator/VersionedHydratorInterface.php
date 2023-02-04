<?php

namespace Pyz\Yves\AppRestApi\Handler\Hydrator;

use stdClass;

interface VersionedHydratorInterface
{
    /**
     * @param string $version
     * @param stdClass $requestObject
     * @param stdClass $responseObject
     *
     * @return void
     */
    public function hydrate(string $version, stdClass $requestObject, stdClass $responseObject);
}
