<?php

namespace IcyEndymion004\InventoryShards\Commands;

use IcyEndymion004\InventoryShards\Lib\jojoe77777\FormAPI\CustomForm;
use IcyEndymion004\InventoryShards\Lib\jojoe77777\FormAPI\SimpleForm;
use IcyEndymion004\InventoryShards\Loader;
use IcyEndymion004\InventoryShards\Translation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditMessagesCommand extends Command {

    public function __construct()
    {
        parent::__construct("editconfigm", "Allows Editing of Config Messages from in game", "/editconfigm", ["editmessages"]);
        $this->setPermission("inventory.shards.command.editmessage");
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
        $this->MessageEditForm($sender);
    }

    public function MessageEditForm(Player $player): void {
        $form = new SimpleForm(function(Player $player, $data){
            if ($data !== null){
                $array = [];
                $int = 0;
                foreach (Loader::getMessageFile()->get("messages") as $key => $messageData){
                    $array[$int] = $key;
                    $int += 1;
                }
                if (is_string($array[$data])){
                    $this->ChangesForm($player, $array[$data]);
                    return;
                }
            }
        });
        $form->setTitle(TextFormat::BLUE . "Editing Messages");
        foreach (Loader::getMessageFile()->get("messages") as $key => $data){
            $form->addButton(TextFormat::GREEN . $key);
        }
        $form->addButton(TextFormat::RED . "Close");
        $player->sendForm($form);
    }

    public function ChangesForm(Player $player, string $key): void {
        $form = new CustomForm(function (Player $player, $data) use ($key){
            if ($data !== null){
                $new = $data[2];
                Loader::getMessageFile()->setNested("messages." . $key . ".message", $new);
                Loader::getMessageFile()->save();
                Translation::sendType("Shard-Message-Update", $player, ["{config}" => $key]);
            }
        });
        $form->setTitle(TextFormat::GREEN . $key . " Message");
        $form->addLabel(TextFormat::GREEN . "Current Message");
        $form->addLabel(TextFormat::GRAY . Translation::getMessage($key));
        $form->addInput(TextFormat::GREEN . "New Message");
        $player->sendForm($form);
    }

}