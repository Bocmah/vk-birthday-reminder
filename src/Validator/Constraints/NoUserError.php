<?php

namespace VkBirthdayReminder\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NoUserError extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Юзер с таким id не найден.';
}