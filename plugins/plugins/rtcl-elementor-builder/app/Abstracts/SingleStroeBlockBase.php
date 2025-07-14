<?php

namespace RtclElb\Abstracts;

use RtclElb\Abstracts\BlockBase;

use RtclElb\Helpers\Fns;

abstract class SingleStroeBlockBase extends BlockBase
{
	protected $store;

	public function __construct()
	{
		parent::__construct();
	}

	public function set_store()
	{
		$this->store = rtclStore()->factory->get_store(Fns::last_store_id());
	}
}
