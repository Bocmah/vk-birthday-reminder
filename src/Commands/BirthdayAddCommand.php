<?php

namespace VkBirthdayReminder\Commands;

use VkBirthdayReminder\Helpers\{UserRetriever, MessageSender};

class BirthdayAddCommand implements CommandInterface
{
    /**
     * VK message object
     * @var
     */
    protected $msg;

    /**
     * @var UserRetriever
     */
    protected $userRetriever;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    /**
     * BirthdayAddCommand constructor.
     * @param $msg
     * @param UserRetriever $userRetriever
     * @param MessageSender $messageSender
     */
    public function __construct($msg, UserRetriever $userRetriever, MessageSender $messageSender)
    {
        $this->msg = $msg;
        $this->userRetriever = $userRetriever;
        $this->messageSender = $messageSender;
    }

    /**
     * Execute the command.
     */
    public function execute()
    {
        $senderId = $this->msg->from_id;
        $messageArr = explode(" ", $this->msg->text);
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
            $this->messageSender->send(
                "Я нашель.",
                $senderId
            );
        }
    }
}