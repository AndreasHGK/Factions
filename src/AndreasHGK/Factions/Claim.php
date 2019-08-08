<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

use AndreasHGK\Factions\manager\ClaimManager;
use AndreasHGK\Factions\manager\FactionManager;
use pocketmine\level\Level;
use pocketmine\Server;

class Claim {

    /** @var int */
    protected $x;
    /** @var int */
    protected $z;
    /** @var Level */
    protected $level;
    /** @var string|null */
    protected $owner;

    public function __construct(int $x, int $z, ?Level $level = null, string $owner = null){
        $this->x = $x;
        $this->z = $z;
        if($level === null){
            $this->level = Server::getInstance()->getDefaultLevel();
        }else{
            $this->level = $level;
        }
        $this->owner = $owner;
    }

    public function getKey() : string {
        return ClaimManager::getClaimKey($this->getX(), $this->getZ(), $this->getLevel());
    }

    public function getLevel() : Level{
        return $this->level;
    }

    public function getX() : int{
        return $this->x;
    }

    public function getZ() : int{
        return $this->z;
    }

    public function getFactionID() : ?string {
        return $this->owner;
    }

    public function getFaction() : ?Faction{
        return FactionManager::get($this->owner);
    }

    public function setFaction(?string $faction) : void{
        $this->owner= $faction;
    }

}