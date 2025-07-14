<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMc_Frontend_Price_Filters {
	protected static $instance = null;
	protected static $settings;


	/**
	 * @param bool $new
	 *
	 * @return RtclMc_Frontend_Price_Filters
	 */
	public static function instance( $new = false ) {
		// If the single instance hasn't been set, set it now.
		if ( $new || null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		self::$settings = RtclMc_Data::instance();

		if ( self::$settings->get_enable() ) {
			add_filter( 'rtcl_price_args', [ &$this, 'price_argument' ] );
			add_filter( 'rtcl_raw_price', [ &$this, 'add_rate' ] );
		}
	}

	public function add_rate( $price ) {
		if ( isset( $price ) ) {
			global $rtclmcData;
			if ( empty( $rtclmcData ) ) {
				$rtclmcData = $this->getCurrencyData();
			}
			if ( $rtclmcData ) {
				$rate = $rtclmcData['rate'];

				return $price * (float) $rate;
			}
		}

		return $price;
	}

	public function price_argument( $args ) {
		global $rtclmcData;
		if ( ! is_array( $rtclmcData ) ) {
			$rtclmcData = $this->getCurrencyData();
		}
		if ( ! empty( $rtclmcData ) ) {
			$args['currency']     = $rtclmcData['currency'];
			$args['rate']         = $rtclmcData['rate'];
			$args['price_format'] = $rtclmcData['price_format'];
			$args['decimals']     = $rtclmcData['decimals'];
		}

		return $args;
	}

	public function getCurrencyData() {
		if ( 'approximate' === self::$settings->get_auto_detect() ) {
			if ( ! self::$settings->getcookie( 'rtclmc_currency_rate' ) || ! self::$settings->getcookie( 'rtclmc_currency_symbol' ) || ! self::$settings->getcookie( 'rtclmc_ip_info' ) ) {
				return [];
			}
			$geoplugin_arg        = json_decode( base64_decode( self::$settings->getcookie( 'rtclmc_ip_info' ) ), true );
			$detect_currency_code = isset( $geoplugin_arg['currency_code'] ) ? $geoplugin_arg['currency_code'] : '';
			$country_code         = isset( $geoplugin_arg['country'] ) ? $geoplugin_arg['country'] : '';
			$currencies           = self::$settings->get_currencies_code();
			if ( self::$settings->get_enable_currency_by_country() ) {
				foreach ( $currencies as $currency_code ) {
					$data = self::$settings->get_currency_by_countries( $currency_code );
					if ( in_array( $country_code, $data ) ) {
						$detect_currency_code = $currency_code;
						break;
					}
				}
			}
			if ( $detect_currency_code == self::$settings->get_current_currency() ) {
				return [];
			}
			$currency_code   = $detect_currency_code;
			$list_currencies = self::$settings->get_list_currencies();
			if ( $currency_code && isset( $list_currencies[ $currency_code ] ) ) {
				$decimals    = (int) $list_currencies[ $currency_code ]['decimals'];
				$current_pos = $list_currencies[ $currency_code ]['pos'];
				$rate        = self::$settings->getcookie( 'rtclmc_currency_rate' );
			} else {
				$defaultCurrency = rtclmc_get_default_currency();
				$decimals        = (int) $defaultCurrency['decimals'];
				$current_pos     = $defaultCurrency['pos'];
				$rate            = $defaultCurrency['rate'];
			}
		} else {
			$currency_code   = self::$settings->get_current_currency();
			$list_currencies = self::$settings->get_list_currencies();
			if ( $currency_code && isset( $list_currencies[ $currency_code ] ) ) {
				$decimals    = (int) $list_currencies[ $currency_code ]['decimals'];
				$current_pos = $list_currencies[ $currency_code ]['pos'];
				$rate        = $list_currencies[ $currency_code ]['rate'];
			} else {
				$defaultCurrency = rtclmc_get_default_currency();
				$decimals        = (int) $defaultCurrency['decimals'];
				$current_pos     = $defaultCurrency['pos'];
				$rate            = $defaultCurrency['rate'];
			}
		}

		$format = '';
		switch ( $current_pos ) {
			case 'left' :
				$format = '%1$s%2$s';
				break;
			case 'right' :
				$format = '%2$s%1$s';
				break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
				break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
				break;
		}

		return [
			'currency'     => $currency_code,
			'rate'         => $rate,
			'price_format' => $format,
			'decimals'     => $decimals
		];
	}


	public function get_current_url() {
		global $wp;
		$current_url = site_url( add_query_arg( [], $wp->request ) );

		$redirect_url = isset( $_SERVER['REDIRECT_URI'] ) ? $_SERVER['REDIRECT_URI'] : '';
		$redirect_url = ! empty( $_SERVER['REDIRECT_URL'] ) ? $_SERVER['REDIRECT_URL'] : $redirect_url;
		$redirect_url = ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : $redirect_url;

		return $current_url . $redirect_url;
	}

	public function has_shortcode( $post, $tag ) {
		return is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
	}
}

RtclMc_Frontend_Price_Filters::instance();