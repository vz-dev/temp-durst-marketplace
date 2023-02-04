<?php

namespace Pyz\Zed\MerchantManagement\Communication\Form\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CurrentPasswordValidator extends ConstraintValidator
{

    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     *
     * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CurrentPassword) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Password');
        }

        if (!$this->isProvidedPasswordEqualsToPersisted($value, $constraint)) {
            $this->buildViolation($constraint->getMessage())
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }

    /**
     * @param $password
     * @param CurrentPassword $constraint
     * @return bool
     */
    protected function isProvidedPasswordEqualsToPersisted($password, CurrentPassword $constraint)
    {
        $merchantTransfer = $constraint->getFacadeMerchant()->getCurrentMerchant();

        return $constraint->getFacadeMerchant()->isValidPassword($password, $merchantTransfer->getPassword());
    }

}
