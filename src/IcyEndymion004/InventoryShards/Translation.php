<?php

namespace IcyEndymion004\InventoryShards;

use pocketmine\player\Player;

final class Translation {

    public static function getMessage(string $configKey, null|array $vars = []): string {
        $messages = Loader::getMessageFile();
        $message = $messages->get("messages")[$configKey]["message"];
        foreach ($vars as $name => $value){
            $message = str_replace($name, $value, $message);
        }
        return $message;
    }

    public static function sendType(string $configKey, Player $player, null|array $vars = []): void {
        $message = Translation::getMessage($configKey);
        $defaultVars = ["{sender}" => $player->getName(), "{player}" => $player->getName()];
        $vars = array_merge($defaultVars, $vars);
        foreach ($vars as $name => $value){
            $message = str_replace($name, $value, $message);
        }
        $type = Loader::getMessageFile()->get("messages")[$configKey]["type"];
        switch ($type){
            case 1:
                $player->sendMessage($message);
                break;
            case 2:
                $player->sendPopup($message);
                break;
            case 3:
                $player->sendTitle($message);
                break;
            case 4:
                $player->sendActionBarMessage($message);
                break;
            case 5:
                $player->sendTitle("", $message);
                break;
            case 6:
                $player->sendTip($message);
                break;
        }
    }
}