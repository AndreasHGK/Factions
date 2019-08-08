<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

use AndreasHGK\Factions\utils\FactionRank;
use pocketmine\IPlayer;
use pocketmine\utils\UUID;

interface IMember{

    /** @return string */
    public function getName() : string;

    /** @return string */
    public function getFactionID() : ?string;

    /** @return Faction */
    public function getFaction() : ?Faction;

    /**
     * @param string $factionID
     * @param bool $force
     */
    public function setFaction(string $factionID = null, bool $force = false) : void;

    public function hasFaction() : bool;

    /** @return IPlayer */
    public function getPlayer() : IPlayer;

    /** @return FactionRank */
    public function getRank() : ?FactionRank ;

    /** @param int $rank*/
    public function setRank(?int $rank) : void ;

    public function onEdited() : void;

    public function save() : void;

    public function isOnline() : bool;

}