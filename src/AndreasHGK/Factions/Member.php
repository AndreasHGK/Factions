<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

class Member extends OfflineMember implements FacPermissible {

    /** @var bool */
    protected $overriding = false;
    /** @var bool */
    protected $fchat = false;

    public function isOverriding() : bool {
        return $this->overriding;
    }

    public function setOverriding(bool $overriding) : void{
        $this->overriding = $overriding;
    }

    public function isOnline() : bool{
        return true;
    }

    public function onEdited(): void
    {
        return;
    }

    public function isFChat() : bool{
        return $this->fchat;
    }

    public function setFChat(bool $fchat) : void{
        $this->fchat = $fchat;
    }

}