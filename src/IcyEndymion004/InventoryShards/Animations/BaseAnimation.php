<?php

namespace IcyEndymion004\InventoryShards\Animations;

use pocketmine\player\Player;

abstract class BaseAnimation {

    /**
     * @var int
     * Duration in Animation ticks (5 ticks to be exact)
     */
    protected int $duration;

    /**
     * @return bool|null
     * Before Creating your own animations. please Read the Developer Documentation on the Wiki (https://github.com/IcyEndymion004/Inventory-Shards/wiki)
     */
    public abstract function Animate(Player $player): void;

    /**
     * @return bool|null
     * Before Creating your own animations. please Read the Developer Documentation on the Wiki (https://github.com/IcyEndymion004/Inventory-Shards/wiki)
     */
    public abstract function FinalAnimation(Player $player): void;

    public abstract function CleanUp(Player $plauer): void;

    /**
     * @return int
     */
    public  function getDuration(): int
    {
        return $this->duration;
    }
}