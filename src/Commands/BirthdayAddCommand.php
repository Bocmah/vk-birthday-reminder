<?php

namespace VkBirthdayReminder\Commands;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use VkBirthdayReminder\Helpers\{UserRetriever, MessageSender};
use Symfony\Component\Validator\Validation;
use VkBirthdayReminder\Validator\Constraints as CustomConstraints;
use Symfony\Component\Validator\Constraints;

class BirthdayAddCommand implements CommandInterface
{
    /**
     * VK message object
     * @var
     */
    protected $msg;

    /**
     * @var UserRetriever
     */
    protected $userRetriever;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * BirthdayAddCommand constructor.
     * @param $msg
     * @param UserRetriever $userRetriever
     * @param MessageSender $messageSender
     */
    public function __construct($msg, UserRetriever $userRetriever, MessageSender $messageSender)
    {
        $this->msg = $msg;
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
    }

    /**
     * Execute the command.
     */
    public function execute()
    {
        $data = [];
        $senderId = $this->msg->from_id;
        $messageArr = explode(" ", $this->msg->text);
        $userId = $messageArr[1];
        [$day, $month, $year] = explode(".",$messageArr[2]);
        $data['date_of_birth'] = "{$year}-{$month}-{$day}";
        $data['user'] = $this->userRetriever->getUser($userId, true);

        $violations = $this->performValidation($data);

        if (count($violations) !== 0) {
            $errorMessage = $this->composeErrorMessage($violations);

            return $this->messageSender->send($errorMessage, $senderId);
        }

        return $this->messageSender->send("Я нашель.",$senderId);
    }

    /**
     * Validate an array of data from the command.
     *
     * @param array $data
     * @return ConstraintViolationListInterface
     */
    protected function performValidation(array $data): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();
        $constraint = new Constraints\Collection([
            'user' => new CustomConstraints\NoUserError(),
            'date_of_birth' => new Constraints\Date([
                'message' => 'Дата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.'
            ])
        ]);

        return $validator->validate($data, $constraint);
    }

    /**
     * Builds an error message from validation errors.
     *
     * @param ConstraintViolationListInterface $violations
     * @return string
     */
    protected function composeErrorMessage(ConstraintViolationListInterface $violations): string
    {
        $errorMessage = "Обнаружены ошибки:\n";

        foreach ($violations as $violation) {
            $errorMessage .= $violation->getMessage() . "\n";
        }

        return $errorMessage;
    }
}