<?php

namespace VkBirthdayReminder\Handlers;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Commands\CommandInterface;
use VkBirthdayReminder\Commands\Invoker;
use VkBirthdayReminder\Helpers\UserRetriever;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\CommandParser;
use VkBirthdayReminder\Helpers\ObserveeDataRetriever;
use VkBirthdayReminder\Commands\CommandFactory;
use VkBirthdayReminder\Commands;

/**
 * Encapsulates the logic related to handling a message received by the bot.
 */
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
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ObserveeDataRetriever
     */
    protected $observeeDataRetriever;

    /**
     * VK message object
     *
     * @var
     */
    protected $msg;

    /**
     * MessageHandler constructor.
     * @param UserRetriever $userRetriever
     * @param MessageSender $messageSender
     * @param CommandParser $commandParser
     * @param CommandFactory $commandFactory
     * @param EntityManager $entityManager
     * @param ObserveeDataRetriever $observeeDataRetriever
     */
    public function __construct(
        UserRetriever $userRetriever,
        MessageSender $messageSender,
        CommandParser $commandParser,
        CommandFactory $commandFactory,
        EntityManager $entityManager,
        ObserveeDataRetriever $observeeDataRetriever
    ) {
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
        $this->commandParser = $commandParser;
        $this->commandFactory = $commandFactory;
        $this->entityManager = $entityManager;
        $this->observeeDataRetriever = $observeeDataRetriever;
    }

    /**
     * @inheritdoc
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
            case Commands::ADD:
                return $this->commandFactory->createBirthDayAddCommand(
                    $this->msg,
                    $this->userRetriever,
                    $this->messageSender,
                    $this->entityManager,
                    $this->observeeDataRetriever
                );
            case Commands::LIST:
                return $this->commandFactory->createListCommand(
                  $this->msg->from_id,
                  $this->messageSender,
                  $this->entityManager
                );
            case Commands::UPDATE:
                return $this->commandFactory->createUpdateCommand(
                    $this->msg,
                    $this->messageSender,
                    $this->entityManager,
                    $this->observeeDataRetriever
                );
            case Commands::DELETE:
                return $this->commandFactory->createDeleteCommand(
                    $this->msg,
                    $this->messageSender,
                    $this->entityManager,
                    $this->observeeDataRetriever
                );
            case Commands::HELP:
                return $this->commandFactory->createHelpCommand(
                  $this->msg->from_id,
                  $this->messageSender
                );
            case Commands::NOTIFY:
                return $this->commandFactory->createNotifyCommand(
                  $this->msg->from_id,
                  $this->messageSender,
                  $this->entityManager
                );
            default:
                return $this->commandFactory->createUnknownCommand(
                    $this->msg->from_id,
                    $this->messageSender
                );
        }
    }
}