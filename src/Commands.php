<?php

namespace VkBirthdayReminder;

/**
 * Contains all commands which are recognizable by the bot.
 */
final class Commands
{
    /**
     * Add a new observee.
     */
    const ADD = 'add';

    /**
     * Update an existing observee.
     */
    const UPDATE = 'update';

    /**
     * Delete an existing observee.
     */
    const DELETE = 'delete';

    /**
     * List all observees for observer who made a request.
     */
    const LIST = 'list';

    /**
     * List all commands recognizable by the bot and its' short descriptions.
     */
    const HELP = 'help';

    /**
     * Toggle notifiable state of the observer. If observer is notifiable, the bot will inform an observer even if
     * there are no birthdays today or tomorrow. If s/he is not, bot will only send a message if there are birthdays
     * in the near future.
     */
    const NOTIFY = 'notify';

    /**
     * This label is assigned to any command which is unrecognizable by the bot.
     */
    const UNKNOWN = 'unknown';
}