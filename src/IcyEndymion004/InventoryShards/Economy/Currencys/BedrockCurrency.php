<?php

namespace IcyEndymion004\InventoryShards\Economy\Currencys;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use IcyEndymion004\InventoryShards\Economy\BaseCurrency;
use pocketmine\player\Player;

class BedrockCurrency extends BaseCurrency {

    public function getName(): string
    {
        return "BedrockEconomy";
    }

    public function getBalance(Player $player): int|float
    {
        $balance = BedrockEconomyAPI::legacy()->getPlayerBalance($player->getName());
        return $balance->getResult();
    }

    public function addBalance(Player $player, int $amount): void
    {
        BedrockEconomyAPI::legacy()->addToPlayerBalance($player->getName(), $amount);
    }

    public function setBalance(Player $player, int $amount): void
    {
        BedrockEconomyAPI::legacy()->setPlayerBalance($player->getName(), $amount);
    }

    public function subtractBalance(Player $player, int $amount): void
    {
        BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(), $amount);
    }
}