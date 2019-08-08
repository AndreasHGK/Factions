<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

use AndreasHGK\Factions\manager\MemberManager;
use AndreasHGK\Factions\utils\FactionException;
use pocketmine\Player;

class Faction {

    public const WARZONE = "warzone";
    public const SAFEZONE = "safezone";

    public const MAXPOWER = 10000;

    /** @var string */
    protected $name;
    /** @var string */
    protected $desc = "a faction";
    /** @var int */
    protected $power = 0;
    /** @var string[] */
    protected $members = [];
    /** @var string[] */
    protected $invites = [];

    public function __construct(string $name){
        $this->name = $name;
    }

    public function broadcast(string $message) : void{
        foreach ($this->members as $name){
            $member = MemberManager::get($name);
            if($member->isOnline()){
                $member->getPlayer()->sendMessage($message);
            }
        }
    }

    public function getName() : string {
        return $this->name;
    }

    public function getDesc() : string {
        return $this->desc;
    }

    public function setDesc(?string $desc) : void{
        $this->desc = $desc;
    }

    public function getPower() : int{
        return $this->power;
    }

    public function setPower(int $power) : void{
        if($power < 0 || $power > self::MAXPOWER) throw new FactionException("power is out of bounds");
        $this->power = $power;
    }

    public function addPower() : void{
        $power = 0;
        foreach($this->members as $name){
            $member = MemberManager::get($name);
            if($member->isOnline()){
                $power++;
            }
        }
        $this->power += $power;
    }

    public function getMemberNames() : array {
        return $this->members;
    }

    public function getInvitedNames() : array {
        return $this->invites;
    }

    /**
     * @param string[] $members
     */
    public function setMembers(array $members) : void{
        $this->members = $members;
    }

    public function addMember(string $member) : void {
        $this->members[strtolower($member)] = $member;
    }

    public function removeMember(string $member) : void {
        unset($this->members[strtolower($member)]);
    }

    public function hasMember(string $member) : bool {
        return isset($this->members[strtolower($member)]);
    }

    /**
     * @param string[] $invites
     */
    public function setInvites(array $invites) : void{
        $this->invites = $invites;
    }

    public function isInvited(string $player) : bool{
        return isset($this->invites[$player]);
    }

    public function addInvite(string $player) : bool{
        if($this->isInvited($player)) return false;
        $this->invites[$player] = $player;
        return true;
    }

    public function removeInvite(string $player) : bool{
        if(!$this->isInvited($player)) return false;
        unset($this->invites[$player]);
        return true;
    }

    public function isSpecial() : bool{
        return $this->name === self::WARZONE || $this->name === self::SAFEZONE;
    }

    public function __toString()
    {
        return $this->getName();
    }

}