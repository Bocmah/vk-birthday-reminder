<?php

namespace VkBirthdayReminder\Commands;

use VkBirthdayReminder\Commands;
use VkBirthdayReminder\Helpers\MessageSender;

class HelpCommand implements CommandInterface
{
    protected $commandHelpMessages = [
        Commands::ADD => 'Добавляет юзера в список отслеживаемых. Формат: "add id DD.MM.YYYY" (без кавычек), где id - id юзера VK. Может быть как числовым представлением, так и именем страницы, которое задал пользователь. DD.MM.YYYY - день рождения (полное числовое представление дня, месяца и года, например, 13.10.1996).',
        Commands::UPDATE => 'Обновляет день рождения юзера, который находится в вашем списке отслеживаемых. Формат: "update id DD.MM.YYYY" (без кавычек).',
        Commands::DELETE => 'Удаляет юзера из вашего списка отслеживаемых. Формат: "delete id" (без кавычек)',
        Commands::NOTIFY => 'Включает/выключает уведомления от бота в случае, если ближайших дней рождений нет. Если включено, бот пришлет вам сообщение, что в ближайшее время дней рождений не предвидится. Если отключено, то бот будет присылать уведомления только в случае наличия ближайших дней рождений.',
        Commands::LIST => 'Показывает ваш список отслеживаемых юзеров.',
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
        $message = "Список доступных команд:\n\n";

        foreach ($this->commandHelpMessages as $command => $commandHelpMessage) {
            $message .= $command . ": \n" . $commandHelpMessage . "\n\n";
        }

        return $this->messageSender->send($message, $this->senderId);
    }
}