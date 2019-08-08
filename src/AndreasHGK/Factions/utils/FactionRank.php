<?php

declare(strict_types=1);

namespace AndreasHGK\Factions\utils;



class FactionRank{

    //ids
    public const RECRUIT = 0;
    public const MEMBER = 1;
    public const OFFICER = 2;
    public const LEADER = 3;

    public const MIN = 0;
    public const MAX = 3;

    public static function getIDFromName(string $rankname) : int{
        switch ($rankname){
            case "recruit":
                return self::RECRUIT;
                break;
            case "member":
                return self::MEMBER;
                break;
            case "officer":
                return self::OFFICER;
                break;
            case "leader":
                return self::LEADER;
                break;
            default:
                throw new FactionsException("rank doesn't exist");
        }
    }

    public static function getRankName(int $rank) : string {
        switch ($rank){
            case self::RECRUIT:
                return "recruit";
                break;
            case self::MEMBER:
                return "member";
                break;
            case self::OFFICER:
                return "officer";
                break;
            case self::LEADER:
                return "leader";
                break;
            default:
                throw new FactionsException("rank out of bounds");
        }
    }

    public static function getRankSymbol(int $rank) : string {
        switch ($rank){
            case self::RECRUIT:
                return "-";
                break;
            case self::MEMBER:
                return "+";
                break;
            case self::OFFICER:
                return "*";
                break;
            case self::LEADER:
                return "**";
                break;
            default:
                throw new FactionsException("rank out of bounds");
        }
    }

    public static function inBounds(int $rank) : bool {
        return $rank < self::MIN || $rank > self::MAX ? false : true;
    }

    public static function getRank(int $rank) : FactionRank{
        if(!self::inBounds($rank)) throw new FactionsException("rank out of bounds");
        return new FactionRank($rank);
    }

    /** @var int */
    public $rank;

    public function setRank(int $id) : void{
        if(!self::inBounds($id)) throw new FactionsException("rank out of bounds");
        $this->rank = $id;
    }

    public function getRankID() : int{
        return $this->rank;
    }

    public function getName() : string {
        return self::getRankName($this->getRankID());
    }

    public function getSymbol() : string {
        return self::getRankSymbol($this->getRankID());
    }

    private function __construct(int $rank)
    {
        $this->rank = $rank;
    }

    public function __toString()
    {
        return self::getName();
    }

}