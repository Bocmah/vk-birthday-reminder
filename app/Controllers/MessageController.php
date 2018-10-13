<?php

namespace VkBirthdayReminder\Controllers;

use Symfony\Component\HttpFoundation\Request;

class MessageController
{
    public function store(Request $request)
    {
        $data = json_decode($request->getContent());


    }
}