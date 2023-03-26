<?php

namespace IcyEndymion004\InventoryShards\Animations;

use IcyEndymion004\InventoryShards\Animations\Types\BreakAnimation;
use IcyEndymion004\InventoryShards\Animations\Types\CombustionAnimation;
use IcyEndymion004\InventoryShards\Animations\Types\InstantAnimation;
use IcyEndymion004\InventoryShards\Animations\Types\MagicAnimation;
use IcyEndymion004\InventoryShards\Animations\Types\UnlockAnimation;
use IcyEndymion004\InventoryShards\Loader;
use pocketmine\player\Player;

class AnimationHandler {


    public static function Animate(BaseAnimation $type, Player $player, array $rewards): void {
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new Animator($type, $player, $rewards), 5);
    }

    public static function typeToAnimation(string $type): BaseAnimation|null {
        return match (strtolower($type)) {
            "unlock" => new UnlockAnimation(),
            "magic" => new MagicAnimation(),
            "combustion" => new CombustionAnimation(),
            "break" => new BreakAnimation(),
            "instant" => new InstantAnimation(),
            default => null,
        };
    }
}