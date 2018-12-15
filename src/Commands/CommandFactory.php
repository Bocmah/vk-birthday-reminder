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
     * @param $msg
     * @param Helpers\MessageSender $messageSender
     * @return UnknownCommand
     */
    public function createUnknownCommand($msg, Helpers\MessageSender $messageSender): UnknownCommand
    {
        return new UnknownCommand($msg,$messageSender);
    }
}