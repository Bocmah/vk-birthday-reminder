<?php

namespace VkBirthdayReminder\Commands;

class Invoker
{
    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @param CommandInterface $cmd
     */
    public function setCommand(CommandInterface $cmd)
    {
        $this->command = $cmd;
    }

    /**
     * Execute the command.
     */
    public function run()
    {
        $this->command->execute();
    }
}