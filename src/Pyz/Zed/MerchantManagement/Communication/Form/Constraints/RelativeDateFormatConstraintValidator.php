<?php
/**
 * Durst - project - RelativeDateFormatConstraintValidatortValidator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 2020-02-11
 * Time: 20:46
 */

namespace Pyz\Zed\MerchantManagement\Communication\Form\Constraints;


use DateTime;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RelativeDateFormatConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid. We check if the value is a valid php date modifier string,
     * if not throw a errror
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate(
        $value,
        Constraint $constraint
    )
    {
        if($value !== null)
        {
            try {
                $date = new DateTime();
                $date->modify($value);
            } catch (Exception $e) {
                $this
                    ->context
                    ->buildViolation($constraint->getMessage())
                    ->addViolation();
            }
        }
    }
}
