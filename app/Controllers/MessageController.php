<?php

namespace VkBirthdayReminder\Controllers;

class MessageController
{
    public function store()
    {
        $data = json_decode(file_get_contents("php://input"));
    }
}