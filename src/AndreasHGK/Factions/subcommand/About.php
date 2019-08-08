<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\subcommand;

use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\Member;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class About extends SubCommand {

    public $name = "about";
    public $desc = "get info about the plugin";
    public $aliases = ["about"];
    public $usage = "/f about";

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args, Member $member = null): bool
    {
        $sender->sendMessage(TextFormat::colorize(
            "§r§8§l<§9!§8>§r§9 Factions§7 info§r\n §8§l>§r §7Version: §9".Factions::get()->getDescription()->getVersion()."§r\n §8§l>§r §7Author: §9AndreasHGK§r"
        ));
        return true;
    }

}