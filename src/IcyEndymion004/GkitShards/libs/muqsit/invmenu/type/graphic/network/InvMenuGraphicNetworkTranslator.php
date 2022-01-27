<?php

declare(strict_types=1);

namespace IcyEndymion004\GkitShards\libs\muqsit\invmenu\type\graphic\network;

use IcyEndymion004\GkitShards\libs\muqsit\invmenu\session\InvMenuInfo;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}