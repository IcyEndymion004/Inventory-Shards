<?php

declare(strict_types=1);

namespace IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\type\graphic;

use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\type\graphic\network\InvMenuGraphicNetworkTranslator;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuGraphic{

	public function send(Player $player, ?string $name) : void;

	public function sendInventory(Player $player, Inventory $inventory) : bool;

	public function remove(Player $player) : void;

	public function getNetworkTranslator() : ?InvMenuGraphicNetworkTranslator;

	/**
	 * Returns a rough duration (in milliseconds) the client
	 * takes to animate the inventory opening and closing.
	 *
	 * @return int
	 */
	public function getAnimationDuration() : int;
}