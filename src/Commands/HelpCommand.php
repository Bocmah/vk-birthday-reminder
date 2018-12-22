<?php

namespace VkBirthdayReminder\Commands;

use VkBirthdayReminder\Commands;
use VkBirthdayReminder\Helpers\MessageSender;

class HelpCommand implements CommandInterface
{
    protected $commandHelpMessages = [
        Commands::ADD => 'Добавляет юзера в список отслеживаемых. Формат: "add id DD.MM.YYYY" (без кавычек), где id - 
        id юзера VK. Может быть как числовым представлением, так и именем страницы, которое задал пользователь. 
        DD.MM.YYYY - день рождения (полное числовое представление дня, месяца и года, например, 13.10.1996).',
        Commands::UPDATE => 'Обновляет день рождения юзера, который находится в вашем списке отслеживаемых. Формат: 
        "update id DD.MM.YYYY" (без кавычек).',
        Commands::LIST => 'Показывает ваш список отслеживаемых юзеров.'
    ];

    /**
     * @var MessageSender
     */
    protected $messageSender;

    protected $senderId;

    public function __construct($senderId, MessageSender $messageSender)
    {
        $this->senderId = $senderId;
        $this->messageSender = $messageSender;
    }

    public function execute()
    {
        $message = '';

        foreach ($this->commandHelpMessages as $command => $commandHelpMessage) {
            $message .= $command . ": " . $commandHelpMessage . "\n\n";
        }

        return $this->messageSender->send($message, $this->senderId);
    }
}