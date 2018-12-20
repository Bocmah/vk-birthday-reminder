<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Helpers\MessageSender;

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
        $userId = $messageParts[1];
        [$day, $month, $year] = explode(".",$messageParts[2]);
        $observeeData['date_of_birth'] = "{$year}-{$month}-{$day}";
        $observeeData['user_id'] = $userId;

        return $observeeData;
    }
}