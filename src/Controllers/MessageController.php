<?php

namespace VkBirthdayReminder\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VkBirthdayReminder\Handlers\MessageHandler;

/**
 * The only controller of the application used to redirect message to the MessageHandler.
 */
class MessageController
{
    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    public function __construct(MessageHandler $messageHandler)
    {
        $this->messageHandler = $messageHandler;
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        switch ($data->type) {
            case "message_new":
                $this->messageHandler->handle($data->object);

                return new Response("ok");
                break;
        }
    }
}