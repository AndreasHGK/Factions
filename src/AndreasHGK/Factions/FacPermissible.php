<?php

declare(strict_types=1);

namespace AndreasHGK\Factions;

interface FacPermissible{

    public function isOverriding() : bool;

    public function setOverriding(bool $overriding) : void;

}