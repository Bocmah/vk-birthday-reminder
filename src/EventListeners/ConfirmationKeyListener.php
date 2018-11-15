<?php

namespace VkBirthdayReminder\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use VkBirthdayReminder\VkEvents;

class ConfirmationKeyListener implements EventSubscriberInterface
{
    /**
     * If VK expects confirmation send the confirmation key
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $data = json_decode($event->getRequest()->getContent());

        if ($data->type === VkEvents::CONFIRMATION) {
            $response = new Response(getenv("VK_CONFIRMATION_KEY"));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array("onKernelRequest"));
    }
}