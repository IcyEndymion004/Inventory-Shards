<?php

namespace Nicky\GkitShards;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase implements Listener {

    /**
     * @var Config
     */
    private $shardData;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->saveDefaultConfig();
        $this->shardData = new Config($this->getDataFolder() . "sharddata.yml", Config::YAML);
    }

    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($item->getNamedTag()->hasTag("invdata") && $item->getId() === ItemIds::HEART_OF_THE_SEA){
            $val = $item->getNamedTag()->getTag("invdata")->getValue();
            $contents = $this->getInvContents($val);
            if($player->getInventory()->firstEmpty() === -1) {
                $player->sendMessage(TextFormat::RED . "Your inventory doesn't have room!");
                return;
            }
            foreach($contents as $content){
                $player->getInventory()->addItem($content);
            }
            self::pop($player);
        }
    }

    public static function pop(Player $player): void{
        $index = $player->getInventory()->getHeldItemIndex();
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItem($index, $item->setCount($item->getCount() - 0.5));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        $types = $this->getConfig()->get("type-shards");
        if($command->getName() === "setshardinv"){
            if(!$sender instanceof Player) return false;
            if(!isset($types[$args[0]])){
                $sender->sendMessage(TextFormat::RED . $args[0] . " does not exist.");
                return false;
            }
            $sender->sendMessage(TextFormat::GREEN . "Set your inventory to the shard type: " . TextFormat::YELLOW . $args[0]);
            $this->setContentsToFile($args[0], $sender->getInventory()->getContents());
            $sender->getInventory()->clearAll();
        }
        if($command->getName() === "giveshard"){
            if(!$sender instanceof Player) return false;
            if(!isset($types[$args[0]])){
                $sender->sendMessage(TextFormat::RED . $args[0] . " does not exist.");
                return false;
            }
            $item = ItemFactory::get(ItemIds::HEART_OF_THE_SEA);
            $item->setCustomName($this->getConfig()->get("type-shards")[$args[0]]["item-name"]);
            $item->setLore($this->getConfig()->get("type-shards")[$args[0]]["item-lore"]);
            $item->setNamedTagEntry(new StringTag("invdata", $args[0]));
            $item->setNamedTagEntry(new ListTag("ench", []));
            $sender->getInventory()->addItem($item);
            $sender->sendMessage(TextFormat::GREEN . "Gave you a GOD shard!");
        }
        return true;
    }

    public function getShardData(): Config{
        return $this->shardData;
    }

    public function setContentsToFile(string $shard, array $contents): void{
        foreach($contents as $key => $value){
            /** @var Item $value */
            $contents[$key] = $value->jsonSerialize();
        }
        $this->getShardData()->set($shard, $contents);
        $this->getShardData()->save();
    }

    /**
     * Set contents
     * @param string $shard
     * @return array
     */
    public function getInvContents(string $shard): array{
        $data = $this->getShardData()->get($shard);
        foreach($data as $key => $value){
            $data[$key] = Item::jsonDeserialize($value);
        }
        return $data;
    }

}