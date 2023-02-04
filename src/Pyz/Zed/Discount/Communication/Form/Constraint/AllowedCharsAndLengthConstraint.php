<?php
/**
 * Durst - project - AllowedCharsAndLengthConstraint.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.03.21
 * Time: 18:10
 */

namespace Pyz\Zed\Discount\Communication\Form\Constraint;


use Symfony\Component\Validator\Constraint;

class AllowedCharsAndLengthConstraint extends Constraint
{
    public $message = 'Der Gutschein-Code({{ string }}) darf nur aus Großbuchstaben, Zahlen und Bindestriche bestehen. Außerdem muss der Code aus mindestens 6 Zeichen bestehen.';
}
