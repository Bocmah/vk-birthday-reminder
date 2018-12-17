<?php

namespace VkBirthdayReminder\Commands;

use Doctrine\ORM\EntityManager;
use VkBirthdayReminder\Helpers;

/**
 * Factory that is responsible for creation of Command objects.
 */
class CommandFactory
{
    /**
     * @param $msg
     * @param Helpers\UserRetriever $userRetriever
     * @param Helpers\MessageSender $messageSender
     * @param EntityManager $entityManager
     *
     * @return BirthdayAddCommand
     */
    public function createBirthDayAddCommand(
        $msg,
        Helpers\UserRetriever $userRetriever,
        Helpers\MessageSender $messageSender,
        EntityManager $entityManager
    ): BirthdayAddCommand {
        return new BirthdayAddCommand(
            $msg,
            $userRetriever,
            $messageSender,
            $entityManager
        );
    }

    /**
     * @param int $observerId
     * @param Helpers\MessageSender $messageSender
     * @param EntityManager $entityManager
     *
     * @return ListCommand
     */
    public function createListCommand(
        int $observerId,
        Helpers\MessageSender $messageSender,
        EntityManager $entityManager
    ): ListCommand {
        return new ListCommand(
          $observerId,
          $messageSender,
          $entityManager
        );
    }

    /**
     * @return UpdateCommand
     */
    public function createUpdateCommand(): UpdateCommand
    {
        return new UpdateCommand();
    }

    /**
     * @param $msg
     * @param Helpers\MessageSender $messageSender
     * 
     * @return UnknownCommand
     */
    public function createUnknownCommand($msg, Helpers\MessageSender $messageSender): UnknownCommand
    {
        return new UnknownCommand($msg,$messageSender);
    }
}