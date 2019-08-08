<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\manager;

use AndreasHGK\Factions\Claim;
use AndreasHGK\Factions\Faction;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class ClaimManager{

    /**
     * @var self */
    public static $instance = null;

    /**
     * @var Claim[]
     */
    public $cache = [];

    public static function releaseCache() : void {
        $file = self::getFile();
        foreach(self::getInstance()->cache as $claim){
            if($claim->getFaction() === null) {
                if(isset($file[$claim->getKey()])) unset($file[$claim->getKey()]);
            }
            $file[$claim->getKey()] = $claim->getFactionID();
        }
        if($file->hasChanged()) $file->save();
    }

    public static function get(int $chunkX, int $chunkZ, Level $level) : Claim{
        $key = self::getClaimKey($chunkX, $chunkZ, $level);
        $faction = DataManager::getKey(DataManager::CLAIMS, $key, null);
        $claim = new Claim($chunkX, $chunkZ, $level, $faction);
        self::getInstance()->cache[$key] = $claim;
        return $claim;
    }

    public static function getClaimKey(int $chunkX, int $chunkZ, Level $level) : string {
        return $chunkX.":".$chunkZ.":".$level->getName();
    }

    public static function claim(Faction $faction, int $chunkX, int $chunkZ, Level $level = null) : bool {
        $key = self::getClaimKey($chunkX, $chunkZ, $level);
        $originalFaction = DataManager::getKey(DataManager::CLAIMS, $key, null);
        if($originalFaction === $faction->getName()) return false;
        self::get($chunkX, $chunkZ, $level)->setFaction($faction->getName());
        return true;
    }

    public static function unclaim(int $chunkX, int $chunkZ, Level $level = null) : bool {
        $key = self::getClaimKey($chunkX, $chunkZ, $level);
        $originalFaction = DataManager::getKey(DataManager::CLAIMS, $key, null);
        if($originalFaction === null) return false;
        self::get($chunkX, $chunkZ, $level)->setFaction(null);
        return true;
    }

    public static function getFactionAt(int $chunkX, int $chunkZ, Level $level = null) : ?Faction {
        return self::get($chunkX, $chunkZ, $level)->getFaction();
    }

    public static function isClaimed(int $chunkX, int $chunkZ, Level $level = null) : bool {
        $fac = self::getFactionAt($chunkX, $chunkZ, $level);
        if(!FactionManager::exists($fac->getName())) {
            self::unclaim($chunkX, $chunkZ, $level);
            return false;
        }
        return $fac !== null;
    }

    // loading, reloading and saving will do the action on every single plot
    public static function load() : Config {
        return DataManager::load(DataManager::CLAIMS);
    }

    public static function isLoaded() : bool {
        return DataManager::isLoaded(DataManager::CLAIMS);
    }

    public static function reload() : void {
        self::getFile()->reload();
    }

    public static function save() : void {
        self::getFile()->save();
    }

    public static function getFile() : Config {
        return DataManager::get(DataManager::CLAIMS);
    }

    private function __construct()
    {
    }

    public static function getInstance() : self {
        if(self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

}