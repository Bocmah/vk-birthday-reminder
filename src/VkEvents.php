<?php

namespace VkBirthdayReminder;

/**
 * Contains events thrown by VK
 */
final class VkEvents
{
    /**
     * The event is thrown when VK expects a server to return confirmation key
     */
    const CONFIRMATION = "confirmation";

    /**
     * The event is thrown when new message is received
     */
    const MESSAGE_NEW = "message_new";
}
