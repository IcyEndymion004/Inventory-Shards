<?php

namespace IcyEndymion004\InventoryShards\Commands;

use IcyEndymion004\InventoryShards\Lib\jojoe77777\FormAPI\SimpleForm;
use IcyEndymion004\InventoryShards\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditShardCommand extends Command {

    public function __construct()
    {
        parent::__construct("editconfigs", "Allows Editing of a shards data!", "/editconfigs {shard}", ["editshard"]);
        $this->setPermission("inventory.shards.command.editdata");
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
        $this->EditShard($sender, $args[0]);
    }

    public function EditShard(Player $player, string $name): void {
        $form = new SimpleForm(function (Player $player, $data) use ($name){
            if ($data !== null){
                var_dump($data);
            }
        });
        $form->setTitle(TextFormat::GREEN . $name . " Editing");
        $form->setContent(TextFormat::GRAY . TextFormat::BOLD . "Attributes " . "\n" . TextFormat::RESET . TextFormat::GRAY . "  Are the values of the shard like display name or Cost to claim!" . "\n" . TextFormat::BOLD . "Shard Item" . TextFormat::RESET . TextFormat::GRAY . "\n" . "  Is the values of the item when you give the shard to a user" . "\n");
        $form->addButton(TextFormat::GRAY . "Attributes");
        $form->addButton(TextFormat::GRAY . "Shard Item");
        $form->addButton(TextFormat::RED . "Close");
        $player->sendForm($form);
    }

    public function ShardAttributes(Player $player, string $name): void {

    }

    public function ShardItem(Player $player, string $name): void {

    }
}