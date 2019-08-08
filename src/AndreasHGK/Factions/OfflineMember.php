<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

use AndreasHGK\Factions\manager\FactionManager;
use AndreasHGK\Factions\manager\MemberManager;
use AndreasHGK\Factions\utils\FactionRank;
use pocketmine\IPlayer;

class OfflineMember implements IMember{

    /** @var string */
    protected $name;
    /** @var IPlayer */
    protected $player;
    /** @var string|null */
    protected $factionID;
    /** @var FactionRank */
    protected $rank;

    /**
     * Do NOT call this function. Use MemberManager::get() to get a member class.
     *
     * @param IPlayer $player
     */
    public function __construct(IPlayer $player)
    {
        $this->name = $player->getName();
        $this->player = $player;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlayer(): IPlayer
    {
        return $this->player;
    }

    public function getFactionID(): ?string
    {
        return $this->factionID;
    }

    public function getFaction(): ?Faction
    {
        return FactionManager::get($this->factionID);
    }

    public function setFaction(string $factionID = null, bool $force = false): void
    {
        $this->factionID = $factionID;
        $this->onEdited();
    }

    public function hasFaction(): bool
    {
        return isset($this->factionID) && FactionManager::exists($this->factionID);
    }

    public function isOnline() : bool{
        return false;
    }

    public function getRank(): ?FactionRank
    {
        return $this->rank;
    }

    public function setRank(?int $rank): void
    {
        if($rank === null){
            $this->rank = null;
            return;
        }
        if($this->rank === null) $this->rank = FactionRank::getRank($rank);
        else $this->rank->setRank($rank);
        $this->onEdited();
    }

    public function onEdited(): void
    {
        MemberManager::saveOffline($this);
    }

    public function save(): void
    {
        MemberManager::save($this->getName());
    }

    public function __toString()
    {
        return $this->getName();
    }

}