<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\listener;

use AndreasHGK\Factions\manager\MemberManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class MemberListener implements Listener {

    public function onLogin(PlayerLoginEvent $ev) : void{
        MemberManager::get($ev->getPlayer()->getName());
    }

    public function onQuit(PlayerQuitEvent $ev) : void{
        MemberManager::unload($ev->getPlayer()->getName());
    }

}