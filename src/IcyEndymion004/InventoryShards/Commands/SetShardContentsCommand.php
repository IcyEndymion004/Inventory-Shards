<?php

namespace IcyEndymion004\InventoryShards\Commands;

use IcyEndymion004\InventoryShards\Translation;
use IcyEndymion004\InventoryShards\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class  SetShardContentsCommand extends Command {

    public function __construct()
    {
        parent::__construct("setshardcontents", "Sets the given shards contents as your inventory!", "/setshardcontents {shard}", []);
        $this->setPermission("inventory.shards.command.setcontent");
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
        if(!count($args) > 0){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        if (!is_array(Utils::getShard($args[0]))){
            $sender->sendMessage(TextFormat::RED . $args[0] . " is not valid, Please check your Config.");
            return;
        }
        Translation::sendType("Update-Shard-Contents", $sender, ["{shard}" => $args[0]]);
        Utils::setShardContents($args[0], $sender->getInventory()->getContents());
    }
}