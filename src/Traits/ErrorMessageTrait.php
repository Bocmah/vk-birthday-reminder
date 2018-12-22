<?php

namespace VkBirthdayReminder\Traits;

use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ErrorMessageTrait
{
    /**
     * Builds an error message from validation errors.
     *
     * @param ConstraintViolationListInterface $violations
     * @return string
     */
    protected function composeErrorMessage(ConstraintViolationListInterface $violations): string
    {
        $errorMessage = "Обнаружены ошибки:\n\n";

        foreach ($violations as $violation) {
            $errorMessage .= $violation->getMessage() . "\n";
        }

        return $errorMessage;
    }
}