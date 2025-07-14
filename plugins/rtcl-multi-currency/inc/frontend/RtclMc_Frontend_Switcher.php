<?php

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Geolocation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMc_Frontend_Switcher {
	protected static $instance = null;
	protected $settings;


	/**
	 * @param bool $new
	 *
	 * @return RtclMc_Frontend_Switcher
	 */
	public static function instance( $new = false ) {
		// If the single instance hasn't been set, set it now.
		if ( $new || null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		$this->settings = RtclMc_Data::instance();
		if ( $this->settings->get_enable() ) {
			/*Check change currency. Can not code in init function because Widget price can not get symbol.*/
			$selected_currencies = $this->settings->get_currencies_code();
			if ( $this->settings->use_session() ) {
				add_action( 'wp_ajax_rtclmc_currency_switcher', [ $this, 'currency_switcher' ] );
				add_action( 'wp_ajax_nopriv_rtclmc_currency_switcher', [ $this, 'currency_switcher' ] );
			}
			if ( isset( $_GET['rtclmc-currency'] ) && in_array( str_replace( '/', '', $_GET['rtclmc-currency'] ), $selected_currencies ) ) {

				if ( is_admin() ) {
					return;
				}
				$current_currency = str_replace( '/', '', $_GET['rtclmc-currency'] );
				$this->settings->set_current_currency( $current_currency );
			}
			add_action( 'init', [ $this, 'init' ], 1 );
		}
	}


	/**
	 * Currency switcher via Ajax
	 */
	public function currency_switcher() {
		$selected_currencies = $this->settings->get_currencies_code();
		$message             = '';
		if ( isset( $_POST['rtclmc-currency'] ) && in_array( $_POST['rtclmc-currency'], $selected_currencies ) ) {
			$current_currency = $_POST['rtclmc-currency'];
			$this->settings->set_current_currency( $current_currency );
			$message = esc_html__( 'Currency changed', 'rtcl-multi-currency' );
		}
		wp_send_json_success( $message );
	}

	public function init() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$auto_detect = $this->settings->get_auto_detect();
		switch ( $auto_detect ) {
			case 'auto':
				/*Auto select currency*/
				/*Do not run if a request is rest api or cron*/
				if ( $this->settings->getcookie( 'rtclmc_current_currency' ) || RtclMc_Data::is_request_to_rest_api() || ! empty( $_REQUEST['doing_wp_cron'] ) ) {
					return;
				} else {
					$currencies_code    = $this->settings->get_currencies_code();
					$detect_ip_currency = $this->detect_ip_currency();
					if ( $this->settings->get_enable_currency_by_country() && isset( $detect_ip_currency['country_code'] ) && $detect_ip_currency['country_code'] ) {
						$currency_detected = '';
						foreach ( $currencies_code as $currency_code ) {
							$data = $this->settings->get_currency_by_countries( $currency_code );
							if ( in_array( $detect_ip_currency['country_code'], $data ) ) {
								$currency_detected = $currency_code;
								break;
							}
						}
						if ( $currency_detected ) {
							$this->settings->set_current_currency( $currency_detected );
						} else {
							$this->settings->set_current_currency( $detect_ip_currency['currency_code'] );
						}
					} elseif ( isset( $detect_ip_currency['currency_code'] ) && in_array( $detect_ip_currency['currency_code'], $currencies_code ) ) {
						$this->settings->set_current_currency( $detect_ip_currency['currency_code'] );
					} else {
						$this->settings->set_current_currency( $this->settings->get_default_currency() );
					}

				}
				break;


			case 'approximate':
				if ( $ip_info = $this->settings->getcookie( 'rtclmc_ip_info' ) ) {
					$ip_info         = json_decode( base64_decode( $ip_info ) );
					$currencies_list = $this->settings->get_list_currencies();
					$db_rate         = isset( $currencies_list[ $ip_info->currency_code ]['rate'] ) ? $currencies_list[ $ip_info->currency_code ]['rate'] : '';
					$cookie_rate     = $this->settings->getcookie( 'rtclmc_currency_rate' );
					if ( $db_rate == $cookie_rate ) {
						return;
					}
				}

				$detect_ip_currency = $this->detect_ip_currency();
				if ( isset( $detect_ip_currency['currency_code'] ) ) {
					$this->settings->setcookie( 'rtclmc_currency_rate', $detect_ip_currency['currency_rate'], time() + 86400 );
					$this->settings->setcookie( 'rtclmc_currency_symbol', $detect_ip_currency['currency_symbol'], time() + 86400 );
				}
				break;

			case 'polylang':
				if ( $this->settings->getcookie( 'rtclmc_current_currency' ) ) {
					return;
				} else {
					if ( class_exists( 'Polylang' ) ) {
						$detect_lang   = pll_current_language();
						$currency_code = $this->settings->get_currency_by_language( $detect_lang );
						if ( $currency_code ) {
							$this->settings->set_current_currency( $currency_code );
						}
					}
				}
				break;
			case 'translatepress':
				if ( class_exists( 'TRP_Translate_Press' ) ) {
					$lang             = get_locale();
					$trp_currencies   = $this->settings->get_params( 'translatepress' );
					$current_currency = $this->settings->get_current_currency();
					if ( ! empty( $trp_currencies[ $lang ] ) && $current_currency !== $trp_currencies[ $lang ] ) {
						$this->settings->set_current_currency( $trp_currencies[ $lang ] );
					}
				}
				break;
			default:
		}

	}

	/**
	 * @return array
	 */
	protected function detect_ip_currency() {
		if ( $this->settings->getcookie( 'rtclmc_ip_info' ) ) {
			$geoplugin_arg = json_decode( base64_decode( $this->settings->getcookie( 'rtclmc_ip_info' ) ), true );
		} else {
			if ( ! $this->settings->get_geo_api() ) {
				$ip            = new Geolocation();
				$geo_ip        = $ip->geolocate_ip();
				$country_code  = isset( $geo_ip['country'] ) ? $geo_ip['country'] : '';
				$geoplugin_arg = [
					'country'       => $country_code,
					'currency_code' => $this->settings->get_currency_code_by_country_code( $country_code )
				];
			} else {
				$ip_add = $this->get_ip();
				$this->settings->setcookie( 'rtclmc_ip_add', $ip_add, time() + 86400 );

				@$geoplugin = file_get_contents( 'http://www.geoplugin.net/php.gp?ip=' . $ip_add );
//				@$geoplugin = file_get_contents( 'https://www.geoplugin.com/geodata.php?ip=' . $ip_add );

				if ( $geoplugin ) {
					$geoplugin_arg = unserialize( $geoplugin );
				}


				$geoplugin_arg = [
					'country'       => isset( $geoplugin_arg['geoplugin_countryCode'] ) ? $geoplugin_arg['geoplugin_countryCode'] : 'US',
					'currency_code' => isset( $geoplugin_arg['geoplugin_currencyCode'] ) ? $geoplugin_arg['geoplugin_currencyCode'] : 'USD',
				];
			}

			if ( $geoplugin_arg['country'] ) {
				$this->settings->setcookie( 'rtclmc_ip_info', base64_encode( json_encode( $geoplugin_arg ) ), time() + 86400 );
			} else {
				return [];
			}
		}

		$auto_detect = $this->settings->get_auto_detect();
		if ( $auto_detect == 1 ) {
			/*Auto select currency*/
			if ( is_array( $geoplugin_arg ) && isset( $geoplugin_arg['currency_code'] ) ) {
				$currencies_code = $this->settings->get_currencies_code();
				if ( ! in_array( $geoplugin_arg['currency_code'], $currencies_code ) ) {
					$geoplugin_arg['currency_code'] = $this->settings->get_default_currency();
				}

				return [
					'currency_code' => $geoplugin_arg['currency_code'],
					'country_code'  => $geoplugin_arg['country']
				];
			} else {
				return [];
			}
		} elseif ( $auto_detect == 2 ) {
			/*Approximately price*/
			if ( is_array( $geoplugin_arg ) && isset( $geoplugin_arg['currency_code'] ) ) {
				$currency_code = $geoplugin_arg['currency_code'];
				$country_code  = $geoplugin_arg['country'];
				$symbol        = Functions::get_currency_symbol( $geoplugin_arg['currency_code'] );
			} else {
				return [];
			}
			$currencies_code   = $this->settings->get_currencies_code();
			$main_currency     = $this->settings->get_default_currency();
			$list_currencies   = $this->settings->get_list_currencies();
			$currency_detected = '';
			if ( $this->settings->get_enable_currency_by_country() ) {
				foreach ( $currencies_code as $_currency_code ) {
					$data = $this->settings->get_currency_by_countries( $_currency_code );
					if ( in_array( $country_code, $data ) ) {
						$currency_detected = $_currency_code;
						break;
					}
				}
			}

			if ( $currency_detected ) {
				if ( $currency_detected !== $this->settings->get_current_currency() ) {
					return [
						'currency_code'   => $currency_detected,
						'currency_rate'   => $list_currencies[ $currency_detected ]['rate'],
						'currency_symbol' => Functions::get_currency_symbol( $currency_detected )
					];
				} else {
					return [];
				}

			} else if ( in_array( $currency_code, $currencies_code ) ) {
				return [
					'currency_code'   => $currency_code,
					'currency_rate'   => $list_currencies[ $currency_code ]['rate'],
					'currency_symbol' => Functions::get_currency_symbol( $currency_code )
				];
			} else {
				$exchange_rate = $this->settings->get_exchange( $main_currency, $currency_code );
				if ( is_array( $exchange_rate ) && isset( $exchange_rate[ $currency_code ] ) ) {
					return [
						'currency_code'   => $currency_code,
						'currency_rate'   => $exchange_rate[ $currency_code ],
						'currency_symbol' => $symbol
					];
				} else {
					return [];
				}

			}

		} else {
			return [];
		}
	}

	/**
	 * Return IP
	 *
	 * @return string
	 */
	protected function get_ip() {
		if ( defined( 'RTCLMC_CUSTOM_IP' ) ) {
			if ( isset( $_SERVER[ RTCLMC_CUSTOM_IP ] ) ) {
				return $_SERVER[ RTCLMC_CUSTOM_IP ];
			}
		}
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}
}

RtclMc_Frontend_Switcher::instance();