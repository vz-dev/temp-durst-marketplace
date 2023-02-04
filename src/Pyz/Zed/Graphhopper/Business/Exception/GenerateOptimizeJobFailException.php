<?php
/**
 * Durst - project - GenerateOptimizeJobFailException.php.
 *
 * Initial version by:
 * User: Oliver Gail, <oliver.gail@durst.shop>
 * Date: 04.12.19
 * Time: 11:24
 */

namespace Pyz\Zed\Graphhopper\Business\Exception;


class GenerateOptimizeJobFailException extends OptimizeException
{
    public const MESSAGE = 'Bei der Anlage eines Jobs ist ein Fehler aufgetreten.';
}
