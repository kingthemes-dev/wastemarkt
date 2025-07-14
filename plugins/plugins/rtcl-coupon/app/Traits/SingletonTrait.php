<?php


namespace RadiusTheme\COUPON\Traits;

trait SingletonTrait {

	/**
	 * Store the singleton object.
	 *
	 * @var Singleton
	 */
	private static $singleton = false;

	/**
	 * Create an inaccessible constructor.
	 */
	private function __construct() {
		$this->init();
	}
	/**
	 * Function run by this function
	 *
	 * @return void
	 */
	protected function init() {
	}

	/**
	 * Fetch an instance of the class.
	 */
	final public static function get_instance() {
		if ( false === self::$singleton ) {
			self::$singleton = new self();
		}
		return self::$singleton;
	}

	/**
	 * Prevent cloning.
	 */
	final public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'rtcl-coupon' ), '1.0' );
	}

	/**
	 * Prevent unserializing.
	 */
	final public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'rtcl-coupon' ), '1.0' );
	}

}
