<?php

namespace VkBirthdayReminder\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController
{
    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        if (!$data) {
            return new Response("nioh");
        }

        if ($data->secret !== getenv("VK_SECRET_KEY") && $data->type !== "confirmation") {
            return new Response("nioh");
        }

        switch ($data->type) {
            case "confirmation":
                return new Response(getenv("VK_CONFIRMATION_KEY"));
                break;
            case "message_new":
                break;
        }
    }
}