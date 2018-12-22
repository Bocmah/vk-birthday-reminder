<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Helpers\MessageSender;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use VkBirthdayReminder\Traits\ErrorMessageTrait;

class UpdateCommand implements CommandInterface
{
    use ErrorMessageTrait;

    /**
     * VK message object
     */
    protected $msg;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct($msg, MessageSender $messageSender, EntityManager $entityManager)
    {
        $this->msg = $msg;
        $this->messageSender = $messageSender;
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        $senderId = $this->msg->from_id;
        $observer = $this->getObserverIfExists($senderId);

        if (!$observer) {
            return $this->messageSender->send(
                'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                $senderId
            );
        }

        ['date_of_birth' => $dateOfBirth, 'observee_vk_id' => $observeeVkId] = $this->getObserveeData();

        $observee = $this->getObserveeIfExists($observer->getId(),$observeeVkId);

        $errors = $this->performValidation([
            'date_of_birth' => $dateOfBirth,
            'observee' => $observee
        ]);

        if (count($errors) !== 0) {
            $errorMessage = $this->composeErrorMessage($errors);

            return $this->messageSender->send($errorMessage, $senderId);
        }
    }

    /**
     * Return an observer object if $senderId already exists in the observers table.
     * Null otherwise.
     *
     * @param int $senderId
     * @return null|object
     */
    protected function getObserverIfExists(int $senderId)
    {
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findOneBy([
                'vkId' => $senderId
        ]);
    }

    /**
     * Return an observee object if it exists in the observer's list.
     * Null otherwise.
     *
     * @param int $observerId
     * @param int $observeeVkId
     * @return null|object
     */
    protected function getObserveeIfExists(int $observerId, int $observeeVkId)
    {
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observee')->findOneBy([
                'observer' => $observerId,
                'vkId' => $observeeVkId
        ]);
    }

    /**
     * Get observee data from the message.
     *
     * @return array
     */
    protected function getObserveeData(): array
    {
        $observeeData = [];
        $messageParts = explode(" ", $this->msg->text);
        $userVkId = $messageParts[1];
        [$day, $month, $year] = explode(".",$messageParts[2]);
        $observeeData['date_of_birth'] = "{$year}-{$month}-{$day}";
        $observeeData['observee_vk_id'] = $userVkId;

        return $observeeData;
    }

    /**
     * Validates observee data and returns an array of errors.
     *
     * @param array $observeeData
     * @return ConstraintViolationListInterface
     */
    protected function performValidation(array $observeeData): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();
        $constraint = new Constraints\Collection([
            'observee' => new Constraints\NotNull([
                'message' => 'Пользователь с таким id не найден в вашем списке отслеживания.'
            ]),
            'date_of_birth' => new Constraints\Date([
                'message' => 'Дата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.'
            ])
        ]);

        return $validator->validate($observeeData, $constraint);
    }
}