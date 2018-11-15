<?php

namespace VkBirthdayReminder\Handlers;

interface MessageHandlerInterface
{
    /**
     * Handle incoming message by examining passed $data which is VK request object
     *
     * @param $data
     */
    public function handle($data);
}