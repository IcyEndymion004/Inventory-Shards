<?php

namespace IcyEndymion004\InventoryShards;

use IcyEndymion004\InventoryShards\Animations\AnimationHandler;
use IcyEndymion004\InventoryShards\Animations\BaseAnimation;
use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\InvMenu;
use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\type\InvMenuTypeIds;
use JsonException;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Utils {

    /**
     * Returns an array With all the items in the shard.
     * @param string $shard THIS IS CASE SENSITIVE
     * @return array
     */
    public static function getShardContents(string $shard): array {
        $data = Loader::getShardDataFile()->get($shard, []);
        foreach ($data as $key => $value){
            $data[$key] = Item::jsonDeserialize($value);
        }
        return $data;
    }

    /**
     * Sets the data of a shard to the contents of an array
     * @param string $shard THIS IS CASE SENSITIVE
     * @param array $content
     * @return void
     * @throws JsonException
     */
    public static function setShardContents(string $shard, array $content): void {
        foreach ($content as $key => $value){
            if($value instanceof Item){
                $content[$key] = $value->jsonSerialize();
            }
        }
        Loader::getShardDataFile()->set($shard, $content);
        Loader::getShardDataFile()->save();
    }

    /**
     * @param string $shard_name
     * @return array|null
     * Returns Null if the given shard is invalid, Other than that returns all the data of the shard in an array
     */
    public static function getShard(string $shard_name): array|null {
        $shard = Loader::getShardsFile()->get("shards")[$shard_name];
        if(is_bool($shard)){
            return null;
        }
        return $shard;
    }

    /**
     * @param string $shard_name
     * @return Item|null
     * Returns the given Shard in its item form, or returns null if the name is not valid
     */
    public static function getDisplayShard(string $shard_name): Item|null {
        $shard = Loader::getShardsFile()->get("shards")[$shard_name]["item-form"];
        if(is_bool($shard)){
            return null;
        }
        $item = ItemFactory::getInstance()->get($shard["id"], $shard["meta"]);
        $item->setCustomName($shard["display-name"]);
        $item->setLore($shard["lore"]);
        $item->getNamedTag()->setTag("ShardData", new StringTag($shard_name));
        $item->getNamedTag()->setTag("ShardVerified", new StringTag($shard_name));
        $item->getNamedTag()->setTag("Shard-Claim-Cost", new FloatTag(self::getShard($shard_name)["claimCost"]));
        return $item;
    }

    /**
     * @param string $shard_name
     * @return int|null
     * Returns the claim price of the given shard, or returns null if the name is not valid
     */
    public static function getShardClaim(string $shard_name): int|null {
        $shard = Loader::getShardsFile()->get("shards")[$shard_name]["claimCost"];
        if(is_bool($shard)){
            return null;
        }
        return $shard;
    }

    /**
     * @param string $shard_name
     * @return string|null
     * Returns the Display name of the given shard, or returns null if the name is not valid
     */
    public static function getShardDisplayName(string $shard_name): string|null {
        $shard = Loader::getShardsFile()->get("shards")[$shard_name]["name"];
        if(is_bool($shard)){
            return null;
        }
        return $shard;
    }

    /**
     * @param string $shard_name
     * @return BaseAnimation|null
     * Returns the Animation a shard has
     */
    public static function getAnimationFromShard(string $shard_name): BaseAnimation|null {
        $shard = Loader::getShardsFile()->get("shards")[$shard_name]["animation"];
        if(is_bool($shard)){
            return null;
        }
        return AnimationHandler::typeToAnimation($shard);
    }

    /**
     * @return bool
     */
    public static function Editing(): bool {
        $value = Loader::getInstance()->getConfig()->get("Shard_Config")["Editing"];
        if (!is_bool($value)){
            return true;
        }
        return $value;
    }

    /**
     * @return bool
     */
    public static function Shift_Preview(): bool {
        $value = Loader::getInstance()->getConfig()->get("Shard_Config")["Shift_Preview"];
        if (!is_bool($value)){
            return true;
        }
        return $value;
    }

    /**
     * @return bool
     */
    public static function Creation(): bool {
        $value = Loader::getInstance()->getConfig()->get("Shard_Config")["Creation"];
        if (!is_bool($value)){
            return true;
        }
        return $value;
    }

    /**
     * @param Player $player
     * @param string $shard_name
     * @return void
     * Sends the GUI for Previewing a shard
     */
    public static function Preview_Shard_Gui(Player $player, string $shard_name): void {
        $items = self::getShardContents($shard_name);
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName(Utils::getShardDisplayName($shard_name) . " " . TextFormat::GRAY . "Preview");
        $menu->setListener(InvMenu::readonly());
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use ($shard_name): void {
            Translation::sendType("Shard-Preview-Finish", $player, ["{shard}" => $shard_name]);
        });
        $inv = $menu->getInventory();
        foreach($items as $item){
            $inv->addItem($item);
        }
        for ($i = 0; ; ){
            if ($i > 53){
                break;
            }
            if ($i > 35){
                $inv->setItem($i, VanillaBlocks::IRON_BARS()->asItem()->setCustomName(" "));
            }
            $i += 1;
        }
        $menu->send($player);
    }

    public static function Edit_Shard_Gui(Player $player, string $shard_name): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::GRAY . "Editing " . TextFormat::GREEN . $shard_name);
        $menu->setListener(InvMenu::readonly());
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use ($shard_name): void {
            $contents = $inventory->getContents();
            $items = [];
            foreach($contents as $item){
                if($item->getNamedTag()->getTag("InventoryShards") !== null){
                    $items[] = $item;
                }
            }
            self::setShardContents($shard_name, $items);
            Translation::sendType("Edit-Shard", $player, ["{shard}" => $shard_name]);
        });
        $inv = $menu->getInventory();
        $contents = self::getShardContents($shard_name);
        foreach ($contents as $item){
            $inv->addItem($item);
        }
        for ($i = 0; ; ){
            if ($i > 53){
                break;
            }
            if ($i > 35){
                $inv->setItem($i, VanillaBlocks::IRON_BARS()->asItem()->setCustomName(" "));
            }
            $i += 1;
        }
        $inv->setItem(53, VanillaBlocks::GREEN_GLAZED_TERRACOTTA()->asItem()->setCustomName(TextFormat::GREEN . "Close the Inventory To Save!"));
        $menu->send($player);
    }
}