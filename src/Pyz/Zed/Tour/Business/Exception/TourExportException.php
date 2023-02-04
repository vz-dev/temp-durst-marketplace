<?php
/**
 * Created by PhpStorm.
 * User: olivergail
 * Date: 2018-12-12
 * Time: 10:24
 */

namespace Pyz\Zed\Tour\Business\Exception;

use Exception;

class TourExportException extends Exception
{
    public const ERROR_NO_HTTPS_SCHEMA = 'Das Übertragungsschema ist nicht sicher, kein HTTPS.';
    public const ERROR_CONCRETE_TOUR_EXPORT_NOT_FOUND = 'Der Export mit der Id "%d" einer konkreten Tour wurde nicht gefunden.';
    public const ERROR_CONCRETE_TOUR_NOT_FOUND = 'Die konkrete Tour mit der Id "%d" wurde nicht gefunden.';
    public const ERROR_GRAPHMASTERS_TOUR_NOT_FOUND = 'Die Graphmasters-Tour mit der Id "%d" wurde nicht gefunden.';
}
