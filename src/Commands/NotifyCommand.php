<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Helpers\MessageSender;

class NotifyCommand implements CommandInterface
{
    /**
     * VK id of a person that sent the message to the bot.
     */
    protected $senderId;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * NotifyCommand constructor.
     *
     * @param $senderId
     * @param MessageSender $messageSender
     * @param EntityManager $entityManager
     */
    public function __construct($senderId, MessageSender $messageSender, EntityManager $entityManager)
    {
        $this->senderId = $senderId;
        $this->messageSender = $messageSender;
        $this->entityManager = $entityManager;
    }

    public function execute()
    {
        $observer = $this->getObserverIfExists($this->senderId);

        if (!$observer) {
            return $this->messageSender->send(
                'Вы не найдены в базе. Вероятно вы ни разу не вызывали add команду.',
                $this->senderId
            );
        }

        $newNotifiableState = !$observer->getIsNotifiable();

        $observer->setIsNotifiable(
            $newNotifiableState
        );

        $this->entityManager->flush();

        if ($newNotifiableState) {
            return $this->messageSender->send(
                'Теперь бот будет присылать вам уведомления, даже если ни у кого из ваших отслеживаемых людей нет ближайших дней рождений.',
                $this->senderId
            );
        } else {
            return $this->messageSender->send(
              'Теперь бот будет присылать вам уведомления, только если у кого-то из ваших друзей скоро день рождения.',
              $this->senderId
            );
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
        return $this->entityManager->getRepository('VkBirthdayReminder\Entities\Observer')->findOneBy(
            [
                'vkId' => $senderId
            ]
        );
    }
}