<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Entities\Observee;
use VkBirthdayReminder\Helpers\MessageSender;

class ListCommand implements CommandInterface
{
    /**
     * @var int
     */
    protected $observerVkId;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(int $observerVkId, MessageSender $messageSender, EntityManager $entityManager)
    {
        $this->observerVkId = $observerVkId;
        $this->messageSender = $messageSender;
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        $observer = $this->getObserverIfExists($this->observerVkId);
        $message = '';
        $observeeTemplate = "*id%d (%s %s) - %s\n";

        if (!$observer) {
            $message = 'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.';

            return $this->messageSender->send(
                $message,
                $this->observerVkId
            );
        }

        /** @var Observee $observee */
        foreach ($observer->getObservees() as $observee) {
            $message .= sprintf(
                $observeeTemplate,
                $observee->getVkId(),
                $observee->getFirstName(),
                $observee->getLastName(),
                $observee->getBirthday()->format('d.m.Y')
            );
        }

        if (!$message) {
            $message = 'Вы еще не отслеживаете ДР ни одного юзера.';
        }

        return $this->messageSender->send($message, $this->observerVkId);
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
}