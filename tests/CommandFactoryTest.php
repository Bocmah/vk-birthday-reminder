<?php

namespace VkBirthdayReminder\Tests;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use VkBirthdayReminder\Commands\BirthdayAddCommand;
use VkBirthdayReminder\Commands\CommandFactory;
use VkBirthdayReminder\Commands\DeleteCommand;
use VkBirthdayReminder\Commands\HelpCommand;
use VkBirthdayReminder\Commands\ListCommand;
use VkBirthdayReminder\Commands\NotifyCommand;
use VkBirthdayReminder\Commands\UnknownCommand;
use VkBirthdayReminder\Commands\UpdateCommand;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Helpers\UserRetriever;

class CommandFactoryTest extends TestCase
{
    public function commandsProvider()
    {
        $msg = new \stdClass();
        $userRetriever = $this->createMock(UserRetriever::class);
        $messageSender = $this->createMock(MessageSender::class);
        $entityManager = $this->createMock(EntityManager::class);
        $observeeDataRetriever = $this->createMock(ObserveeDataRetriever::class);
        $observerId = 5;
        $senderId = 7;

        return [
           ['createBirthDayAddCommand', [$msg, $userRetriever, $messageSender, $entityManager, $observeeDataRetriever], BirthdayAddCommand::class],
           ['createListCommand', [$observerId, $messageSender, $entityManager], ListCommand::class],
           ['createUpdateCommand', [$msg, $messageSender, $entityManager, $observeeDataRetriever], UpdateCommand::class],
           ['createDeleteCommand', [$msg, $messageSender, $entityManager, $observeeDataRetriever], DeleteCommand::class],
           ['createHelpCommand', [$senderId, $messageSender], HelpCommand::class],
           ['createNotifyCommand', [$senderId, $messageSender, $entityManager], NotifyCommand::class],
           ['createUnknownCommand', [$senderId, $messageSender], UnknownCommand::class]
        ];
    }

    /**
     * @dataProvider commandsProvider
     * @test
     * @param $methodName
     * @param $args
     * @param $outputClassName
     */
    public function createsCommand($methodName, $args, $outputClassName)
    {
        $commandFactory = new CommandFactory();
        $command = call_user_func_array(array($commandFactory, $methodName), $args);

        $this->assertInstanceOf($outputClassName, $command);
    }
}