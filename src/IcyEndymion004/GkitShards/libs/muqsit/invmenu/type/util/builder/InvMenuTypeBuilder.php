<?php

declare(strict_types=1);

namespace IcyEndymion004\GkitShards\libs\muqsit\invmenu\type\util\builder;

use IcyEndymion004\GkitShards\libs\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}