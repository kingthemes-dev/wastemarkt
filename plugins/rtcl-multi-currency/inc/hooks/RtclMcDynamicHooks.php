<?php

use Rtcl\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMcDynamicHooks {
	protected static $instance = null;


	final public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'rtcl_rest_set_local', [ $this, 'set_local_currency' ] );
		add_filter( 'rtcl_rest_api_listing_data', [ $this, 'add_dynamic_currency_at_listing' ] );
		add_filter( 'rtcl_rest_api_config_data', [ $this, 'multiCurrencyConfig' ] );
	}

	/**
	 * @param array $headers
	 */
	public function set_local_currency( array $headers ) {
		if ( empty( $headers['X-LOCALE-CURRENCY'] ) || ! $current_currency = sanitize_key( wp_unslash( $headers['X-LOCALE-CURRENCY'] ) ) ) {
			return;
		}
		$current_currency = strtoupper($current_currency);
		RtclMc_Data::instance()->set_current_currency( $current_currency );
	}

	public function multiCurrencyConfig( $config ) {
		$config['multiCurrency'] = [
			'type'             => RtclMc_Data::instance()->get_type(),
			'enable_selection' => RtclMc_Data::instance()->get_enable_design(),
			'currencyList'     => RtclMc_Data::instance()->getSelectedCurrencies()
		];

		return $config;
	}


	public function add_dynamic_currency_at_listing( $data ) {
		if ( $currency_id = RtclMc_Data::instance()->get_current_currency() ) {
			$data['currency'] = [
				'id'     => $currency_id,
				'symbol' => Functions::get_currency_symbol( $currency_id )
			];
		}

		return $data;
	}
}

RtclMcDynamicHooks::instance();