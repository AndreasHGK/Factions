<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\subcommand;

use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\manager\FactionManager;
use AndreasHGK\Factions\Member;
use AndreasHGK\Factions\utils\FactionException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Create extends SubCommand {

    public const ALLOWED_CHARACTERS = ["_", "-"];

    public $name = "create";
    public $desc = "create a faction";
    public $aliases = ["create", "make", "new"];
    public $usage = "/f create <name>";


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args, Member $member = null): bool
    {
        if($member->hasFaction()){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 You are already in a faction."));
            return true;
        }
        if(!isset($args[0])){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Please enter a name for your faction"));
            return true;
        }
        $name = (string)$args[0];
        if(!ctype_alnum(str_replace(self::ALLOWED_CHARACTERS, '', $name))){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Your faction name contains characters that are not allowed."));
            return true;
        }

        try{
            FactionManager::create($name, $member);
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 You have created §9".$name."§7."));
        }catch (FactionException $e){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 ".$e->getMessage()."."));
        }

        return true;
    }

}