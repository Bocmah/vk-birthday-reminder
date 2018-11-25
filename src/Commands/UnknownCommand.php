<?php

namespace VkBirthdayReminder\Commands;

use VkBirthdayReminder\Helpers\MessageSender;

class UnknownCommand implements CommandInterface
{
    /**
     * VK message object
     * @var
     */
    protected $msg;

    /**
     * @var MessageSender
     */
    protected $messageSender;

    public function __construct($msg, MessageSender $messageSender)
    {
        $this->msg = $msg;
        $this->messageSender = $messageSender;
    }

    public function execute()
    {
        $this->messageSender->send("Не знаю таких команд братан", $this->msg->from_id);
    }
}