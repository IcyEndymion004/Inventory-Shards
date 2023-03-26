<?php

namespace IcyEndymion004\InventoryShards\Commands;

use IcyEndymion004\InventoryShards\Translation;
use IcyEndymion004\InventoryShards\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GiveShardCommand extends Command {

    public function __construct()
    {
        parent::__construct("giveshard", "Gives Your self or The Given player the Shard", "/giveshard {shard} {amount} {player}", []);
        $this->setPermission("inventory.shards.command.give");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Please Use this command as a Player!");
            return;
        }
        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command!");
            return;
        }
        if(count($args) < 2){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        if (!is_array(Utils::getShard($args[0]))){
            $sender->sendMessage(TextFormat::RED . $args[0] . " is not valid, Please check your Config.");
            return;
        }
        if(!is_int($args[1])){
            $count = 1;
        }else{
            $count = $args[1];
        }
        $item = Utils::getDisplayShard($args[0]);
        $item->setCount($count);
        try {
            if(is_string($args[2]) and Server::getInstance()->getPlayerByPrefix($args[2]) instanceof Player){
                $player = Server::getInstance()->getPlayerByPrefix($args[2]);
                $player->getInventory()->addItem($item);
                Translation::sendType("Given-Shard", $player, ["{shard}" => $args[0]]);
                Translation::sendType("Give-Shard", $sender, ["{player}" => $player->getName(), "{shard}" => $args[0]]);
            }else{
                $sender->sendMessage(TextFormat::RED . "The Given Player is invalid.");
            }
            return;
        }catch (\ErrorException){
            $sender->getInventory()->addItem($item);
            Translation::sendType("Give-Shard", $sender, ["{player}" => $sender->getName(), "{shard}" => $args[0]]);
            return;
        }
    }
}