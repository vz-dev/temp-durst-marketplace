<?php
/**
 * Durst - project - AllowedCharsAndLengthConstraintValidator.php.
 *
 * Initial version by:
 * User: Ike Simmons, <issac.simmons@durst.shop>
 * Date: 09.03.21
 * Time: 18:12
 */

namespace Pyz\Zed\Discount\Communication\Form\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AllowedCharsAndLengthConstraintValidator extends ConstraintValidator
{
    protected const CODE_PLACEHOLDER = '[code]';
    protected const CODE_LENGTH = 6;

    public function validate($value, Constraint $constraint)
    {
        if($value != '' && $value !== null) {
            $codeNoPlaceHolder = str_replace(self::CODE_PLACEHOLDER, '', $value);
            $codeLength = strlen($codeNoPlaceHolder);

            if(strpos($value, self::CODE_PLACEHOLDER) !== false){
                $codeLength += intval($this->context->getRoot()->getData()->getRandomGeneratedCodeLength());
            }

            if (!preg_match('/^[A-Z0-9-]+$/', $codeNoPlaceHolder) || $codeLength < self::CODE_LENGTH) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ string }}', $value)
                    ->addViolation();
            }
        }
    }
}
