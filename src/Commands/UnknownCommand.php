<?php

namespace VkBirthdayReminder\Commands;

use VkBirthdayReminder\Helpers\MessageSender;

/**
 * Encapsulates logic related to handling a command unknown to the bot.
 */
class UnknownCommand implements CommandInterface
{
    /**
     * @var
     */
    protected $senderId;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    public function __construct($senderId, MessageSender $messageSender)
    {
        $this->senderId = $senderId;
        $this->messageSender = $messageSender;
    }

    public function execute()
    {
        $this->messageSender->send(
            'Такой команды я не знаю... Наберите help, чтобы увидеть список доступных команд.',
            $this->senderId
        );
    }
}