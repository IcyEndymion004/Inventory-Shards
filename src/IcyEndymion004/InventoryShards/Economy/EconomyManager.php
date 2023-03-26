<?php

namespace IcyEndymion004\InventoryShards\Economy;

use IcyEndymion004\InventoryShards\Economy\Currencys\BedrockCurrency;
use IcyEndymion004\InventoryShards\Economy\Currencys\EconomyAPICurrency;
use IcyEndymion004\InventoryShards\Loader;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EconomyManager {

    protected static bool $status = false;

    protected static null|BaseCurrency $currency = null;

    /**
     * @throws \Exception
     */
    public static function init(): void {
        if(Loader::getInstance()->getConfig()->getNested("Economy.Enabled") === true){
            $provider = Loader::getInstance()->getConfig()->getNested("Economy.Provider");
            switch ($provider){
                case "EconomyAPI":
                    if(Server::getInstance()->getPluginManager()->getPlugin("EconomyAPI") instanceof Plugin){
                        self::$currency = new EconomyAPICurrency();
                    }else{
                        throw new \Exception("The given economy does not exist or loaded!");
                    }
                    break;
                case "BedrockEconomy":
                    if(Server::getInstance()->getPluginManager()->getPlugin("BedrockEconomy") instanceof Plugin){
                        self::$currency = new BedrockCurrency();
                    }else{
                        throw new \Exception("The given economy does not exist or loaded!");
                    }
                    break;
                /*
                Disabled Due to me hating Capital, if another dev wants to finish the CapitalCurrency, ill be sure to add it. but it most likely wont be me
                case "Capital":
                    //
                    break;
                */
                default:
                    Loader::getInstance()->getLogger()->critical("No Valid Economy Found, Disabling Inventory Shards!");
                    Server::getInstance()->getPluginManager()->disablePlugin(Loader::getInstance());
            }
        }else{
            Loader::getInstance()->getLogger()->alert(TextFormat::RED . "Economy Is not Enabled. there for anything with economy will not work!");
        }
    }

    /**
     * @return BaseCurrency|null
     */
    public static function getCurrency(): ?BaseCurrency
    {
        return self::$currency;
    }

    public static function isEnabled(): bool {
        return self::$status;
    }
}