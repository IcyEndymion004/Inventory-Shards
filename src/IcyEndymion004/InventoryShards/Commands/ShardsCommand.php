<?php

namespace IcyEndymion004\InventoryShards\Commands;

use IcyEndymion004\InventoryShards\Lib\jojoe77777\FormAPI\SimpleForm;
use IcyEndymion004\InventoryShards\Loader;
use IcyEndymion004\InventoryShards\Translation;
use IcyEndymion004\InventoryShards\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ShardsCommand extends Command {

    public function __construct()
    {
        parent::__construct("shards", "Displays a list of shards", "/shards", ["listshards"]);
        $this->setPermission("inventory.shards.command.list");
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
        $this->ListShardsForm($sender);
    }

    public function ListShardsForm(Player $player): void {
        $form = new SimpleForm(function(Player $player, $data){
            $shards = array_keys(Loader::getShardsFile()->get("shards"));
            foreach($shards as $key => $shard){
                $totalint = $key;
                if ($data !== null) {
                    if ($data === ($totalint)) {
                        $this->ShardForm($player, $shard);
                        return;
                    }
                }
            }
        });
        $title = Translation::getMessage("List-Shard-UI-Name");
        $form->setTitle($title);
        $list = array_keys(Loader::getShardsFile()->get("shards"));
        foreach($list as $key => $shard){
            $form->addButton(Translation::getMessage("List-Shard-UI-Button-Format", ["{shard}" => $shard]));
        }
        $shardclose = Translation::getMessage("List-Shard-UI-Close-Button");
        $form->addButton($shardclose);
        $player->sendForm($form);
    }

    public function ShardForm(Player $player, string $shard_name): void {
        $form = new SimpleForm(function(Player $player, $data) use ($shard_name){
            if ($data !== null) {
                switch ($data){
                    case 0:
                        $item = Utils::getDisplayShard($shard_name);
                        $player->getInventory()->addItem($item);
                        Translation::sendType("Give-Shard", $player, ["{shard}" => $shard_name, ]);
                        break;
                    case 1:
                        Utils::Preview_Shard_Gui($player, $shard_name);
                        break;
                    case 2:
                        Utils::Edit_Shard_Gui($player, $shard_name);
                        break;
                }
            }
        });
        $form->setTitle(TextFormat::RED . $shard_name);
        $form->addButton(TextFormat::GREEN . "Claim Shard");
        $form->addButton(TextFormat::AQUA . "Preview Shard");
        $form->addButton(TextFormat::BLUE . "Edit Shard");
        $form->addButton(TextFormat::RED . "Close");
        $player->sendForm($form);
    }
}