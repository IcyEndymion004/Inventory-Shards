<?php

namespace IcyEndymion004\InventoryShards\Animations\Types;

use IcyEndymion004\InventoryShards\Animations\BaseAnimation;
use pocketmine\player\Player;

class InstantAnimation extends BaseAnimation {

    protected int $duration = 0;

    public function Animate(Player $player): void
    {
        // TODO: Implement Animate() method.
    }

    public function FinalAnimation(Player $player): void
    {
        // TODO: Implement FinalAnimation() method.
    }

    public function CleanUp(Player $plauer): void
    {
        // TODO: Implement CleanUp() method.
    }
}