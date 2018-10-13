<?php

namespace VkBirthdayReminder\Handlers;

class MessageHandler implements MessageHandlerInterface
{
    public function handle($data)
    {
        $message = $data->object->text;

        preg_match("/Add\s\S+\s\d\d\.\d\d(\.\d{4})?/i", $message);
    }
}