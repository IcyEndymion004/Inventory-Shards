<?php

namespace IcyEndymion004\InventoryShards\Animations\Types;

use IcyEndymion004\InventoryShards\Animations\BaseAnimation;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\particle\HugeExplodeParticle;

class MagicAnimation extends BaseAnimation {

    protected int $duration = 11;

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
        $particle = new DustParticle($this->translateToColor($this->timer));
        $center = $pos;
        for ($yaw = 0, $y = $center->y; $y < $center->y + 1.5; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
            $x = -sin($yaw) + $center->x;
            $z = cos($yaw) + $center->z;
            $vec = new Vector3($x, $y, $z);
            $player->getWorld()->addParticle($vec, $particle);
        }
    }

    public function FinalAnimation(Player $player): void
    {
        $pos = $this->pos;
        $player->getWorld()->addParticle($pos->add(0, 1, 0), new HugeExplodeParticle());
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

    protected function translateToColor(int $timer): Color {
        return match ($timer){
            11 => new Color(0, 0 , 0),
            10 => new Color(25, 25, 25),
            9 => new Color(50, 50 ,50),
            8 => new Color(75, 75, 75),
            7 => new Color(100, 100, 100),
            6 => new Color(125, 125, 125),
            5 => new Color(150, 150, 150),
            4 => new Color(175, 175, 175),
            3 => new Color(200, 200, 200),
            2 => new Color(225, 225, 225),
            default => new Color(255, 255 , 255),
        };
    }
}