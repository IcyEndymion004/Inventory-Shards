<?php

namespace IcyEndymion004\InventoryShards\Commands;

use IcyEndymion004\InventoryShards\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditShardContentsCommand extends Command {

    public function __construct()
    {
        parent::__construct("editshardcontents", "Allows Editing of a shards inventory!", "/editshardcontents {shard}", []);
        $this->setPermission("inventory.shards.command.edit");
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
        Utils::Edit_Shard_Gui($sender, $args[0]);
    }
}