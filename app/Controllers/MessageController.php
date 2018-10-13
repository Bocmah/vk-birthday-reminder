<?php

namespace VkBirthdayReminder\Controllers;

use Symfony\Component\HttpFoundation\Request;

class MessageController
{
    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        if (!$data) {
            return "nioh";
        }

        if ($data->secret !== getenv("VK_SECRET_KEY") && $data->type !== "confirmation") {
            return "nioh";
        }

        switch ($data->type) {
            case "confirmation":
                return getenv("VK_CONFIRMATION_KEY");
                break;
            case "message_new":
                break;
        }
    }
}