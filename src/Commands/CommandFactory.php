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
     * @param Helpers\ObserveeDataRetriever $observeeDataRetriever
     *
     * @return BirthdayAddCommand
     */
    public function createBirthDayAddCommand(
        $msg,
        Helpers\UserRetriever $userRetriever,
        Helpers\MessageSender $messageSender,
        EntityManager $entityManager,
        Helpers\ObserveeDataRetriever $observeeDataRetriever
    ): BirthdayAddCommand {
        return new BirthdayAddCommand(
            $msg,
            $userRetriever,
            $messageSender,
            $entityManager,
            $observeeDataRetriever
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
     * @param $msg
     * @param Helpers\MessageSender $messageSender
     * @param EntityManager $entityManager
     * @param Helpers\ObserveeDataRetriever $observeeDataRetriever
     *
     * @return UpdateCommand
     */
    public function createUpdateCommand(
        $msg,
        Helpers\MessageSender $messageSender,
        EntityManager $entityManager,
        Helpers\ObserveeDataRetriever $observeeDataRetriever
    ): UpdateCommand {
        return new UpdateCommand(
            $msg,
            $messageSender,
            $entityManager,
            $observeeDataRetriever
        );
    }

    /**
     * @param $msg
     * @param Helpers\MessageSender $messageSender
     * @param EntityManager $entityManager
     * @param Helpers\ObserveeDataRetriever $observeeDataRetriever
     *
     * @return DeleteCommand
     */
    public function createDeleteCommand(
        $msg,
        Helpers\MessageSender $messageSender,
        EntityManager $entityManager,
        Helpers\ObserveeDataRetriever $observeeDataRetriever
    ): DeleteCommand {
        return new DeleteCommand(
            $msg,
            $messageSender,
            $entityManager,
            $observeeDataRetriever
        );
    }

    /**
     * @param $senderId
     * @param Helpers\MessageSender $messageSender
     *
     * @return HelpCommand
     */
    public function createHelpCommand(
        $senderId,
        Helpers\MessageSender $messageSender
    ): HelpCommand {
        return new HelpCommand(
          $senderId,
          $messageSender
        );
    }

    /**
     * @param $senderId
     * @param Helpers\MessageSender $messageSender
     * @param EntityManager $entityManager
     *
     * @return NotifyCommand
     */
    public function createNotifyCommand(
        $senderId,
        Helpers\MessageSender $messageSender,
        EntityManager $entityManager
    ): NotifyCommand {
        return new NotifyCommand(
            $senderId,
            $messageSender,
            $entityManager
        );
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