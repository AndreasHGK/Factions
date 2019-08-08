<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\subcommand;

use AndreasHGK\Factions\Member;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

abstract class SubCommand {

    public $consoleExecute = false;

    public function canBeExecutedFromConsole() : bool {
        return $this->consoleExecute;
    }

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     * @param Member        $member
     *
     * @return bool
     */
    public abstract function onCommand(CommandSender $sender, Command $command, string $label, array $args, Member $member = null) : bool;

}