<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Helpers\MessageSender;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

class UpdateCommand implements CommandInterface
{
    /**
     * VK message object
     */
    protected $msg;

    /**
     * @var MessageSender
     */
    private $messageSender;

    /**
     * @var EntityManager
     */
    private $entityManager;

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

        $observeeData = $this->getObserveeData();
        $observeeData['observer_id'] = $observer->getId();
        $errors = $this->performValidation($observeeData);
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
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findOneBy(
            [
                'vkId' => $senderId
            ]
        );
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
        $observeeData['user_vk_id'] = $userVkId;

        return $observeeData;
    }

    /**
     * @param array $observeeData
     * @return array
     */
    protected function performValidation(array $observeeData): array
    {
        $errors = [];
        $observee = $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observee')->findOneBy(
            [
                'observer' => $observeeData['observer_id'],
                'vkId' => $observeeData['user_vk_id']
            ]
        );

        if (!$observee) {
            array_push($errors, "Пользователь с id {$observeeData['user_vk_id']} не найден в вашем списке.");
        }

        $dateConstraint = new Constraints\Date([
            'message' => 'Дата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996.'
        ]);
        $validator = Validation::createValidator();
        $dateViolations = $validator->validate($observeeData['date_of_birth'], $dateConstraint);

        if (count($dateViolations) !== 0) {
            array_push($errors, $dateViolations[0]->getMessage());
        }

        return $errors;
    }
}