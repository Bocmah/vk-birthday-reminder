<?php

namespace VkBirthdayReminder\Handlers;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Commands\CommandInterface;
use VkBirthdayReminder\Commands\Invoker;
use VkBirthdayReminder\Helpers\UserRetriever;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\Helpers\CommandParser;
use VkBirthdayReminder\Commands\CommandFactory;

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
     */
    public function __construct(
        UserRetriever $userRetriever,
        MessageSender $messageSender,
        CommandParser $commandParser,
        CommandFactory $commandFactory,
        EntityManager $entityManager
    ) {
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
        $this->commandParser = $commandParser;
        $this->commandFactory = $commandFactory;
        $this->entityManager = $entityManager;
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
            case 'birthdayAdd':
                return $this->commandFactory->createBirthDayAddCommand(
                    $this->msg,
                    $this->userRetriever,
                    $this->messageSender,
                    $this->entityManager
                );
            case 'list':
                return $this->commandFactory->createListCommand(
                  $this->msg->from_id,
                  $this->messageSender,
                  $this->entityManager
                );
            default:
                return $this->commandFactory->createUnknownCommand(
                    $this->msg,
                    $this->messageSender
                );
        }
    }
}