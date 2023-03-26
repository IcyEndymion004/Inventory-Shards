<?php

namespace IcyEndymion004\InventoryShards\Animations;

use IcyEndymion004\InventoryShards\Translation;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class Animator extends Task {

    protected int $timer = 1;

    protected BaseAnimation $animation;

    protected Player $player;

    protected array $rewards;

    public function __construct(BaseAnimation $animation, Player $player, array $rewards)
    {
        $this->animation = $animation;
        $this->player = $player;
        $this->rewards = $rewards;
    }

    public function onRun(): void
    {
        $player = $this->player;
        $loot = $this->rewards;
        if ($this->timer > $this->getAnimation()->getDuration()){
            foreach ($loot as $item){
                if ($player->getInventory()->canAddItem($item)){
                    $player->getInventory()->addItem($item);
                }else{
                    $player->dropItem($item);
                }
            }
            Translation::sendType("Shard-Claim", $player);
            $this->getAnimation()->CleanUp($player);
            $this->getHandler()->cancel();
        }
        if($this->timer === $this->getAnimation()->getDuration()){
            $this->getAnimation()->FinalAnimation($player);
        }
        if ($this->timer < $this->getAnimation()->getDuration()){
            $this->getAnimation()->Animate($player);
        }
        $this->timer += 1;
    }

    /**
     * @return BaseAnimation
     */
    public function getAnimation(): BaseAnimation
    {
        return $this->animation;
    }
}