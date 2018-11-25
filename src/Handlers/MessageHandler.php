<?php

namespace VkBirthdayReminder\Handlers;

use VkBirthdayReminder\Commands\CommandInterface;
use VkBirthdayReminder\Commands\Invoker;
use VkBirthdayReminder\Helpers\UserRetriever;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\CommandParser;
use VkBirthdayReminder\Commands\CommandFactory;

class MessageHandler implements MessageHandlerInterface
{
    /**
     * @var UserRetriever
     */
    protected $userRetriever;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * @var CommandParser
     */
    protected $commandParser;

    /**
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * VK message object
     *
     * @var
     */
    protected $msg;

    public function __construct(
        UserRetriever $userRetriever,
        MessageSender $messageSender,
        CommandParser $commandParser,
        CommandFactory $commandFactory
    ) {
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
        $this->commandParser = $commandParser;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Handle message sent to the bot
     *
     * @param $msg
     */
    public function handle($msg)
    {
        $this->msg = $msg;
        $command = $this->commandParser->parse($msg->text);
        $commandClass = $this->createCommandClass($command);
        $commandInvoker = new Invoker();

        $commandInvoker->setCommand($commandClass);
        $commandInvoker->run();
    }

    /**
     * Create a Command class using CommandFactory based on provided $command string.
     *
     * @param string $command
     * @return CommandInterface
     */
    protected function createCommandClass(string $command): CommandInterface
    {
        switch ($command) {
            case 'birthdayAdd':
                return $this->commandFactory->createBirthDayAddCommand(
                    $this->msg,
                    $this->userRetriever,
                    $this->messageSender
                );
            default:
                return $this->commandFactory->createUnknownCommand(
                    $this->msg,
                    $this->messageSender
                );
        }
    }
}