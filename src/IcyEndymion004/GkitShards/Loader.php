<?php

namespace IcyEndymion004\GkitShards;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\InvMenu;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\InvMenuHandler;
use Stringable;

class Loader extends PluginBase implements Listener {

    /** @var Config */
    private $shardData;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->shardData = new Config($this->getDataFolder() . "sharddata.yml", Config::YAML);  
                @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        if(!$this->getConfig()->exists("config-version")){
			      $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
			      $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
			      rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
			      $this->saveResource("config.yml");
			      return;
		    }
		    if(version_compare("0.0.9", $this->getConfig()->get("config-version"))){
            $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
			      $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
			      rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
			      $this->saveResource("config.yml");
			      return;
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
    }

	/**
	 * @param PlayerInteractEvent $event
	 */
    public function onInteract(PlayerInteractEvent $event): void {
        $noRoomMessage = $this->getConfig()->get("NoRoomMessage");
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tagcheck = $item->getNamedTag();
        if($tagcheck->hasTag("invdata") && $tagcheck->hasTag("ShardTagVerifyed")){
            $val = $item->getNamedTag()->getTag("invdata")->getValue();
            $contents = $this->getInvContents($val);
            if($player->getInventory()->firstEmpty() === -1) {
                $player->sendMessage($noRoomMessage);
                return;
            }
            foreach($contents as $content){
                $player->getInventory()->addItem($content);
            }
            self::pop($player);
        }
    }

    public static function pop(Player $player): void {
        $index = $player->getInventory()->getHeldItemIndex();
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItem($index, $item->setCount($item->getCount() - 0.5));
    }

	/**
	 * @param CommandSender $sender
	 * @param Command $command
	 * @param string $label
	 * @param array $args
	 * @return bool
	 */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {  



        if(!isset($args[0])) return false;
        $types = $this->getConfig()->get("type-shards");
        if(!isset($types($args[0]))){ 
        $invaildShard = str_replace("{shard}", $args[0], $this->getConfig()->get("InvalidShard"));
        $invaildShard = str_replace("{player}", $sender->getName(), $invaildShard);
        $sender->sendMessage($invaildShard);
        return false;
        }

        $shardName = $args[0]; //The Thing You Entered for the shard is not a vaild shard

        $noShardExists = str_replace("{shard}", $shardName, $this->getConfig()->get("NoShardexistsmsg"));
        $noShardExists = str_replace("{player}", $sender->getName(), $noShardExists);
        $setInvAsShard = str_replace("{shard}", $shardName, $this->getConfig()->get("SetInvasShardmsg"));
        $setInvAsShard = str_replace("{player}", $sender->getName(), $setInvAsShard);
        $givenShard = str_replace("{shard}", $shardName, $this->getConfig()->get("GivenShardMsg"));
        $givenShard = str_replace("{player}", $sender->getName(), $givenShard);
        
        if($command->getName() === "setshardinv"){
            if(!$sender instanceof Player) return false;
            if(!isset($types($args[0]))){
                $sender->sendMessage($noShardExists);
                return false;
            }
            $sender->sendMessage($setInvAsShard);
            $this->setContentsToFile($args[0], $sender->getInventory()->getContents());
        }
        if($command->getName() === "seeshardinfo"){
            if(!$sender instanceof Player) return false;
            if(!isset($types($args[0]))){
                $sender->sendMessage($noShardExists);
                return false;
            }
            $guiname = str_replace("{shard}", $shardName, $this->getConfig()->get("GUIname"));
    
            $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $menu->readonly();
            $menu->setName($guiname);
            $inv = $menu->getInventory();
            $val = $args[0];
            $contents = $this->getInvContents($val);
            foreach($contents as $item) {
                $inv->addItem($item);
            }
            $menu->send($sender);   
            }
        if($command->getName() === "giveshard"){
            if(!$sender instanceof Player) return false;
            if(!isset($types($args[0]))){ 
                $sender->sendMessage($noShardExists);
                return false;
            }
            $shardItemId = $this->getConfig()->get("shardID");
            $shardItemMeta = $this->getConfig()->get("shardMeta");
            $item = ItemFactory::get($shardItemId, $shardItemMeta, 1);
            $item->setCustomName($this->getConfig()->get("type-shards")[$args[0]]["item-name"]);
            $item->setLore($this->getConfig()->get("type-shards")[$args[0]]["item-lore"]);
            $item->setNamedTagEntry(new StringTag("invdata", $args[0]));
            $item->setNamedTagEntry(new ListTag("ench", []));
            $item->setNamedTagEntry(new StringTag("ShardTagVerifyed", $args[0]));
            $sender->getInventory()->addItem($item);
            $sender->sendMessage($givenShard);
        }
        return true;
    }

	/**
	 * @return Config
	 */
    public function getShardData(): Config {
        return $this->shardData;
    }

	/**
	 * @param string $shard
	 * @param array $contents
	 */
    public function setContentsToFile(string $shard, array $contents): void {
        foreach($contents as $key => $value){
            /** @var Item $value */
            $contents[$key] = $value->jsonSerialize();
        }
        $this->getShardData()->set($shard, $contents);
        $this->getShardData()->save();
    }

    /**
     * Set contents.
     * @param string $shard
     * @return array
     */
    public function getInvContents(string $shard): array {
        $data = $this->getShardData()->get($shard);
        foreach($data as $key => $value){
            $data[$key] = Item::jsonDeserialize($value);
        }
        return $data;
    }
}
