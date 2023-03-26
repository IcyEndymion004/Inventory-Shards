<?php

namespace IcyEndymion004\InventoryShards;

use IcyEndymion004\InventoryShards\Commands\EditMessagesCommand;
use IcyEndymion004\InventoryShards\Commands\EditShardCommand;
use IcyEndymion004\InventoryShards\Commands\EditShardContentsCommand;
use IcyEndymion004\InventoryShards\Commands\GiveShardCommand;
use IcyEndymion004\InventoryShards\Commands\PreviewShardCommand;
use IcyEndymion004\InventoryShards\Commands\SetShardContentsCommand;
use IcyEndymion004\InventoryShards\Commands\ShardsCommand;
use IcyEndymion004\InventoryShards\Economy\EconomyManager;
use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\InvMenuHandler;
use IcyEndymion004\InventoryShards\Tasks\CheckUpdateTask;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class Loader extends PluginBase {

    protected static Config $messageFile;

    protected static Config $shardsFile;

    protected static Config $shardDataFile;

    protected static string $version = "2.0.0";

    protected static self $instance;

    /**
     * @throws \Exception
     */
    protected function onEnable(): void
    {
        self::$instance = $this;
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        $this->saveResource("Messages.yml");
        $this->saveResource("Shards.yml");
        self::$messageFile = new Config($this->getDataFolder() . "Messages.yml", 2);
        self::$shardsFile = new Config($this->getDataFolder() . "Shards.yml", 2);
        self::$shardDataFile = new Config($this->getDataFolder() . "ShardData.yml", 2);
        Server::getInstance()->getAsyncPool()->submitTask(new CheckUpdateTask());
        if (!$this->getConfig()->exists("config-version")) {
            $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
            $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
            rename($this->getDataFolder()."config.yml", $this->getDataFolder() . "config_old.yml");
            $this->saveResource("config.yml");
            return;
        }
        if (version_compare("2.0.0", $this->getConfig()->get("config-version"))) {
            $this->getLogger()->notice("§eYour configuration file is from another version. Updating the Config...");
            $this->getLogger()->notice("§eThe old configuration file can be found at config_old.yml");
            rename($this->getDataFolder()."config.yml", $this->getDataFolder() . "config_old.yml");
            $this->saveResource("config.yml");
            return;
        }
        EconomyManager::init(); //Handles all the Economy stuff
        Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
        $commands = [
            new ShardsCommand(),
            new SetShardContentsCommand(),
            new PreviewShardCommand(),
            new GiveShardCommand(),
            new EditMessagesCommand(),
            new EditShardContentsCommand(),
            new EditShardCommand()
        ];
        Server::getInstance()->getCommandMap()->registerAll("I.S", $commands);
    }

    public static function getMessageFile(): Config{
        return self::$messageFile;
    }

    public static function getShardsFile(): Config{
        return self::$shardsFile;
    }

    public static function getShardDataFile(): Config{
        return self::$shardDataFile;
    }

    public static function getVersion(): string {
        return self::$version;
    }

    public static function getInstance(): self {
        return self::$instance;
    }
}