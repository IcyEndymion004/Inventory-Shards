<?php

declare(strict_types=1);

namespace IcyEndymion004\GkitShards\libs\muqsit\invmenu\session\network\handler;

use Closure;
use IcyEndymion004\GkitShards\libs\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}