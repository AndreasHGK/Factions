<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\subcommand;

use AndreasHGK\Factions\command\FactionCommand;
use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\Member;
use pocketmine\command\Command;
use pocketmine\command\CommandMap;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Help extends SubCommand {

    public $consoleExecute = true;

    public $name = "help";
    public $desc = "show all available commands";
    public $aliases = ["help", "?", "commands"];
    public $usage = "/f help";

    /**
     * @var SubCommand[]
     */
    public $sub = [];

    public function __construct(array $sub)
    {
        $this->sub = $sub;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args, Member $member = null): bool
    {
        $page = 1;
        if(isset($args[0])){
            if(!is_int((int)$args[0])) {
                $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 Please enter a valid page number."));
                return true;
            }
            if((int)$args[0] < 1){
                $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 That page doesn't exist!"));
                return true;
            }
            $page = (int)$args[0];
        }
        var_dump($page);
        $max = (int)ceil((count($this->sub)/10));
        var_dump($max);
        if($page > $max){
            $sender->sendMessage(TextFormat::colorize("§r§8§l<§9!§8>§r§7 That page doesn't exist!"));
            return true;
        }

        $str = "§r§8§l<§9!§8>§r§7 §9Faction §7help §8- §7showing page §9".$page." §7out of §9".$max."§7.§r";
        $int = 0;
        foreach ($this->sub as $subcommand){
            $int++;
            if($int >= ($page-1)*10 && $int < ($page)*10){
                $str .= "\n §8§l> §r§9/f ".$subcommand->name."§r §8- §7".$subcommand->desc."§r";
            }
        }
        $sender->sendMessage(TextFormat::colorize($str));
        return true;
    }

}