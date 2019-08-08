<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\manager;

use AndreasHGK\Factions\Faction;
use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\Member;
use AndreasHGK\Factions\utils\FactionException;
use AndreasHGK\Factions\utils\FactionRank;
use AndreasHGK\Factions\utils\StorageException;
use Composer\Command\SelfUpdateCommand;
use pocketmine\utils\Config;

class FactionManager {

    public const FACTION_DIR = "factions";

    public const KEY_NAME = "name";
    public const KEY_DESC = "desc";
    public const KEY_POWER = "power";
    public const KEY_MEMBERS = "members";
    public const KEY_INVITES = "invites";

    public const DEFAULT_DATA = [
        "name" => null,
        "desc" => "a faction",
        "power" => 0,
        "members" => [],
        "invites" => [],
    ];

    /** @var FactionManager */
    private static $instance;

    /** @var Faction[] */
    protected $memory = [];

    public static function create(string $name, Member $leader) : Faction {
        if(self::exists($name)) throw new FactionException("A faction with that name already exists");
        $faction = new Faction($name);
        $faction->addMember($leader->getName());
        $faction->setPower(DataManager::getKey(DataManager::CONFIG, "standard-power", 100));
        self::getInstance()->memory[$name] = $faction;
        $leader->setFaction($name);
        $leader->setRank(FactionRank::MAX);
        self::save($faction->getName());
        return $faction;
    }

    public static function delete(string $fname) : void {
        $f = self::get($fname);
        foreach($f->getMemberNames() as $name){
            $mbr = MemberManager::get($name);
            $mbr->setFaction(null);
            $mbr->setRank(null);
        }
        unset(self::getInstance()->memory[$fname]);
        DataManager::deleteFile(self::FACTION_DIR.DIRECTORY_SEPARATOR.$fname.".json");
    }

    public static function get(string $name) : Faction{
        if(self::isLoaded($name)) return self::getInstance()->memory[$name];
        if(self::exists($name)) return self::load($name);
        return null;
    }

    /**
     * @return Faction[]
     */
    public static function getAll() : array {
        return self::getInstance()->memory;
    }

    public static function load(string $name) : Faction{
        if(!self::exists($name)) throw new FactionException("faction doesn't exist");
        $data = self::getFactionData($name);
        $name = $data->get(self::KEY_NAME);
        $desc = $data->get(self::KEY_DESC);
        $power = $data->get(self::KEY_POWER);
        if(!is_string($name)) throw new StorageException("invalid value found in faction data");
        if(!is_string($desc)) $desc = "a faction";
        if(!is_int($power)) $power = 0;
        $faction = new Faction($name);
        $faction->setDesc($desc);
        $faction->setPower($power);

        $faction->setMembers($data->get(self::KEY_MEMBERS));
        $faction->setInvites($data->get(self::KEY_INVITES));
        self::getInstance()->memory[$name] = $faction;
        return $faction;
    }

    public static function unload(string $name) : bool{
        if(!self::isLoaded($name)) return false;
        self::save($name);
        unset(self::getInstance()->memory[$name]);
        return true;
    }

    /**
     * @return Faction[] */
    public static function loadAll() : array {
        $return = [];
        $scans = scandir(Factions::get()->getDataFolder().self::FACTION_DIR);
        foreach($scans as $scan){
            $scanexpl = explode(".", $scan);
            if(self::exists($scanexpl[0])){
                $return[$scanexpl[0]] = self::load($scanexpl[0]);
            }
        }
        return $return;
    }

    public static function isLoaded(string $name) : bool{
        return isset(self::getInstance()->memory[$name]);
    }

    public static function save(string $name) : bool{
        if(!self::isLoaded($name)) return false;

        $data = self::getFactionData($name);
        $fac = self::get($name);

        $data->set(self::KEY_NAME, $fac->getName());
        $data->set(self::KEY_DESC, $fac->getDesc());
        $data->set(self::KEY_POWER, $fac->getPower());
        $data->set(self::KEY_MEMBERS, $fac->getMemberNames());
        $data->set(self::KEY_INVITES, $fac->getInvitedNames());
        $data->save();
        return true;
    }

    /**
     * @return Faction[]
     */
    public static function saveAll() : array {
        $return = [];
        foreach(self::getAll() as $faction){
            $return[$faction->getName()] = $faction;
            self::save($faction->getName());
        }
        return $return;
    }

    public static function exists(string $name) : bool {
        return DataManager::exists(self::FACTION_DIR.DIRECTORY_SEPARATOR.$name.".json");
    }

    public static function getFactionData(string $name) : Config{
        return DataManager::get(self::FACTION_DIR.DIRECTORY_SEPARATOR.$name.".json", false);
    }

    private function __construct(){}

    public static function getInstance() : FactionManager{
        if(!isset(self::$instance)){
            self::$instance = new FactionManager();
        }
        return self::$instance;
    }

}