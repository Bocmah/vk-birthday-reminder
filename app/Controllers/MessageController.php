<?php

namespace VkBirthdayReminder\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController
{
    public function store(Request $request)
    {
        $requestParams = [
            "user_id" => 387532930,
            "message" => "КОГДА ТЫ РОДИЛСЯ КОЖАНЫЙ УБЛЮДОК",
            "access_token" => getenv("VK_TOKEN"),
            "v" => "5.85"
        ];
        file_get_contents(
            "https://api.vk.com/method/messages.send?" . http_build_query($requestParams)
        );

        return new Response("ok");
        /*
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
                $requestParams = [
                  "user_id" => $data->object->from_id,
                  "message" => "Test",
                  "access_token" => getenv("VK_TOKEN"),
                  "v" => "5.85"
                ];
                file_get_contents(
                    "https://api.vk.com/method/messages.send?" . http_build_query($requestParams)
                );

                return new Response("ok");
                break;
        }
        */
    }
}