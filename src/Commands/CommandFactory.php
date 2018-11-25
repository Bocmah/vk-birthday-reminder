<?php

namespace VkBirthdayReminder\Commands;

use VkBirthdayReminder\Helpers;

class CommandFactory
{
    /**
     * @param $msg
     * @param Helpers\UserRetriever $userRetriever
     * @param Helpers\MessageSender $messageSender
     * @return BirthdayAddCommand
     */
    public function createBirthDayAddCommand(
        $msg,
        Helpers\UserRetriever $userRetriever,
        Helpers\MessageSender $messageSender
    ): BirthdayAddCommand {
        return new BirthdayAddCommand($msg, $userRetriever, $messageSender);
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