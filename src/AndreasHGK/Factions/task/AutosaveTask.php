<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\task;

use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\manager\DataManager;
use AndreasHGK\Factions\manager\FactionManager;
use AndreasHGK\Factions\manager\MemberManager;
use pocketmine\scheduler\Task;

class AutosaveTask extends Task {

    public $name = "AutosaveTask";
    public $period = 300;

    public function __construct()
    {
        $this->period = DataManager::getKey(DataManager::CONFIG, "autosave-timer", 300);
    }

    public function onRun(int $currentTick)
    {
        Factions::get()->getLogger()->debug("autosaving...");
        Factions::saveAll();
    }
}