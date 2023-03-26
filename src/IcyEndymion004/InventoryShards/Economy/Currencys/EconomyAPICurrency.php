<?php

namespace IcyEndymion004\InventoryShards\Economy\Currencys;

use IcyEndymion004\InventoryShards\Economy\BaseCurrency;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;

class EconomyAPICurrency extends BaseCurrency {

    public function getName(): string
    {
        return "EconomyAPI";
    }

    /**
     * @throws \Exception
     */
    public function getBalance(Player $player): int|float
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
        if ($api instanceof EconomyAPI){
            return $api->myMoney($player);
        }else{
            throw new \Exception("Economy Called When no valid plugin is in place!");
        }
    }

    /**
     * @throws \Exception
     */
    public function setBalance(Player $player, int $amount): void
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
        if ($api instanceof EconomyAPI){
            $api->setMoney($player, $amount);
        }else{
            throw new \Exception("Economy Called When no valid plugin is in place!");
        }
    }

    /**
     * @throws \Exception
     */
    public function addBalance(Player $player, int $amount): void
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
        if ($api instanceof EconomyAPI){
            $api->addMoney($player, $amount);
        }else{
            throw new \Exception("Economy Called When no valid plugin is in place!");
        }
    }

    /**
     * @throws \Exception
     */
    public function subtractBalance(Player $player, int $amount): void
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI");
        if ($api instanceof EconomyAPI){
            $api->reduceMoney($player, $amount);
        }else{
            throw new \Exception("Economy Called When no valid plugin is in place!");
        }
    }
}