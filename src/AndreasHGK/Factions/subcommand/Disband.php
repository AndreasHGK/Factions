<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\subcommand;

use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\manager\FactionManager;
use AndreasHGK\Factions\Member;
use AndreasHGK\Factions\utils\FactionException;
use AndreasHGK\Factions\utils\FactionRank;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Disband extends SubCommand {

    public $name = "disband";
    public $desc = "disband a faction";
    public $aliases = ["disband", "del", "delete", "dis"];
    public $usage = "/f disband";


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args, Member $member = null): bool
    {
        if(!$member->hasFaction()){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 You are not in a faction."));
            return true;
        }
        if($member->getRank()->getRankID() < FactionRank::LEADER){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 You don't have permission to disband your faction."));
            return true;
        }

        $fac = $member->getFactionID();

        FactionManager::delete($member->getFactionID());
        $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 You have disbanded §9".$fac."§7."));

        return true;
    }

}