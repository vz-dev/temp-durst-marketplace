<?php
/**
 * Durst - project - GeneratedCharLengthConstraint.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.03.21
 * Time: 20:10
 */

namespace Pyz\Zed\Discount\Communication\Form\Constraint;


use Symfony\Component\Validator\Constraint;

class GeneratedCharLengthConstraint extends Constraint
{
    public $message = 'Ein Gutschein-Code muss mindestens aus 6 Zeichen(inkl. Custom-Code) bestehen, aktuell sind es nur {{ string }}';
}
