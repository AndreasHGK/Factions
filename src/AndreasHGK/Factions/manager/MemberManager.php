<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\manager;

use AndreasHGK\Factions\Factions;
use AndreasHGK\Factions\IMember;
use AndreasHGK\Factions\Member;
use AndreasHGK\Factions\OfflineMember;
use AndreasHGK\Factions\utils\MemberException;
use pocketmine\IPlayer;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;

class MemberManager {

    public const MEMBER_DIR = DIRECTORY_SEPARATOR."members";

    public const DEFAULT_DATA = [
        "name" => null,
        "FactionID" => "",
        "rank" => 0,
    ];

    /** @var MemberManager */
    private static $instance;

    public static function get(string $player, bool $create = true) : ?IMember{
        if(self::isLoaded($player)) return self::getOnlineMembers()[$player];
        if(self::exists($player)){
            return self::load($player);

        }elseif($create){
            self::create($player);
            return self::load($player);

        }else{
            return null;
        }
    }

    public static function load(string $name) : IMember{
        //if(!Server::getInstance()->hasOfflinePlayerData($name)) throw new MemberException("Player was never connected");
        if(!self::exists($name)) throw new MemberException("Member is not created");



        $player = Server::getInstance()->getOfflinePlayer($name);

        $data = self::getMemberData($name);

        $member = new OfflineMember($player);
        if($player->isOnline()){
            $member = new Member($player);

        }
        $rank = $data->get("rank", null);
        $fac = $data->get("FactionID", null);
        if(is_int($rank)) $member->setRank($rank);
        if(is_string($fac)) $member->setFaction($fac, true);

        if($member->isOnline()) self::getInstance()->memory[$member->getName()] = $member;

        return $member;
    }

    public static function isLoaded(string $player) : bool {
        $members = self::getOnlineMembers();
        return isset($members[$player]);
    }

    public static function unload(string $player) : bool{
        if(self::isLoaded($player)) {
            self::save($player);
            unset(self::getInstance()->memory[$player]);
            return true;
        }
        return false;
    }

    public function reload(string $player) : bool{
        if(self::isLoaded($player)) {
            $data = self::getMemberData($player);
            $member = self::get($player);
            $member->setFaction($data->get("FactionID", null));
            $member->setRank($data->get("rank", null));
            return true;
        }
        return false;
    }

    public static function create(string $name) : void {
        Factions::get()->getLogger()->debug("creating member: ".$name);
        //if(!Server::getInstance()->hasOfflinePlayerData($name)) throw new MemberException("Player was never connected");

        $data = self::getMemberData($name);

        $player = Server::getInstance()->getOfflinePlayer($name);

        $data->set("name", strtolower($player->getName()));
        if($player->isOnline()){
            //add online only data
        }

        $data->save();

        return;
    }

    public static function exists(string $name) : bool {
        return DataManager::exists(self::MEMBER_DIR.DIRECTORY_SEPARATOR.strtolower($name).".json");
    }

    /** @return Member[] */
    public static function getOnlineMembers() : array{
        return self::getInstance()->memory;
    }

    public static function getOnlineMember(string $player) : Member{
        return self::getOnlineMembers()[$player];
    }

    /**
     * @return IMember[]1
     */
    public static function saveAll() : array {
        $return = [];
        foreach (self::getOnlineMembers() as $member){
            $return[$member->getName()] = $member;
            self::save($member->getName());
        }
        return $return;
    }

    public static function save(string $player) : bool{
        if(self::isLoaded($player)) {
            $member = self::get($player);
            $data = self::getMemberData($player);
            $data->set("FactionID", $member->getFactionID());
            if($member->getRank() === null){
                $data->set("rank", null);
            }else{
                $data->set("rank", $member->getRank()->getRankID());
            }
            $data->save();
            return true;
        }else{
            self::saveOffline(self::get($player));
        }
        return false;
    }

    public static function saveOffline(IMember $member) : void{
        $data = self::getMemberData($member->getName());
        $data->set("FactionID", $member->getFactionID());
        if($member->getRank() === null){
            $data->set("rank", null);
        }else{
            $data->set("rank", $member->getRank()->getRankID());
        }
        $data->save();
    }

    public static function getMemberData(string $member) : Config{
        return DataManager::get(self::MEMBER_DIR.DIRECTORY_SEPARATOR.strtolower($member).".json", false);
    }

    public static function getInstance() : MemberManager{
        if(!isset(self::$instance)){
            self::$instance = new MemberManager();
        }
        return self::$instance;
    }

    /** @var Member[] */
    protected $memory = [];

    private function __construct(){}

}