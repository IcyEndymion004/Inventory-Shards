<?php

namespace IcyEndymion004\InventoryShards\Economy;

use pocketmine\player\Player;

abstract class BaseCurrency {

    public abstract function getName(): string;

    public abstract function setBalance(Player $player, int $amount): void;

    public abstract function addBalance(Player $player, int $amount): void;

    public abstract function subtractBalance(Player $player, int $amount): void;

    public abstract function getBalance(Player $player): int|float;
}