<?php

namespace VkBirthdayReminder\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoUserErrorValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NoUserError) {
            throw new UnexpectedTypeException($constraint, NoUserError::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (array_key_exists('error', $value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}