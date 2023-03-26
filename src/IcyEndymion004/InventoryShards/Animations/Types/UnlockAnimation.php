<?php

namespace IcyEndymion004\InventoryShards\Animations\Types;

use IcyEndymion004\InventoryShards\Animations\BaseAnimation;
use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\InvMenu;
use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\type\InvMenuTypeIds;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilBreakSound;
use pocketmine\world\sound\BlazeShootSound;

class UnlockAnimation extends BaseAnimation {

    protected int $duration = 11;

    protected int $timer = 1;

    public function Animate(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setListener(InvMenu::readonly());
        $menu->setName(" ");
        $inv = $menu->getInventory();
        $walls = [0, 1, 2, 3, 4, 5, 6, 7, 8, 18, 19, 20, 21, 22, 23, 24, 25, 26];
        foreach ($walls as $slot){
            $inv->setItem($slot, VanillaBlocks::IRON_BARS()->asItem());
        }
        $tick = $this->timer / 2;
        $tick_1 = [9, 17];
        $tick_2 = [10, 16];
        $tick_3 = [11, 15];
        $tick_4 = [12, 14];
        $tick_5 = [13];
        switch ($tick){
            case 2:
                foreach ($tick_1 as $slot){
                    $inv->setItem($slot, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 13));
                }
                break;
            case 4:
                foreach ($tick_2 as $slot){
                    $inv->setItem($slot, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 13));
                }
                $player->broadcastSound(new BlazeShootSound());
                break;
            case 6:
                foreach ($tick_3 as $slot){
                    $inv->setItem($slot, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 13));
                }
                $player->broadcastSound(new BlazeShootSound());
                break;
            case 8:
                foreach ($tick_4 as $slot){
                    $inv->setItem($slot, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 13));
                }
                $player->broadcastSound(new BlazeShootSound());
                break;
            case 10:
                foreach ($tick_5 as $slot){
                    $inv->setItem($slot, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 13));
                }
                $player->broadcastSound(new BlazeShootSound());
                break;
        }
        $this->timer += 1;
    }

    public function FinalAnimation(Player $player): void
    {
        $player->removeCurrentWindow();
        $player->broadcastSound(new AnvilBreakSound());
    }

    public function CleanUp(Player $plauer): void
    {
        // TODO: Implement CleanUp() method.
    }
}