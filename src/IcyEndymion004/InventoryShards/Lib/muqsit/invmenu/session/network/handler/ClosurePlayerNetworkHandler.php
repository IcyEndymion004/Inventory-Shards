<?php

declare(strict_types=1);

namespace IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\session\network\handler;

use Closure;
use IcyEndymion004\InventoryShards\Lib\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

final class ClosurePlayerNetworkHandler implements PlayerNetworkHandler{

	/**
	 * @param Closure(Closure) : NetworkStackLatencyEntry $creator
	 */
	public function __construct(
		private Closure $creator
	){}

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry{
		return ($this->creator)($then);
	}
}