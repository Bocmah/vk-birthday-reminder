<?php

namespace VkBirthdayReminder\Handlers;

use VkBirthdayReminder\Helpers\UserRetriever;
use VkBirthdayReminder\Helpers\MessageSender;
use VkBirthdayReminder\CommandPatterns;

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

    public function __construct(UserRetriever $userRetriever, MessageSender $messageSender)
    {
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
    }

    /**
     * Handle message sent to the bot
     *
     * @param $msg
     */
    public function handle($msg)
    {
        $this->parseCommand($msg);
    }

    /**
     * Invoke command-specific method
     *
     * @param $msg
     */
    protected function parseCommand($msg)
    {
        $command = $msg->text;

        if (preg_match(CommandPatterns::BIRTHDAY_ADD, $command)) {
            $this->handleBirthdayAdd($msg);
        } else {
            $this->handleUnknownCommand($msg);
        }
    }


    /**
     * Handle BIRTHDAY_ADD command
     *
     * @param $msg
     */
    protected function handleBirthdayAdd($msg)
    {
        $senderId = $msg->from_id;
        $messageArr = explode(" ", $msg->text);
        $userId = $messageArr[1];
        $birthday = explode(".",$messageArr[2]);
        [$day, $month, $year] = $birthday;
        $user = $this->userRetriever->getUser($userId, true);

        if (array_key_exists("error", $user)) {
            $this->messageSender->send("Чет не могу найти такой айдишник. Проверь.", $senderId);

            return;
        } else if (!checkdate($month, $day, $year)) {
            $this->messageSender->send(
                "Дата неправильная. Она должна быть в формате DD.MM.YYYY. Например: 13.10.1996",
                $senderId
            );

            return;
        } else {
            $this->proceedBirthdayAdd($msg);
        }
    }


    /**
     * Handle a validated BIRTHDAY_ADD command
     *
     * @param $msg
     */
    protected function proceedBirthdayAdd($msg)
    {
        // TODO implement method
    }

    /**
     * @param $msg
     */
    protected function handleUnknownCommand($msg)
    {
        $this->messageSender->send("Не знаю таких команд, братан", $msg->from_id);
    }
}