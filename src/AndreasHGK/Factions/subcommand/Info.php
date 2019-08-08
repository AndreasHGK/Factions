<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\subcommand;

use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\manager\FactionManager;
use AndreasHGK\Factions\Member;
use AndreasHGK\Factions\utils\FactionException;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Info extends SubCommand {

    public $consoleExecute = true;

    public $name = "info";
    public $desc = "show a faction's info";
    public $aliases = ["info", "show"];
    public $usage = "/f info [faction]";


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args, Member $member = null): bool
    {
        if((!$sender instanceof Player || !$member->hasFaction()) && !isset($args[0])){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Please enter a target faction."));
            return true;
        }
        if($sender instanceof Player){
            $fac = $member->getFaction();
        }

        if(isset($args[0])){
            $fac = FactionManager::get((string)$args[0]);
        }
        if($fac === null){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 There is no faction with that name."));
            return true;
        }

        $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Faction §9".$fac->getName()."§7 info:\n§r§8§l >§r "));

        return true;
    }

}