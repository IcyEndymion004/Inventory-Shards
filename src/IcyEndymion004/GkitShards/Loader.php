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
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\InvMenuHandler;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\InvMenu;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\transaction\InvMenuTransaction;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\inventory\Inventory;
use IcyEndymion004\GkitShards\libs\jojoe77777\FormAPI\SimpleForm;
use pocketmine\Server;
use Stringable;

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
                @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        if (!$this->getConfig()->exists("config-version")) {
			      $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
			      $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
			      rename($this->getDataFolder()."config.yml", $this->getDataFolder()."config_old.yml");
			      $this->saveResource("config.yml");
			      return;
		    }
		    if (version_compare("1.0.0", $this->getConfig()->get("config-version"))) {
            $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
			      $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
			      rename($this->getDataFolder()."config.yml", $this->getDataFolder()."config_old.yml");
			      $this->saveResource("config.yml");
			      return;
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
    }

    public function onInteract(PlayerInteractEvent $event): void{
        $NoRoomMessage = $this->getConfig()->get("NoRoomMessage");
        $NoRoomMessagetype = $this->getConfig()->get("typeNoRoomMessage");
        $poorboitype = $this->getConfig()->get("typeToPoor");
        $poorboi = $this->getConfig()->get("ToPoor");
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tagcheck = $item->getNamedTag();
        if($tagcheck->hasTag("invdata") && $tagcheck->hasTag("ShardTagVerifyed")){
            $val = $item->getNamedTag()->getTag("invdata")->getValue();
            $contents = $this->getInvContents($val);
            if($player->getInventory()->firstEmpty() === -1) {
            if($NoRoomMessagetype === 1){
            $player->sendMessage($NoRoomMessage);
            return;
            }
            if($NoRoomMessagetype === 2){
            $player->sendPopup($NoRoomMessage);
            return;
            }
            if($NoRoomMessagetype === 3)
            $player->sendTitle($NoRoomMessage);
            return;
            }  
            
            $econ = $this->getConfig()->get("EconamyEnabled");
            if($econ === true){
            $deduction = $item->getNamedTag()->getTag("claimcost")->getValue();
            $poorboi = str_replace("{cost}", $deduction, $this->getConfig()->get("ToPoor"));
            $econbase = Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI")->getInstance();
            if($econbase->myMoney($player) > $deduction){
            $econbase->reduceMoney($player, $deduction);
            }else{
            if($poorboitype === 1){
            $player->sendMessage($poorboi);
            }
            if($poorboitype === 2){
            $player->sendPopup($poorboi);
            }
            if($poorboitype === 3){
            $player->sendTitle($poorboi);
            }  
            }
            foreach($contents as $content){
                $player->getInventory()->addItem($content);
            }
            self::pop($player);
            }
        }
    }

    public static function pop(Player $player): void{
        $index = $player->getInventory()->getHeldItemIndex();
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItem($index, $item->setCount($item->getCount() - 0.5));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {  
        if($command->getName() === "shardlist"){
            if(!$sender instanceof Player) return false;
            $this->shardslist($sender);
        }
        if(!isset($args[0])) return false;
        $types = $this->getConfig()->get("type-shards");
        if(!isset($types[$args[0]])){ 
        $invaildshard = str_replace("{shard}", $args[0], $this->getConfig()->get("InvaidShard"));
        $invaildshard = str_replace("{player}", $sender->getName(), $invaildshard);
        $sender->sendMessage($invaildshard);
        return false;
        }
        $shardname = $args[0]; //The Thing You Entered for the shard is not a vaild shard
        $NoShardexists = str_replace("{shard}", $shardname, $this->getConfig()->get("NoShardexistsmsg"));
        $NoShardexists = str_replace("{player}", $sender->getName(), $NoShardexists);
        $SetInvasShard = str_replace("{shard}", $shardname, $this->getConfig()->get("SetInvasShardmsg"));
        $SetInvasShard = str_replace("{player}", $sender->getName(), $SetInvasShard);
        $GivenShard = str_replace("{shard}", $shardname, $this->getConfig()->get("GivenShardMsg"));
        $GivenShard = str_replace("{player}", $sender->getName(), $GivenShard);

        $msgtypeNoShardExists = $this->getConfig()->get("NoShardExistsType");
        $msgtypeSetInvShard = $this->getConfig()->get("SetinvshardType");
        $msgtypegiveshard = $this->getConfig()->get("giveshardType");
        
        if($command->getName() === "setshardinv"){
            if(!$sender instanceof Player) return false;
            if(!isset($types[$args[0]])){
                if($msgtypeNoShardExists === 1){
                $sender->sendMessage($NoShardexists);
                return false;
                }
                if($msgtypeNoShardExists === 2){
                $sender->sendPopup($NoShardexists);
                return false;
                }
                if($msgtypeNoShardExists === 3){
                $sender->sendTitle($NoShardexists);
                return false;
                }  
            }
            if($msgtypeSetInvShard === 1){
                $sender->sendMessage($SetInvasShard);
                }
                if($msgtypeSetInvShard === 2){
                $sender->sendPopup($SetInvasShard);
                }
                if($msgtypeSetInvShard === 3){
                $sender->sendTitle($SetInvasShard);
                } 
            $this->setContentsToFile($args[0], $sender->getInventory()->getContents());
        }

        if($command->getName() === "seeshardinfo"){
            if(!$sender instanceof Player) return false;
            if(!isset($types[$args[0]])){
                if($msgtypeNoShardExists === 1){
                $sender->sendMessage($NoShardexists);
                return false;
                }
                if($msgtypeNoShardExists === 2){
                $sender->sendPopup($NoShardexists);
                return false;
                }
                if($msgtypeNoShardExists === 3){
                $sender->sendTitle($NoShardexists);
                return false;
                }  
            }
            $guiname = str_replace("{shard}", $shardname, $this->getConfig()->get("GUIname"));
    
            $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $menu->setListener(InvMenu::readonly());
            $menu->setName($guiname);
            $inv = $menu->getInventory();
            $val = $args[0];
            $contents = $this->getInvContents($val);
            foreach($contents as $item) {
                $inv->addItem($item);
            }
            $menu->send($sender);   
            }
            if($command->getName() === "editshard"){
                if(!$sender instanceof Player) return false;
                if(!isset($types[$args[0]])){
                    if($msgtypeNoShardExists === 1){
                    $sender->sendMessage($NoShardexists);
                    return false;
                    }
                    if($msgtypeNoShardExists === 2){
                    $sender->sendPopup($NoShardexists);
                    return false;
                    }
                    if($msgtypeNoShardExists === 3){
                    $sender->sendTitle($NoShardexists);
                    return false;
                    }  
                }
                $guiname = str_replace("{shard}", $shardname, $this->getConfig()->get("GUIname"));
                $this->getConfig()->set(strval($sender->getName()));   
                $this->getConfig()->set(strval($sender->getName()), strval($shardname));
        
                $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void{
                    $contents = $inventory->getContents();
                    foreach($contents as $key => $value){
                        /** @var Item $value */
                        $contents[$key] = $value->jsonSerialize();
                    }
                    $msgtypeeditshardcomplete = $this->getConfig()->get("typeEditShard");
                    $shardname = $this->getConfig()->get((strval($player->getName())));
                    $editshardcomplete = str_replace("{shard}", $shardname, $this->getConfig()->get("ShardEditComplete"));
                    $editshardcomplete = str_replace("{player}", $player->getName(), $editshardcomplete);
                    if($msgtypeeditshardcomplete === 1){
                        $player->sendMessage($editshardcomplete);
                        }
                        if($msgtypeeditshardcomplete === 2){
                        $player->sendPopup($editshardcomplete);
                        }
                        if($msgtypeeditshardcomplete === 3){
                        $player->sendTitle($editshardcomplete);
                        }  
                    $this->getShardData()->set($shardname, $contents);
                    $this->getShardData()->save();
                    $this->getConfig()->remove((strval($player->getName())));
                });
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
            if(!isset($types[$args[0]])){
                if($msgtypeNoShardExists === 1){
                $sender->sendMessage($NoShardexists);
                return false;
                }
                if($msgtypeNoShardExists === 2){
                $sender->sendPopup($NoShardexists);
                return false;
                }
                if($msgtypeNoShardExists === 3){
                $sender->sendTitle($NoShardexists);
                return false;
                }  
            }
            $ShardItemId = ($this->getConfig()->get("type-shards")[$args[0]]["shardID"]);
            $ShardItemMeta = ($this->getConfig()->get("type-shards")[$args[0]]["ShardMeta"]);
            $item = ItemFactory::get($ShardItemId, $ShardItemMeta, 1);
            $item->setCustomName($this->getConfig()->get("type-shards")[$args[0]]["item-name"]);
            $item->setLore($this->getConfig()->get("type-shards")[$args[0]]["item-lore"]);
            $item->setNamedTagEntry(new StringTag("invdata", $args[0]));
            $claimcost = $this->getConfig()->get("type-shards")[$args[0]]["claimcost"];
            $item->setNamedTagEntry(new StringTag("claimcost", $claimcost));
            $item->setNamedTagEntry(new StringTag("invdata", $args[0]));
            $item->setNamedTagEntry(new ListTag("ench", []));
            $item->setNamedTagEntry(new StringTag("ShardTagVerifyed", $args[0]));
            $sender->getInventory()->addItem($item);
            if($msgtypegiveshard === 1){
                $sender->sendMessage($GivenShard);
                }
                if($msgtypegiveshard === 2){
                $sender->sendPopup($GivenShard);
                }
                if($msgtypegiveshard === 3){
                $sender->sendTitle($GivenShard);
                } 
        }
        return true;
    }

    public function shardslist($sender)
    {
        $list = array_keys($this->getConfig()->get("type-shards") );
        foreach($list as $key => $value){
        $totalint = $key;
        }        
            $form = new SimpleForm(function(Player $player, $data){
            $list = array_keys($this->getConfig()->get("type-shards") );
            foreach($list as $key => $value){
             
            $totalint = $key;
            if ($data !== null) {
                if ($data === ($totalint)) {
                    $player->getServer()->dispatchCommand($player, "giveshard $value");
                    return;
                }
                }
            }
        });
        $shardlist = $this->getConfig()->get("UIName");    
        $form->setTitle($shardlist);
        foreach($list as $key => $value){
            $FormButtonFormat = str_replace("{shard}", strval($value), $this->getConfig()->get("UIButtonFormat"));
            $form->addButton($FormButtonFormat);
        }
        $shardclose = $this->getConfig()->get("UICloseButton");
        $form->addButton($shardclose);
        $sender->sendForm($form);
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
            ($data)[$key] = Item::jsonDeserialize($value);
        }
        return $data;
}
}
