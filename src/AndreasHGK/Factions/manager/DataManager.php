<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\manager;

use AndreasHGK\Factions\Factions;
use pocketmine\utils\Config;

class DataManager {

    public const CONFIG = "config.yml";
    public const MESSAGES = "lang.yml";
    public const CLAIMS = "claims.json";

    /**
     * @var DataManager */
    public static $instance = null;

    /**
     * @var Config[]
     */
    public $memory = [];

    /**
     * @param string $file
     * @param string $key
     * @param bool $default
     * @return mixed
     */
    public static function getKey(string $file, string $key, $default = false){
        return self::get($file)->get($key, $default);
    }

    public static function get(string $file, bool $keepLoaded = true) : Config {
        if(self::isLoaded($file)) return self::getInstance()->memory[$file];
        return self::load($file, $keepLoaded);
    }

    public static function load(string $file, bool $keepLoaded = true) : Config {
        $data = self::getFile($file);
        if($keepLoaded){
            self::getInstance()->memory[$file] = $data;
        }
        return $data;
    }

    public static function reload(string $file, bool $save = false) : bool{
        if(!self::isLoaded($file)) return false;
        if($save) self::get($file)->save();
        self::get($file)->reload();
    }

    public static function unload(string $file) : bool {
        if(!self::isLoaded($file)) return false;
        self::save($file);
        unset(self::getInstance()->memory[$file]);
        return true;
    }

    public static function isLoaded(string $file) : bool{
        return isset(self::getInstance()->memory[$file]);
    }

    public static function save(string $file) : bool{
        if(!self::isLoaded($file)) return false;
        self::getInstance()->memory[$file]->save();
        return true;
    }

    public static function getFile(string $file) : Config{
        return new Config(Factions::get()->getDataFolder().$file);
    }

    public static function deleteFile(string $file) : void {
        unlink(Factions::get()->getDataFolder().$file);
    }

    public static function exists(string $file) : bool {
        return file_exists(Factions::get()->getDataFolder().$file);
    }

    public static function loadDefault() : void {
        if(Factions::get()->saveResource(self::CONFIG)) Factions::get()->getLogger()->debug("creating ".self::CONFIG);
        self::get(self::CONFIG);
        if(Factions::get()->saveResource(self::MESSAGES)) Factions::get()->getLogger()->debug("creating ".self::MESSAGES);
        self::get(self::MESSAGES);
    }

    private function __construct()
    {
    }

    public static function getInstance() : self {
        if(self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

}