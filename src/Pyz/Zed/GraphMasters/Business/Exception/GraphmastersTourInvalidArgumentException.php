<?php

namespace Pyz\Zed\GraphMasters\Business\Exception;

use RuntimeException;

class GraphmastersTourInvalidArgumentException extends RuntimeException
{
    public const NOT_BELONGING_TO_CURRENT_BRANCH = 'The Graphmasters tour does not belong to the current branch.';
}
