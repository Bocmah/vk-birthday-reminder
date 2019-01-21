<?php

namespace VkBirthdayReminder\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands;
use VkBirthdayReminder\Commands\HelpCommand;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Traits\MessageSplitterTrait;

class HelpCommandTest extends TestCase
{
    use MessageSplitterTrait;

    /**
     * @test
     */
    public function sendsComposedHelpMessage()
    {
        $commandHelpMessages = [
            Commands::ADD => 'Добавляет юзера в список отслеживаемых. Формат: "add id DD.MM.YYYY" (без кавычек), где id - id юзера VK. Может быть как числовым представлением, так и именем страницы, которое задал пользователь. DD.MM.YYYY - день рождения (полное числовое представление дня, месяца и года, например, 13.10.1996).',
            Commands::UPDATE => 'Обновляет день рождения юзера, который находится в вашем списке отслеживаемых. Формат: "update id DD.MM.YYYY" (без кавычек).',
            Commands::DELETE => 'Удаляет юзера из вашего списка отслеживаемых. Формат: "delete id" (без кавычек)',
            Commands::NOTIFY => 'Включает/выключает уведомления от бота в случае, если ближайших дней рождений нет. Если включено, бот пришлет вам сообщение, что в ближайшее время дней рождений не предвидится. Если отключено, то бот будет присылать уведомления только в случае наличия ближайших дней рождений.',
            Commands::LIST => 'Показывает ваш список отслеживаемых юзеров.',
        ];

        $message = "Список доступных команд:\n\n";

        foreach ($commandHelpMessages as $command => $helpMessage) {
            $message .= $command . ": \n" . $helpMessage . "\n\n";
        }

        $batches = $this->splitMessage($message, 500);
        $senderId = 1;
        $mappedBatches = array_map(function($val) use ($senderId) {
            return [$val, $senderId];
        }, $batches);
        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->exactly(count($batches)))
                          ->method('send')
                          ->withConsecutive(...$mappedBatches);

        $helpCommand = new HelpCommand(
          $senderId,
          $messageSenderStub
        );

        $helpCommand->execute();
    }
}