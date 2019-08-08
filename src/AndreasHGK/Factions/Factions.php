<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

use AndreasHGK\AutoCompleteAPI\AutoCompleteAPI;
use AndreasHGK\AutoCompleteAPI\CustomCommandData;
use AndreasHGK\AutoCompleteAPI\CustomCommandParameter;
use AndreasHGK\Factions\command\FactionCommand;
use AndreasHGK\Factions\listener\MemberListener;
use AndreasHGK\Factions\manager\ClaimManager;
use AndreasHGK\Factions\manager\FactionManager;
use AndreasHGK\Factions\manager\MemberManager;
use AndreasHGK\Factions\task\AutosaveTask;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskHandler;

class Factions extends PluginBase{

    /** @var Factions */
    private static $instance;

    /**
     * @var TaskHandler[]
     */
    private $tasks = [];

    public static function get() : Factions{
        return self::$instance;
    }

    public static function saveAll() : void {
        FactionManager::saveAll();
        MemberManager::saveAll();
        ClaimManager::releaseCache();
    }



    public function onLoad() : void{
        self::$instance = $this;
        @mkdir($this->getDataFolder().MemberManager::MEMBER_DIR);
        @mkdir($this->getDataFolder().FactionManager::FACTION_DIR);
    }

    public function onEnable() : void{
        $this->registerCommands();
        $this->registerListeners();
        $this->registerParameters();
	}

	public function onDisable() : void{
        self::get()->getLogger()->debug("saving...");
        self::saveAll();
	}

	public function registerListeners() : void{
        $listeners = [
            new MemberListener(),
        ];
        foreach($listeners as $listener){
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }
    }

	public function registerCommands() : void{
        $commands = [
            new FactionCommand(),
        ];
        foreach($commands as $command){
            try{
                $cmd = new PluginCommand("faction", $this);
                $cmd->setAliases($command->aliases);
                $cmd->setDescription($command->desc);
                $cmd->setUsage($command->usage);
                $cmd->setExecutor($command);
                $this->getServer()->getCommandMap()->register("factions", $cmd);
            }catch (\Throwable $e){
                $this->getLogger()->error("failed to register command: ".$command->getName());
            }
        }

    }

    public function addTasks() : void{
        $tasks = [
            new AutosaveTask(),
        ];
        foreach ($tasks as $task){
            $this->tasks[$task->name] = $this->getScheduler()->scheduleRepeatingTask($task, $task->period);
        }
    }

    public function registerParameters() : void{
        $acapi = AutoCompleteAPI::getInstance();
        $cmd = $this->getServer()->getCommandMap()->getCommand("faction");
        if(isset($cmd)){
            $data = $acapi->registerCommandData($cmd, true);
            $data->singleParameter(0, 0, "create",  false);
            $data->normalParameter(0, 1, CustomCommandData::ARG_TYPE_STRING, "name", false);

            $data->singleParameter(1, 0, "delete", false);

            $data->singleParameter(2, 0, "join", false);
            $data->normalParameter(2, 1, CustomCommandData::ARG_TYPE_STRING, "faction", false);

            $data->singleParameter(3, 0, "leave", false);

            $data->singleParameter(4, 0, "invite", false);
            $data->normalParameter(4, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(5, 0, "promote", false);
            $data->normalParameter(5, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(6, 0, "demote", false);
            $data->normalParameter(6, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(7, 0, "recruit", false);
            $data->normalParameter(7, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(8, 0, "member", false);
            $data->normalParameter(8, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(9, 0, "officer", false);
            $data->normalParameter(9, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(10, 0, "leader", false);
            $data->normalParameter(10, 1, CustomCommandData::ARG_TYPE_TARGET, "name", false);

            $data->singleParameter(11, 0, "ally", false);
            $data->normalParameter(11, 1, CustomCommandData::ARG_TYPE_STRING, "faction", false);

            $data->singleParameter(12, 0, "neutral", false);
            $data->normalParameter(12, 1, CustomCommandData::ARG_TYPE_STRING, "faction", false);

            $data->singleParameter(13, 0, "war", false);
            $data->normalParameter(13, 1, CustomCommandData::ARG_TYPE_STRING, "faction", false);

            $data->singleParameter(14, 0, "top", false);
            $data->normalParameter(14, 1, CustomCommandData::ARG_TYPE_INT, "page", true);

            $data->singleParameter(15, 0, "info", false);
            $data->normalParameter(15, 1, CustomCommandData::ARG_TYPE_STRING, "faction", false);

            $data->singleParameter(16, 0, "home", false);

            $data->singleParameter(17, 0, "sethome", false);

            $data->singleParameter(18, 0, "desc", false);
            $data->normalParameter(18, 1, CustomCommandData::ARG_TYPE_STRING, "description", false);

            $data->singleParameter(19, 0, "claim", false);

            $data->singleParameter(19, 0, "unclaim", false);

            $data->singleParameter(20, 0, "about", false);
        }

    }

}
