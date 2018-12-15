<?php

namespace VkBirthdayReminder;

/**
 * Contains events issued by VK
 */
final class VkEvents
{
    /**
     * The event is issued when VK expects a server to return confirmation key
     */
    const CONFIRMATION = "confirmation";

    /**
     * The event is issued when new message is received
     */
    const MESSAGE_NEW = "message_new";
}
