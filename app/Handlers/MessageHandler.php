<?php

namespace VkBirthdayReminder\Handlers;

use VkBirthdayReminder\Helpers\UserRetriever;
use VkBirthdayReminder\Helpers\MessageSender;

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
        $message = $msg->text;
        $fromId = $msg->from_id;

        if (preg_match("/Add\s\S+\s\d\d\.\d\d(\.\d{4})?/i", $message)) {
            $messageArr = explode(" ", $message);
            $userId = $messageArr[1];
            $birthday = $messageArr[2];
            $user = $this->userRetriever->getUser($userId, true);

            if (array_key_exists("error", $user)) {
                $this->messageSender->send("Чет не могу найти такой айдишник. Проверь.", $fromId);
            } else {
                //$this->messageSender->send($user["response"]["first_name"], $fromId);
                foreach ($user as $key => $value) {
                    echo "{$key}: {$value}";
                }
            }

        } else {
            $this->messageSender->send("Не знаю таких команд, братан.", $fromId);
        }
    }
}