<?php

namespace IcyEndymion004\InventoryShards\Economy\Currencys;

use IcyEndymion004\InventoryShards\Economy\BaseCurrency;
use IcyEndymion004\InventoryShards\Loader;
use pocketmine\player\Player;

/**
 * To be done at a later date.... or never - Icy 2022
 */
class CapitalCurrency extends BaseCurrency {


    public function getName(): string
    {
        return "Capital";
    }

    public function getBalance(Player $player): int|float
    {
        return 0;
    }

    public function setBalance(Player $player, int $amount): void
    {
    }

    public function addBalance(Player $player, int $amount): void
    {
    }

    public function subtractBalance(Player $player, int $amount): void
    {
    }
}