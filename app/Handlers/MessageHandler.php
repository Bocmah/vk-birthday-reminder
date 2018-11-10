<?php

namespace VkBirthdayReminder\Handlers;

use VkBirthdayReminder\Helpers\UsersRetriever;
use VkBirthdayReminder\Helpers\MessageSender;

class MessageHandler implements MessageHandlerInterface
{
    /**
     * @var UsersRetriever
     */
    protected $usersRetriever;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    public function __construct(UsersRetriever $usersRetriever, MessageSender $messageSender)
    {
        $this->usersRetriever = $usersRetriever;
        $this->messageSender = $messageSender;
    }

    /**
     * Handle message sent to the bot
     *
     * @param $msg
     */
    public function handle($msg)
    {
        $message = $msg->text;
        $fromId = $msg->from_id;

        if (preg_match("/Add\s\S+\s\d\d\.\d\d(\.\d{4})?/i", $message)) {
            $messageArr = explode(" ", $message);
            $userId = $messageArr[1];
            $birthday = $messageArr[2];
            $user = $this->usersRetriever->getUser($userId);

            if (array_key_exists("error", $user)) {
                $this->messageSender->send("Чет не могу найти такой айдишник. Проверь.", $fromId);
            } else {
                $this->messageSender->send($user->responde->first_name, $fromId);
            }

        } else {
            $this->messageSender->send("Не знаю таких команд, братан.", $fromId);
        }
    }
}