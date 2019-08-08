<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\command;

use AndreasHGK\Factions\manager\MemberManager;
use AndreasHGK\Factions\Member;
use AndreasHGK\Factions\subcommand\About;
use AndreasHGK\Factions\subcommand\Create;
use AndreasHGK\Factions\subcommand\Disband;
use AndreasHGK\Factions\subcommand\Help;
use AndreasHGK\Factions\subcommand\Info;
use AndreasHGK\Factions\subcommand\SubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FactionCommand implements CommandExecutor
{

    public $name = "factions";
    public $desc = "the main factions command";
    public $aliases = ["f", "fac"];
    public $usage = "/f <subcommand>";

    /** @var SubCommand[] */
    public $subcommands = [];

    public function __construct()
    {
        $this->subcommands = [
            new About(),
            new Create(),
            new Disband(),
            new Info(),
        ];
        $this->subcommands[] = new Help($this->subcommands);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if(!isset($args[0])){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Do §9/f help§7 to see a list of subcommands."));
            return true;
        }
        $sub_arg = array_shift($args);
        foreach($this->subcommands as $subCommand){
            if(in_array($sub_arg, $subCommand->aliases)){
                if(!$subCommand->canBeExecutedFromConsole() && !$sender instanceof Player){
                    $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Please execute this command in-game."));
                    return true;
                }
                return $subCommand->onCommand($sender, $command, $label, $args, MemberManager::get($sender->getName()));
            }
        }
        $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Subcommand not found. Do §9/f help§7 to see a list of subcommands."));
        return true;
    }
}
