<?php
/**
 * Durst - project - GeneratedCharLengthConstraintValidator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.03.21
 * Time: 20:10
 */

namespace Pyz\Zed\Discount\Communication\Form\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GeneratedCharLengthConstraintValidator extends ConstraintValidator
{
    protected const CODE_PLACEHOLDER = '[code]';
    protected const CODE_LENGTH = 6;

    public function validate($value, Constraint $constraint)
    {
        // skip validation if field is empty
        if($value != '' && $value !== null) {
            $codeNoPlaceHolder = str_replace(self::CODE_PLACEHOLDER, '', $this->context->getRoot()->getData()->getCustomCode());
            $codeLength = strlen($codeNoPlaceHolder) + $value;

            if ($codeLength < self::CODE_LENGTH) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $codeLength)
                    ->addViolation();
            }
        }
    }
}
