<?php
/**
 * Created by PhpStorm.
 * User: bocmah
 * Date: 20/01/2019
 * Time: 21:18
 */

namespace VkBirthdayReminder\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\UnknownCommand;
use VkBirthdayReminder\Helpers\MessageSender;

class UnknownCommandTest extends TestCase
{
    /**
     * @test
     */
    public function correctlyRespondsToUnknownCommand()
    {
        $senderId = 1;
        /** @var MessageSender|MockObject $messageSenderStub */
        $messageSenderStub = $this->createMock(MessageSender::class);
        $messageSenderStub->expects($this->once())
                          ->method('send')
                          ->with(
                              'Такой команды я не знаю... Наберите help, чтобы увидеть список доступных команд.',
                              $senderId
                          );


        $unknownCommand = new UnknownCommand(
            $senderId,
            $messageSenderStub
        );

        $unknownCommand->execute();
    }
}