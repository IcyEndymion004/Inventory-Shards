<?php

namespace IcyEndymion004\InventoryShards;

use IcyEndymion004\InventoryShards\Animations\AnimationHandler;
use IcyEndymion004\InventoryShards\Economy\EconomyManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;

class EventListener implements Listener {

    public function onItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $nbt = $item->getNamedTag();
        if($nbt->getTag("ShardData") !== null and $nbt->getTag("ShardVerified") !== null){
            $data = $nbt->getTag("ShardData")->getValue();
            if($player->isSneaking() and Utils::Shift_Preview()){
                Utils::Preview_Shard_Gui($player, $data);
                return;
            }
            $loot = Utils::getShardContents($data);
            if($player->getInventory()->firstEmpty() === -1){
                Translation::sendType("Full-Inventory", $player);
                return;
            }
            if (EconomyManager::isEnabled()){
                $currency = EconomyManager::getCurrency();
                $balance = $currency->getBalance($player);
                /** @var float $cost */
                $cost = $nbt->getTag("Shard-Claim-Cost");
                if(!$balance >= $cost){
                    Translation::sendType("Cost-To-Redeem", $player);
                    return;
                }
                $currency->subtractBalance($player, $cost);
            }
            $count = $item->getCount() - 1;
            $player->getInventory()->setItemInHand($count <= 0 ? VanillaItems::AIR() : $item->setCount($count));
            AnimationHandler::Animate(Utils::getAnimationFromShard($data), $player, $loot);
        }
    }
}