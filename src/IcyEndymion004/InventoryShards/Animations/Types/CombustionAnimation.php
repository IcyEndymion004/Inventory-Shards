<?php

namespace IcyEndymion004\InventoryShards\Animations\Types;

use IcyEndymion004\InventoryShards\Animations\BaseAnimation;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\HugeExplodeParticle;

class CombustionAnimation extends BaseAnimation {

    protected int $duration = 5;

    protected int $timer = 1;

    protected Vector3 $pos;

    public function Animate(Player $player): void
    {
        $pos = $player->getPosition()->add(2, 0, 2);
        $this->pos = $pos;
        $player->getWorld()->setBlock($pos, VanillaBlocks::STONE_BRICK_WALL());
        $player->getWorld()->setBlock($pos->add(0, 1, 0), VanillaBlocks::CHEST());
        $packet = BlockEventPacket::create(
            new BlockPosition($pos->getX(), $pos->getY() + 1, $pos->getZ()),
            1,
            0
        );
        $player->getNetworkSession()->sendDataPacket($packet);
        $z = mt_rand(-5, 5);
        $x = mt_rand(-5, 5);
        $y = mt_rand(1, 3);
        $player->getWorld()->addParticle($pos->add($x, $y, $z), new HugeExplodeParticle());
        $this->timer += 1;
    }

    public function FinalAnimation(Player $player): void
    {
        $pos = $this->pos;
        $player->getWorld()->addParticle($pos->add(0, 1, 0), new BlockBreakParticle(VanillaBlocks::IRON()));
        $player->getWorld()->setBlock($pos, VanillaBlocks::END_STONE_BRICK_WALL());
        $player->getWorld()->setBlock($pos->add(0, 1, 0), VanillaBlocks::ENDER_CHEST());
    }

    public function CleanUp(Player $player): void
    {
        $player->getWorld()->setBlock($this->pos, VanillaBlocks::AIR());
        $player->getWorld()->setBlock($this->pos->add(0, 1, 0), VanillaBlocks::AIR());
        $player->getWorld()->addParticle($this->pos->add(0, 1, 0), new BlockBreakParticle(VanillaBlocks::IRON()));
    }
}