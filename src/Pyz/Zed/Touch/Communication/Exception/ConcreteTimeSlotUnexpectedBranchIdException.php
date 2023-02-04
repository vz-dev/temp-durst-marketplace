<?php

namespace Pyz\Zed\Touch\Communication\Exception;

use RuntimeException;

class ConcreteTimeSlotUnexpectedBranchIdException extends RuntimeException
{
    public const MESSAGE = 'The branch ID of the concrete time slot with the ID %d was expected to be %d (actual: %d)';
}
