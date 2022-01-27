<?php

declare(strict_types=1);

namespace IcyEndymion004\GkitShards\libs\muqsit\invmenu\session;

use IcyEndymion004\GkitShards\libs\muqsit\invmenu\InvMenu;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		public InvMenu $menu,
		public InvMenuGraphic $graphic
	){}
}