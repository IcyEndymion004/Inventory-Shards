<?php

namespace IcyEndymion004\GkitShards;

use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\data\bedrock\EnchantmentIdMap;

class Loader extends PluginBase implements Listener {

    /** @var Config */
    protected Config $shardData;

    protected function onEnable(): void{
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
		    if (version_compare("0.0.9", $this->getConfig()->get("config-version"))) {
            $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
			      $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
			      rename($this->getDataFolder()."config.yml", $this->getDataFolder()."config_old.yml");
			      $this->saveResource("config.yml");
			      return;
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        EnchantmentIdMap::getInstance()->register(-1, new Enchantment(-1, "Glow", 1, ItemFlags::ALL, ItemFlags::NONE));
    }

    public function onItemUse(PlayerItemUseEvent $event): void{
        $NoRoomMessage = $this->getConfig()->get("NoRoomMessage");
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tagcheck = $item->getNamedTag();
        if($tagcheck->getTag("invdata") !== null && $tagcheck->getTag("ShardTagVerifyed") !== null){
            $val = $item->getNamedTag()->getTag("invdata")->getValue();
            $contents = $this->getInvContents($val);
            if($player->getInventory()->firstEmpty() === -1) {
                $player->sendMessage($NoRoomMessage);
                return;
            }
            foreach($contents as $content){
                $player->getInventory()->addItem($content);
            }
            $item = $player->getInventory()->getItemInHand();
            $count = $item->getCount() - 1;
            $player->getInventory()->setItemInHand($count <= 0 ? VanillaBlocks::AIR()->asItem() : $item->setCount($count));
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!isset($args[0])) return false;
        $types = $this->getConfig()->get("type-shards");
        if(!isset($types[$args[0]])){
            $sender->sendMessage(str_replace(["{shard}", "{player}"], [$args[0], $sender->getName()],$this->getConfig()->get("InvalidShard")));
            return false;
        }
        $shardname = $args[0]; //The Thing You Entered for the shard is not a vaild shard

        $NoShardexists = str_replace("{shard}", $shardname, $this->getConfig()->get("NoShardexistsmsg"));
        $NoShardexists = str_replace("{player}", $sender->getName(), $NoShardexists);
        $SetInvasShard = str_replace("{shard}", $shardname, $this->getConfig()->get("SetInvasShardmsg"));
        $SetInvasShard = str_replace("{player}", $sender->getName(), $SetInvasShard);
        $GivenShard = str_replace("{shard}", $shardname, $this->getConfig()->get("GivenShardMsg"));
        $GivenShard = str_replace("{player}", $sender->getName(), $GivenShard);

        if($command->getName() === "setshardinv"){
            if(!$sender instanceof Player) return false;
            if(!isset($types[$args[0]])){
                $sender->sendMessage($NoShardexists);
                return false;
            }
            $sender->sendMessage($SetInvasShard);
            $this->setContentsToFile($args[0], $sender->getInventory()->getContents());
        }
        if($command->getName() === "seeshardinfo"){
            if(!$sender instanceof Player) return false;
            if(!isset($types[$args[0]])){
                $sender->sendMessage($NoShardexists);
                return false;
            }
            $guiname = str_replace("{shard}", $shardname, $this->getConfig()->get("GUIname"));

            $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
            $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult{
                return $transaction->discard();
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
                $sender->sendMessage($NoShardexists);
                return false;
            }
            $ShardItemId = $this->getConfig()->get("shardID");
            $ShardItemMeta = $this->getConfig()->get("shardMeta");
            $item = ItemFactory::getInstance()->get($ShardItemId, $ShardItemMeta, 1);
            $item->setCustomName($this->getConfig()->get("type-shards")[$args[0]]["item-name"]);
            $item->setLore($this->getConfig()->get("type-shards")[$args[0]]["item-lore"]);
            $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1)));
            $item->setNamedTag($item->getNamedTag()->setString("invdata", $args[0])->setString("ShardTagVerifyed", $args[0]));
            $sender->getInventory()->addItem($item);
            $sender->sendMessage($GivenShard);
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
