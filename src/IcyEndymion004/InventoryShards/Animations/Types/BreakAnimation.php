<?php

namespace IcyEndymion004\InventoryShards\Animations\Types;

use IcyEndymion004\InventoryShards\Animations\BaseAnimation;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\AnvilBreakSound;

class BreakAnimation extends BaseAnimation {

    protected int $duration = 1;

    public function FinalAnimation(Player $player): void
    {
        $player->getWorld()->addParticle($player->getPosition()->add(0, 1, 0), new BlockBreakParticle(VanillaBlocks::STONE()));
        $player->broadcastSound(new AnvilBreakSound());
    }

    public function Animate(Player $player): void
    {
        // TODO: Implement Animate() method.
    }

    public function CleanUp(Player $plauer): void
    {
        // TODO: Implement CleanUp() method.
    }
}