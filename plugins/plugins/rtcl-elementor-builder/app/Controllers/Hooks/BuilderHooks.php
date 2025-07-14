<?php

namespace RtclElb\Controllers\Hooks;

use RtclElb\Traits\ELTempleateBuilderTraits;
use RtclElb\Traits\Singleton;

class BuilderHooks
{
	use Singleton;
	use ELTempleateBuilderTraits;

	public function __construct()
	{
		add_filter('rtclblock/builder/set/current/page/type', [$this, 'builder_page_type']);
	}

	public function builder_page_type($type)
	{
		if (self::is_single() && self::is_builder_page_single()) {
			$type = 'single';
		} elseif (self::is_archive()) {
			$type = 'archive';
		} elseif (self::is_store_single() && self::is_store_page_builder()) {
			$type = 'store-single';
		}
		return $type;
	}
}
