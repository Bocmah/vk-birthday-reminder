<?php

namespace VkBirthdayReminder\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use VkBirthdayReminder\VkEvents;

class RequestDataMismatchListener implements EventSubscriberInterface
{
    /**
     * Return negative response in case of secret key mismatch or missing request content
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $data = json_decode($event->getRequest()->getContent());

        if (!$data) {
            $event->setResponse(new Response("Couldn't get request content"));
        }

        if ($data->secret !== getenv("VK_SECRET_KEY") && $data->type !== VkEvents::CONFIRMATION) {
            $event->setResponse(new Response("Secret key mismatch"));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array("onKernelRequest"));
    }
}