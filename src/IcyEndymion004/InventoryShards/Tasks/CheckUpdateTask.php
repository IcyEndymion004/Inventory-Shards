<?php

namespace IcyEndymion004\InventoryShards\Tasks;

use IcyEndymion004\InventoryShards\Loader;
use pocketmine\plugin\ApiVersion;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

/**
 * Thx Pig for the code
 * "Borrowed" from https://github.com/DaPigGuy/libPiggyUpdateChecker/blob/master/src/DaPigGuy/libPiggyUpdateChecker/tasks/CheckUpdatesTask.php
 * All rights of it go to him for it <3
 */
class CheckUpdateTask extends AsyncTask {

    public function __construct()
    {
    }

    public function onRun(): void
    {
        $result = Internet::getURL("https://poggit.pmmp.io/releases.min.json?name=" . "InventoryShards", 10, [], $error);
        $this->setResult([$result?->getBody(), $error]);
    }

    public function onCompletion(): void
    {
        $logger = Server::getInstance()->getLogger();
        [$body, $error] = $this->getResult();
        if ($error) {
            $logger->warning("Auto-update check failed.");
            $logger->debug($error);
        } else {
            $versions = json_decode($body, true);
            if ($versions) foreach ($versions as $version) {
                if (version_compare(Loader::getVersion(), $version["version"]) === -1) {
                    if (ApiVersion::isCompatible(Server::getInstance()->getApiVersion(), $version["api"][0])) {
                        $logger->notice("Inventory Shards" . " v" . $version["version"] . " is available for download at " . $version["artifact_url"] . "/" . "InventoryShards" . ".phar");
                        break;
                    }
                }
            }
        }
    }
}