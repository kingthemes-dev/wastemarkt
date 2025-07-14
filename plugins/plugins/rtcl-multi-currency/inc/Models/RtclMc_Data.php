<?php

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class  RtclMc_Data {
	protected static $instance = null;
	private $params;
	public static $pos_options;
	public $currencies_list;
	public $selectedCurrencies = null;
	private $currencies_code_list = null;

	/**
	 * RtclMc_Data constructor.
	 * Init setting
	 */
	private function __construct() {
		global $rtclmc_settings;
		$defaultCurrency = rtclmc_get_default_currency();
		if ( ! $rtclmc_settings ) {
			$rtclmc_settings = get_option( 'rtcl_multi_currency_settings', [] );
		}

		$this->params = $rtclmc_settings;

		$args = [
			'activeTab'                    => 'general',
			'enable'                       => false,
			'use_session'                  => false,
			'currency_default'             => $defaultCurrency['code'],
			'enable_switch_currency_by_js' => false,
			'currencies'                   => [ $defaultCurrency ],
			'auto_detect'                  => '',
			/*Design*/
			'enable_design'                => false,
			'title'                        => '',
			'design_position'              => 'left',
			'enable_collapse'              => false,
			'disable_collapse'             => false,
			'max_height'                   => 0,
			'text_color'                   => '#fff',
			'background_color'             => '#212121',
			'main_color'                   => '#f78080',
			'flag_custom'                  => '',
			'sidebar_style'                => 0,

			/*Auto update*/
			'finance_api'                  => '',
			'finance_api_key'              => '',
			'enable_send_email'            => false,
			'rate_decimals'                => 5,
			'geo_api'                      => 0,
			'enable_currency_by_country'   => false,
			'email_custom'                 => '',
			'update_exchange_rate'         => 0,
			'type'                         => 'static',
			'allowed_currency_type'        => 'all'
		];

		$this->params = apply_filters( 'rtclmc_settings_args', wp_parse_args( $this->params, $args ) );

		self::$pos_options = array(
			'top-left'     => __( 'Top - Left', 'rtcl-multi-currency' ),
			'top-right'    => __( 'Top - Right', 'rtcl-multi-currency' ),
			'bottom-left'  => __( 'Bottom - Left', 'rtcl-multi-currency' ),
			'bottom-right' => __( 'Bottom - Right', 'rtcl-multi-currency' )
		);
	}

	/**
	 * @param bool $new
	 *
	 * @return RtclMc_Data
	 */
	public static function instance( $new = false ) {
		// If the single instance hasn't been set, set it now.
		if ( $new || null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Check Enable plugin
	 *
	 * @return mixed
	 */
	public function get_enable() {
		return apply_filters( 'rtclmc_get_enable', $this->params['enable'] );
	}

	/**
	 * Check Enable plugin
	 *
	 * @return string
	 */
	public function get_type() {
		return apply_filters( 'rtclmc_get_type', $this->params['type'] );
	}


	public function get_allowed_currency_type() {
		return apply_filters( 'rtclmc_get_allowed_currency_type', $this->params['allowed_currency_type'] );
	}

	/**
	 * Check use session
	 *
	 * @return mixed
	 */
	public function use_session() {
		return apply_filters( 'rtclmc_use_session', $this->params['use_session'] );
	}

	/**
	 * Get exchange rate
	 *
	 * @return mixed
	 */
	public function get_update_exchange_rate() {
		return apply_filters( 'rtclmc_get_update_exchange_rate', $this->params['update_exchange_rate'] );
	}

	public function get_currencies() {
		return apply_filters( 'rtclmc_get_currencies', $this->params['currencies'] );
	}

	public function get_currencies_code() {
		if ( ! $this->currencies_code_list ) {
			$this->get_list_currencies();
		}

		return apply_filters( 'rtclmc_get_currencies_code', $this->currencies_code_list );
	}

	/**
	 * Enable currency switcher by JS
	 *
	 * @return mixed
	 */
	public function enable_switch_currency_by_js() {

		return apply_filters( 'rtclmc_enable_switch_currency_by_js', $this->params['enable_switch_currency_by_js'] );
	}

	/**
	 * Get option Price switcher
	 *
	 * @return mixed
	 */
	public function get_price_switcher() {
		return apply_filters( 'rtclmc_get_price_switcher', $this->params['price_switcher'] );
	}

	/**
	 * Check send email when auto update exchange rate
	 *
	 * @return mixed
	 */
	public function check_send_email() {
		return apply_filters( 'rtclmc_check_send_email', $this->params['enable_send_email'] );
	}

	/**
	 * Get email custom address
	 *
	 * @return mixed
	 */
	public function get_email_custom() {
		return apply_filters( 'rtclmc_get_email_custom', $this->params['email_custom'] );
	}

	/**
	 * Get API resource
	 *
	 * @return mixed
	 */
	public function get_finance_api() {
		return apply_filters( 'rtclmc_get_finance_api', $this->params['finance_api'] );
	}

	/**
	 * Get API resource
	 *
	 * @return mixed
	 */
	public function get_finance_api_key() {
		return apply_filters( 'rtclmc_get_finance_api_kay', $this->params['finance_api_key'] );
	}

	/**
	 * Get custom CSS
	 *
	 * @return int
	 */
	public function get_rate_decimals() {
		return (int) apply_filters( 'rtclmc_get_rate_decimals', $this->params['rate_decimals'] );
	}


	/**
	 * Get currency default
	 *
	 * @return string
	 */
	public function get_default_currency() {
		return apply_filters( 'rtclmc_get_default_currency', $this->params['currency_default'] );
	}


	/**
	 * Check fixed price
	 *
	 * @return mixed
	 */
	public function check_fixed_price() {
		return apply_filters( 'rtclmc_check_fixed_price', $this->params['enable_fixed_price'] );
	}

	/**
	 * @param string $language
	 *
	 * @return mixed|void
	 */
	public function get_design_title( $language = '' ) {
		return apply_filters( 'rtclmc_get_design_title', $this->get_params( 'title', $language ) );
	}


	/**
	 * Get type of auto-detect
	 *
	 * @return mixed
	 */
	public function get_auto_detect() {
		return apply_filters( 'rtclmc_get_auto_detect', $this->params['auto_detect'] );
	}

	/**
	 * Check Geo APi
	 *
	 * @return mixed
	 */
	public function get_geo_api() {
		return apply_filters( 'rtclmc_get_geo_api', $this->params['geo_api'] );
	}

	/**
	 * Check enable currency by country
	 *
	 * @return mixed
	 */
	public function get_enable_currency_by_country() {
		return apply_filters( 'rtclmc_get_enable_currency_by_country', $this->params['enable_currency_by_country'] );
	}


	/**Get currency by language
	 *
	 * @param $language_slug
	 *
	 * @return array|mixed
	 */
	public function get_currency_by_language( $language_slug ) {

		if ( $language_slug ) {
			if ( isset( $this->params[ $language_slug . '_by_language' ] ) ) {
				$currency_code = $this->params[ $language_slug . '_by_language' ];
			} else {
				return array();
			}

			return apply_filters( 'rtclmc_get_currency_by_language_' . $language_slug, $currency_code );
		} else {
			return array();
		}
	}


	/**
	 * @param $currency_code
	 *
	 * @return array|mixed
	 */
	public function get_currency_by_countries( $currency_code ) {

		if ( $currency_code ) {
			$countries_code = [];
			if ( ! empty( $this->get_list_currencies()[ $currency_code ]['countries'] ) ) {
				$countries_code = $this->get_list_currencies()[ $currency_code ]['countries'];
			}

			return apply_filters( 'rtclmc_get_currency_by_countries_' . $currency_code, $countries_code );
		} else {
			return array();
		}
	}

	/**
	 * Enable collapse
	 *
	 * @return mixed
	 */
	public function enable_collapse() {
		return apply_filters( 'rtclmc_enable_collapse', $this->params['enable_collapse'] );
	}

	/**
	 * Enable collapse
	 *
	 * @return mixed
	 */
	public function disable_collapse() {
		return apply_filters( 'rtclmc_enable_collapse', $this->params['disable_collapse'] );
	}

	/**
	 * Get sidebar style
	 *
	 * @return mixed
	 */
	public function get_sidebar_style() {
		return apply_filters( 'rtclmc_get_sidebar_style', $this->params['sidebar_style'] );
	}

	/**
	 * Get Main color
	 *
	 * @return mixed
	 */
	public function get_main_color() {
		return apply_filters( 'rtclmc_get_main_color', $this->params['main_color'] );
	}


	/**
	 * Get design position
	 *
	 * @return mixed
	 */
	public function get_design_position() {
		return apply_filters( 'rtclmc_get_design_position', $this->params['design_position'] );
	}

	/**
	 * Get text color on design
	 *
	 * @return mixed
	 */
	public function get_text_color() {
		return apply_filters( 'rtclmc_text_color', $this->params['text_color'] );
	}

	/**
	 * Get background color of design
	 *
	 * @return mixed
	 */
	public function get_background_color() {
		return apply_filters( 'wmc_background_color', $this->params['background_color'] );
	}

	/**
	 * Check design enable
	 *
	 * @return mixed
	 */
	public function get_enable_design() {
		if ( $this->params['enable_design'] && $this->params['enable'] ) {
			return apply_filters( 'rtclmc_get_enable_design', $this->params['enable_design'] );
		} else {
			return false;
		}
	}

	/**
	 * @param $param
	 *
	 * @return string
	 */
	public function get_param( $param ) {
		return isset( $this->params[ $param ] ) ? $this->params[ $param ] : '';
	}

	public function get_params( $name = "", $language = '' ) {
		if ( ! $name ) {
			return $this->params;
		} elseif ( isset( $this->params[ $name ] ) ) {
			if ( $language ) {
				$name_language = $name . '_' . $language;
				if ( isset( $this->params[ $name_language ] ) ) {
					return apply_filters( 'rtclmc_params-' . $name_language, $this->params[ $name_language ] );
				} else {
					return apply_filters( 'rtclmc_params-' . $name_language, $this->params[ $name ] );
				}
			} else {
				return apply_filters( 'rtclmc_params-' . $name, $this->params[ $name ] );
			}
		} else {
			return false;
		}
	}

	/**
	 * Get list currencies
	 *
	 * @return mixed
	 */
	public function get_list_currencies() {
		if ( ! $this->currencies_list ) {
			$data                 = array();
			$currencies_code_list = [];
			if ( count( $this->params['currencies'] ) ) {
				foreach ( $this->params['currencies'] as $currency ) {
					$currencies_code_list[]                 = $currency['code'];
					$rate_fee_calculated                    = $currency['rate_fee'] && $currency['rate_fee_type'] == 'percentage' ? $currency['rate_fee'] * $currency['rate'] / 100 : $currency['rate_fee'];
					$data[ $currency['code'] ]['rate']      = ! $currency['rate_fee_type'] ? $currency['rate'] : floatval( $currency['rate'] ) + floatval( $rate_fee_calculated );
					$data[ $currency['code'] ]['pos']       = ! empty( $currency['pos'] ) ? $currency['pos'] : '';
					$data[ $currency['code'] ]['decimals']  = ! empty( $currency['decimals'] ) ? $currency['decimals'] : '';
					$data[ $currency['code'] ]['custom']    = ! empty( $currency['custom'] ) ? $currency['custom'] : '';
					$data[ $currency['code'] ]['hide']      = $currency['hidden'] ?? 0;
					$data[ $currency['code'] ]['countries'] = ! empty( $currency['countries'] ) && is_array( $currency['countries'] ) ? $currency['countries'] : [];
				}
			}
			$this->currencies_list      = $data;
			$this->currencies_code_list = $currencies_code_list;
		}

		return apply_filters( 'rtclmc_get_list_currencies', $this->currencies_list );
	}


	/**
	 * Get selected currency list
	 *
	 * @return mixed
	 */
	public function getSelectedCurrencies() {
		if ( null === $this->selectedCurrencies ) {
			$currencies = [];
			if ( count( $this->params['currencies'] ) ) {
				$currencyList = Options::get_currency_list();
				foreach ( $this->params['currencies'] as $currency ) {
					$currencies[] = [
						'id'     => $currency['code'],
						'name'   => $currencyList[ $currency['code'] ],
						'symbol' => Functions::get_currency_symbol( $currency['code'] ),
						'hidden' => ! empty( $currency['hidden'] )
					];
				}
			}
			$this->selectedCurrencies = $currencies;
		}

		return apply_filters( 'rtclmc_dynamic_get_selected_currencies', $this->selectedCurrencies );
	}

	/**
	 * Get Links to redirect
	 *
	 * @return array
	 */
	public function get_links() {
		$links               = array();
		$selected_currencies = $this->get_list_currencies();
		$current_currency    = $this->get_current_currency();
		$url                 = ! empty( $_POST['rtclmc_current_url'] ) ? sanitize_text_field( $_POST['rtclmc_current_url'] ) : false;
		if ( count( $selected_currencies ) ) {
			foreach ( $selected_currencies as $k => $currency ) {
				if ( $currency['hide'] ) {
					continue;
				}

				$arg = array( 'rtclmc-currency' => $k );
				if ( $current_currency == $k ) {
					if ( isset( $_GET['min_price'] ) ) {
						$arg['min_price'] = floatval( sanitize_text_field( $_GET['min_price'] ) );
					}
					if ( isset( $_GET['max_price'] ) ) {
						$arg['max_price'] = floatval( sanitize_text_field( $_GET['max_price'] ) );
					}
				} else {
					if ( isset( $_GET['min_price'] ) ) {
						$arg['min_price'] = ( floatval( sanitize_text_field( $_GET['min_price'] ) ) / $selected_currencies[ $current_currency ]['rate'] ) * $currency['rate'];
					}
					if ( isset( $_GET['max_price'] ) ) {
						$arg['max_price'] = ( floatval( sanitize_text_field( $_GET['max_price'] ) ) / $selected_currencies[ $current_currency ]['rate'] ) * $currency['rate'];
					}
				}
				$link        = apply_filters( 'rtclmc_get_link', add_query_arg( $arg, $url ), $k, $currency );
				$links[ $k ] = $link;
			}

		}

		return apply_filters( 'rtclmc_get_links', $links );
	}

	/**
	 * @param string $original_price
	 * @param string $other_price
	 *
	 * @return mixed
	 */
	public function get_exchange( $original_price = '', $other_price = '' ) {
		$rates        = array( $original_price => 1 );
		$selected_api = $this->get_finance_api();
		switch ( $selected_api ) {
			case 'google':
				$data_rates = $this->get_google_exchange( $original_price, $other_price );
				break;
			case 'yahoo':
				$data_rates = $this->get_yahoo_exchange( $original_price, $other_price );
				break;
			case 'cuex':
				$data_rates = $this->get_cuex_exchange( $original_price, $other_price );
				break;
			case 'wise':
				$data_rates = $this->get_transferwise_exchange( $original_price, $other_price );
				break;
			default:
				$data_rates = []; //$data_rates = $this->get_default_exchange( $original_price, $other_price );

		}
		if ( count( $data_rates ) ) {
			foreach ( $data_rates as $k => $rate ) {
				if ( $k !== $original_price ) {
					if ( $rate === false ) {
						if ( isset( $list_currencies[ $k ] ) && ! empty( $list_currencies[ $k ]['rate'] ) ) {
							$rates[ $k ] = $list_currencies[ $k ]['rate'];
						} else {
							$rates[ $k ] = 1;
						}
					} else {
						$rates[ $k ] = number_format( round( $rate, $this->get_rate_decimals() ), $this->get_rate_decimals(), '.', '' );
					}
				}
			}
		}

		return apply_filters( 'rtclmc_get_exchange_rates', $rates, $original_price, $other_price, $this, $selected_api );
	}


	/**
	 * @param $original_price
	 * @param $other_price
	 *
	 * @return array
	 */
	private function get_google_exchange( $original_price, $other_price ) {
		$rates = array();
		if ( $other_price ) {
			$other_price = array_filter( explode( ',', $other_price ) );
		}

		foreach ( $other_price as $code ) {
			$rates[ $code ] = false;
			$url            = 'https://www.google.com/async/currency_v2_update?vet=12ahUKEwjfsduxqYXfAhWYOnAKHdr6BnIQ_sIDMAB6BAgFEAE..i&ei=kgAGXN-gDJj1wAPa9ZuQBw&yv=3&async=source_amount:1,source_currency:' . $this->get_country_freebase( $original_price ) . ',target_currency:' . $this->get_country_freebase( $code ) . ',lang:en,country:us,disclaimer_url:https%3A%2F%2Fwww.google.com%2Fintl%2Fen%2Fgooglefinance%2Fdisclaimer%2F,period:5d,interval:1800,_id:knowledge-currency__currency-v2-updatable,_pms:s,_fmt:pc';

			$request = wp_remote_get(
				$url, array(
					'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
					'timeout'    => 10
				)
			);

			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				preg_match( '/data-exchange-rate=\"(.+?)\"/', $request['body'], $match );
				if ( sizeof( $match ) > 1 && $match[1] ) {
					$rates[ $code ] = $match[1];
				}
			}
		}

		return $rates;
	}


	/**
	 * @param $original_price
	 * @param $other_price
	 *
	 * @return array
	 */
	private function get_yahoo_exchange( $original_price, $other_price ) {
		$rates = array();
		if ( $other_price ) {
			$other_price = array_filter( explode( ',', $other_price ) );
		}
		$now = current_time( 'timestamp', true );

		foreach ( $other_price as $code ) {
			$rates[ $code ] = false;
			$url            = 'https://query1.finance.yahoo.com/v8/finance/chart/' . $original_price . $code . '=X?symbol=' . $original_price . $code . '%3DX&period1=' . ( $now - 60 * 86400 ) . '&period2=' . $now . '&interval=1d&includePrePost=false&events=div%7Csplit%7Cearn&lang=en-US&region=US&corsDomain=finance.yahoo.com';

			$request = wp_remote_get(
				$url, array(
					'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
					'timeout'    => 10
				)
			);

			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$data   = json_decode( $request['body'], true );
				$result = isset( $data['chart']['result'][0]['indicators']['quote'][0]['open'] ) ? array_filter( $data['chart']['result'][0]['indicators']['quote'][0]['open'] ) : ( isset( $data['chart']['result'][0]['meta']['previousClose'] ) ? array( $data['chart']['result'][0]['meta']['previousClose'] ) : array() );

				if ( count( $result ) && is_array( $result ) ) {
					$rates[ $code ] = end( $result );
				}
			}
		}

		return $rates;
	}


	/**
	 * @param $original_price
	 * @param $other_price
	 *
	 * @return array
	 */
	private function get_cuex_exchange( $original_price, $other_price ) {
		$rates = array();
		if ( $other_price ) {
			$other_price = array_filter( explode( ',', $other_price ) );
		}

		$original_price = strtolower( $original_price );
		$apiKey         = $this->get_finance_api_key();
		foreach ( $other_price as $code ) {
			$lower_code     = strtolower( $code );
			$rates[ $code ] = false;
			$date           = date( 'Y-m-d', current_time( 'timestamp' ) );
			$url            = "https://api.cuex.com/v1/exchanges/{$original_price}?to_currency={$lower_code}&from_date={$date}&l=en";
			$request        = wp_remote_get(
				$url, array(
					'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
					'timeout'    => 10,
					'headers'    => [
						'Authorization' => $apiKey ?: '3b71e5d431b2331acb65f2d484d423e5'
					],
				)
			);

			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$body = json_decode( wp_remote_retrieve_body( $request ) );
				if ( isset( $body->data[0]->rate ) ) {
					$rates[ $code ] = $body->data[0]->rate;
				}
			}
		}

		return $rates;
	}


	/**
	 * @param $original_price
	 * @param $other_price
	 *
	 * @return array
	 */
	private function get_transferwise_exchange( $original_price, $other_price ) {
		$rates = array();
		if ( $other_price ) {
			$other_price = array_filter( explode( ',', $other_price ) );
		}
		$apiKey = $this->get_finance_api_key();
		foreach ( $other_price as $code ) {
			$rates[ $code ] = false;
			$url            = "https://transferwise.com/api/v1/payment/calculate?amount=1&sourceCurrency={$original_price}&targetCurrency={$code}";
			$request        = wp_remote_get(
				$url, array(
					'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
					'timeout'    => 100,
					'headers'    => array(
						'x-authorization-key' => $apiKey ?: 'dad99d7d8e52c2c8aaf9fda788d8acdc'
					)
				)
			);

			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$body = json_decode( wp_remote_retrieve_body( $request ) );
				if ( isset( $body->transferwiseRate ) ) {
					$rates[ $code ] = $body->transferwiseRate;
				}
			}
		}

		return $rates;
	}

	/**
	 * @param $original_price
	 * @param $other_price
	 *
	 * @return array|bool
	 */
	private function get_default_exchange( $original_price, $other_price ) {
		global $wp_version;
		$rates = array();

		if ( $original_price && $other_price ) {
			$url = 'https://api.villatheme.com/wp-json/exchange/v1';

			$request = wp_remote_post(
				$url, array(
					'user-agent' => 'WordPress/' . $wp_version . '; ',
					'timeout'    => 10,
					'body'       => array(
						'from' => $original_price,
						'to'   => $other_price
					)
				)
			);
			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$rates = json_decode( trim( $request['body'] ), true );
			}
		} else {
			return false;
		}

		return apply_filters( 'rtclmc_get_default_exchange', $rates );

	}

	public function get_settings() {
		return $this->params;
	}

	public function get_country_freebase( $country_code ) {
		$countries = array(
			"AED" => "/m/02zl8q",
			"AFN" => "/m/019vxc",
			"ALL" => "/m/01n64b",
			"AMD" => "/m/033xr3",
			"ANG" => "/m/08njbf",
			"AOA" => "/m/03c7mb",
			"ARS" => "/m/024nzm",
			"AUD" => "/m/0kz1h",
			"AWG" => "/m/08s1k3",
			"AZN" => "/m/04bq4y",
			"BAM" => "/m/02lnq3",
			"BBD" => "/m/05hy7p",
			"BDT" => "/m/02gsv3",
			"BGN" => "/m/01nmfw",
			"BHD" => "/m/04wd20",
			"BIF" => "/m/05jc3y",
			"BMD" => "/m/04xb8t",
			"BND" => "/m/021x2r",
			"BOB" => "/m/04tkg7",
			"BRL" => "/m/03385m",
			"BSD" => "/m/01l6dm",
			"BTC" => "/m/05p0rrx",
			"BWP" => "/m/02nksv",
			"BYN" => "/m/05c9_x",
			"BZD" => "/m/02bwg4",
			"CAD" => "/m/0ptk_",
			"CDF" => "/m/04h1d6",
			"CHF" => "/m/01_h4b",
			"CLP" => "/m/0172zs",
			"CNY" => "/m/0hn4_",
			"COP" => "/m/034sw6",
			"CRC" => "/m/04wccn",
			"CUC" => "/m/049p2z",
			"CUP" => "/m/049p2z",
			"CVE" => "/m/06plyy",
			"CZK" => "/m/04rpc3",
			"DJF" => "/m/05yxn7",
			"DKK" => "/m/01j9nc",
			"DOP" => "/m/04lt7_",
			"DZD" => "/m/04wcz0",
			"EGP" => "/m/04phzg",
			"ETB" => "/m/02_mbk",
			"EUR" => "/m/02l6h",
			"FJD" => "/m/04xbp1",
			"GBP" => "/m/01nv4h",
			"GEL" => "/m/03nh77",
			"GHS" => "/m/01s733",
			"GMD" => "/m/04wctd",
			"GNF" => "/m/05yxld",
			"GTQ" => "/m/01crby",
			"GYD" => "/m/059mfk",
			"HKD" => "/m/02nb4kq",
			"HNL" => "/m/04krzv",
			"HRK" => "/m/02z8jt",
			"HTG" => "/m/04xrp0",
			"HUF" => "/m/01hfll",
			"IDR" => "/m/0203sy",
			"ILS" => "/m/01jcw8",
			"INR" => "/m/02gsvk",
			"IQD" => "/m/01kpb3",
			"IRR" => "/m/034n11",
			"ISK" => "/m/012nk9",
			"JMD" => "/m/04xc2m",
			"JOD" => "/m/028qvh",
			"JPY" => "/m/088n7",
			"KES" => "/m/05yxpb",
			"KGS" => "/m/04k5c6",
			"KHR" => "/m/03_m0v",
			"KMF" => "/m/05yxq3",
			"KRW" => "/m/01rn1k",
			"KWD" => "/m/01j2v3",
			"KYD" => "/m/04xbgl",
			"KZT" => "/m/01km4c",
			"LAK" => "/m/04k4j1",
			"LBP" => "/m/025tsrc",
			"LKR" => "/m/02gsxw",
			"LRD" => "/m/05g359",
			"LSL" => "/m/04xm1m",
			"LYD" => "/m/024xpm",
			"MAD" => "/m/06qsj1",
			"MDL" => "/m/02z6sq",
			"MGA" => "/m/04hx_7",
			"MKD" => "/m/022dkb",
			"MMK" => "/m/04r7gc",
			"MOP" => "/m/02fbly",
			"MRO" => "/m/023c2n",
			"MUR" => "/m/02scxb",
			"MVR" => "/m/02gsxf",
			"MWK" => "/m/0fr4w",
			"MXN" => "/m/012ts8",
			"MYR" => "/m/01_c9q",
			"MZN" => "/m/05yxqw",
			"NAD" => "/m/01y8jz",
			"NGN" => "/m/018cg3",
			"NIO" => "/m/02fvtk",
			"NOK" => "/m/0h5dw",
			"NPR" => "/m/02f4f4",
			"NZD" => "/m/015f1d",
			"OMR" => "/m/04_66x",
			"PAB" => "/m/0200cp",
			"PEN" => "/m/0b423v",
			"PGK" => "/m/04xblj",
			"PHP" => "/m/01h5bw",
			"PKR" => "/m/02svsf",
			"PLN" => "/m/0glfp",
			"PYG" => "/m/04w7dd",
			"QAR" => "/m/05lf7w",
			"RON" => "/m/02zsyq",
			"RSD" => "/m/02kz6b",
			"RUB" => "/m/01hy_q",
			"RWF" => "/m/05yxkm",
			"SAR" => "/m/02d1cm",
			"SBD" => "/m/05jpx1",
			"SCR" => "/m/01lvjz",
			"SDG" => "/m/08d4zw",
			"SEK" => "/m/0485n",
			"SGD" => "/m/02f32g",
			"SLL" => "/m/02vqvn",
			"SOS" => "/m/05yxgz",
			"SRD" => "/m/02dl9v",
			"SSP" => "/m/08d4zw",
			"STD" => "/m/06xywz",
			"SZL" => "/m/02pmxj",
			"THB" => "/m/0mcb5",
			"TJS" => "/m/0370bp",
			"TMT" => "/m/0425kx",
			"TND" => "/m/04z4ml",
			"TOP" => "/m/040qbv",
			"TRY" => "/m/04dq0w",
			"TTD" => "/m/04xcgz",
			"TWD" => "/m/01t0lt",
			"TZS" => "/m/04s1qh",
			"UAH" => "/m/035qkb",
			"UGX" => "/m/04b6vh",
			"USD" => "/m/09nqf",
			"UYU" => "/m/04wblx",
			"UZS" => "/m/04l7bl",
			"VEF" => "/m/021y_m",
			"VND" => "/m/03ksl6",
			"XAF" => "/m/025sw2b",
			"XCD" => "/m/02r4k",
			"XOF" => "/m/025sw2q",
			"XPF" => "/m/01qyjx",
			"YER" => "/m/05yxwz",
			"ZAR" => "/m/01rmbs",
			"ZMW" => "/m/0fr4f",
		);
		$data      = '';
		if ( $country_code && isset( $countries[ $country_code ] ) ) {
			$data = $countries[ $country_code ];
		}

		return $data;
	}


	/** Get country code by currency
	 *
	 * @param $currency_code
	 *
	 * @return array
	 */
	public function get_country_data( $currency_code ) {
		$countries     = array(
			'AFN' => 'AF',
			'ALL' => 'AL',
			'DZD' => 'DZ',
			'USD' => 'US',
			'EUR' => 'EU',
			'AOA' => 'AO',
			'XCD' => 'LC',
			'ARS' => 'AR',
			'AMD' => 'AM',
			'AWG' => 'AW',
			'AUD' => 'AU',
			'AZN' => 'AZ',
			'BSD' => 'BS',
			'BHD' => 'BH',
			'BDT' => 'BD',
			'BBD' => 'BB',
			'BYN' => 'BY',
			'BYR' => 'BY',
			'BZD' => 'BZ',
			'XOF' => 'BJ',
			'BMD' => 'BM',
			'BTN' => 'BT',
			'BOB' => 'BO',
			'BAM' => 'BA',
			'BWP' => 'BW',
			'NOK' => 'NO',
			'BRL' => 'BR',
			'BND' => 'BN',
			'BGN' => 'BG',
			'BIF' => 'BI',
			'KHR' => 'KH',
			'XAF' => 'CM',
			'CAD' => 'CA',
			'CVE' => 'CV',
			'KYD' => 'KY',
			'CLP' => 'CL',
			'CNY' => 'CN',
			'HKD' => 'HK',
			'COP' => 'CO',
			'KMF' => 'KM',
			'CDF' => 'CD',
			'NZD' => 'NZ',
			'CRC' => 'CR',
			'HRK' => 'HR',
			'CUP' => 'CU',
			'CUC' => 'CU',
			'CZK' => 'CZ',
			'DKK' => 'DK',
			'DJF' => 'DJ',
			'DOP' => 'DO',
			'ECS' => 'EC',
			'EGP' => 'EG',
			'SVC' => 'SV',
			'ERN' => 'ER',
			'ETB' => 'ET',
			'FKP' => 'FK',
			'FJD' => 'FJ',
			'GMD' => 'GM',
			'GEL' => 'GE',
			'GHS' => 'GH',
			'GIP' => 'GI',
			'QTQ' => 'GT',
			'GTQ' => 'GT',
			'GGP' => 'GG',
			'GNF' => 'GN',
			'GWP' => 'GW',
			'GYD' => 'GY',
			'HTG' => 'HT',
			'HNL' => 'HN',
			'HUF' => 'HU',
			'ISK' => 'IS',
			'INR' => 'IN',
			'IDR' => 'ID',
			'IRR' => 'IR',
			'IRT' => 'IR',
			'IQD' => 'IQ',
			'IMP' => 'IM',
			'GBP' => 'GB',
			'ILS' => 'IL',
			'JMD' => 'JM',
			'JPY' => 'JP',
			'JOD' => 'JO',
			'JEP' => 'JE',
			'KZT' => 'KZ',
			'KES' => 'KE',
			'KPW' => 'KP',
			'KRW' => 'KR',
			'KWD' => 'KW',
			'KGS' => 'KG',
			'LAK' => 'LA',
			'LBP' => 'LB',
			'LSL' => 'LS',
			'LRD' => 'LR',
			'LYD' => 'LY',
			'CHF' => 'CH',
			'MKD' => 'MK',
			'MGA' => 'MG',
			'MWK' => 'MW',
			'MYR' => 'MY',
			'MVR' => 'MV',
			'MRO' => 'MR',
			'MUR' => 'MU',
			'MRU' => 'MR',
			'MXN' => 'MX',
			'MDL' => 'MD',
			'MNT' => 'MN',
			'MAD' => 'MA',
			'MZN' => 'MZ',
			'MMK' => 'MM',
			'NAD' => 'NA',
			'NPR' => 'NP',
			'ANG' => 'AN',
			'XPF' => 'WF',
			'NIO' => 'NI',
			'NGN' => 'NG',
			'OMR' => 'OM',
			'PKR' => 'PK',
			'PAB' => 'PA',
			'PGK' => 'PG',
			'PYG' => 'PY',
			'PEN' => 'PE',
			'PHP' => 'PH',
			'PLN' => 'PL',
			'QAR' => 'QA',
			'RON' => 'RO',
			'RUB' => 'RU',
			'RWF' => 'RW',
			'SHP' => 'SH',
			'WST' => 'WS',
			'STD' => 'ST',
			'SAR' => 'SA',
			'RSD' => 'RS',
			'SCR' => 'SC',
			'SLL' => 'SL',
			'SGD' => 'SG',
			'SBD' => 'SB',
			'SOS' => 'SO',
			'ZAR' => 'ZA',
			'SSP' => 'SS',
			'LKR' => 'LK',
			'SDG' => 'SD',
			'SRD' => 'SR',
			'SZL' => 'SZ',
			'SEK' => 'SE',
			'SYP' => 'SY',
			'STN' => 'ST',
			'PRB' => 'ST',
			'TWD' => 'TW',
			'TJS' => 'TJ',
			'TZS' => 'TZ',
			'THB' => 'TH',
			'TOP' => 'TO',
			'TTD' => 'TT',
			'TND' => 'TN',
			'TRY' => 'TR',
			'TMT' => 'TM',
			'UGX' => 'UG',
			'UAH' => 'UA',
			'AED' => 'AE',
			'UYU' => 'UY',
			'UZS' => 'UZ',
			'VUV' => 'VU',
			'VEF' => 'VE',
			'VES' => 'VE',
			'VND' => 'VN',
			'YER' => 'YE',
			'ZMW' => 'ZM',
			'ZWD' => 'ZW',
			'BTC' => 'XBT',
			'ETH' => 'ETH',
			'MOP' => 'MO',
			'ZWL' => 'ZW',
		);
		$country_names = rtcl()->countries->countries;
		$data          = array();

		/*Custom Flag*/
		$custom_flags = $this->get_flag_custom();
		if ( is_array( $custom_flags ) && count( array_filter( $custom_flags ) ) ) {
			$countries = array_merge( $countries, $custom_flags );
		}

		if ( isset( $countries[ $currency_code ] ) && $currency_code ) {
			$data['code'] = $countries[ $currency_code ];
			switch ( $currency_code ) {
				case 'EUR':
					$data['name'] = esc_attr__( 'European Union', 'woocommerce-multi-currency' );
					break;
				default:
					$data['name'] = isset( $country_names[ $countries[ $currency_code ] ] ) ? $country_names[ $countries[ $currency_code ] ] : 'Unknown';
			}

		} else {
			$data['code'] = 'unknown';
			$data['name'] = 'Unknown';
		}

		return $data;
	}


	/**
	 * Custom flag
	 *
	 * @return mixed
	 */
	public function get_flag_custom() {
		$value      = array();
		$data_codes = $this->params['flag_custom'];
		if ( $data_codes ) {
			$args = array_filter( explode( "\n", $data_codes ) );
			if ( count( $args ) ) {
				foreach ( $args as $arg ) {
					$code = array_filter( explode( ",", strtoupper( $arg ) ) );
					if ( count( $code ) == 2 ) {
						$code = array_map( 'trim', $code );
						if ( $code[0] == 'EUR' ) {
							if ( isset( $value['EUR'] ) ) {
								continue;
							} else {
								$rtclmc_ip_info = $this->getcookie( 'rtclmc_ip_info' );
								if ( $rtclmc_ip_info ) {
									$geoplugin_arg = json_decode( base64_decode( $rtclmc_ip_info ), true );
									if ( $geoplugin_arg['country'] != $code[1] ) {
										continue;
									}
								} else {
									continue;
								}
							}
						}
						$value[ $code[0] ] = $code[1];
					}
				}
			}
		} else {
			return array();
		}

		return apply_filters( 'rtclmc_get_flag_custom', $value );
	}


	/**
	 * Set Cookie or Session
	 *
	 * @param        $name
	 * @param        $value
	 * @param int $time
	 * @param string $path
	 */
	public function setcookie( $name, $value, $time = 86400, $path = '/' ) {
		if ( $this->use_session() ) {
			@session_start();
			$_SESSION[ $name ] = $value;
			session_write_close();
		} else {
			@setcookie( $name, $value, $time, $path );
			$_COOKIE[ $name ] = $value;
		}
	}

	/**
	 * Get Cookie or Session
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function getcookie( $name ) {
		if ( $this->use_session() ) {
			if ( ! session_id() && ! self::is_request_to_rest_api() ) {
				@session_start();
			}

			return isset( $_SESSION[ $name ] ) ? $_SESSION[ $name ] : false;
		}

		return isset( $_COOKIE[ $name ] ) ? $_COOKIE[ $name ] : false;
	}

	public static function is_request_to_rest_api() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = '/' . untrailingslashit( rest_get_url_prefix() ) . '/';
		$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		return false !== strpos( $request_uri, $rest_prefix );
	}


	/**Set currency in Cookie
	 *
	 * @param $currency_code
	 */
	public function set_current_currency( $currency_code ) {
		if ( $currency_code ) {
			$this->setcookie( 'rtclmc_current_currency', $currency_code, time() + 60 * 60 * 24 );
		}
	}

	/**
	 * Get current currency
	 *
	 * @return string|null
	 */
	public function get_current_currency() {

		/*Check currency*/
		$current_currency         = $this->getcookie( 'rtclmc_current_currency' );
		$selected_currencies_code = $this->get_currencies_code();

		if ( ! in_array( $current_currency, $selected_currencies_code ) ) {
			$current_currency = $this->get_default_currency();
		}

		return $current_currency;
	}


	/**
	 * 237 countries.
	 * Two-letter country code (ISO 3166-1 alpha-2) => Three-letter currency code (ISO 4217).
	 *
	 * @param $country_code
	 *
	 * @return bool|mixed|string
	 */
	function get_currency_code_by_country_code( $country_code ) {
		if ( ! $country_code ) {
			return false;
		}
		$arg = array(
			'AF' => 'AFN',
			'AL' => 'ALL',
			'DZ' => 'DZD',
			'AS' => 'USD',
			'AD' => 'EUR',
			'AO' => 'AOA',
			'AI' => 'XCD',
			'AQ' => 'XCD',
			'AG' => 'XCD',
			'AR' => 'ARS',
			'AM' => 'AMD',
			'AW' => 'AWG',
			'AU' => 'AUD',
			'AT' => 'EUR',
			'AZ' => 'AZN',
			'BS' => 'BSD',
			'BH' => 'BHD',
			'BD' => 'BDT',
			'BB' => 'BBD',
			'BY' => 'BYR',
			'BE' => 'EUR',
			'BZ' => 'BZD',
			'BJ' => 'XOF',
			'BM' => 'BMD',
			'BT' => 'BTN',
			'BO' => 'BOB',
			'BA' => 'BAM',
			'BW' => 'BWP',
			'BV' => 'NOK',
			'BR' => 'BRL',
			'IO' => 'USD',
			'BN' => 'BND',
			'BG' => 'BGN',
			'BF' => 'XOF',
			'BI' => 'BIF',
			'KH' => 'KHR',
			'CM' => 'XAF',
			'CA' => 'CAD',
			'CV' => 'CVE',
			'KY' => 'KYD',
			'CF' => 'XAF',
			'TD' => 'XAF',
			'CL' => 'CLP',
			'CN' => 'CNY',
			'HK' => 'HKD',
			'CX' => 'AUD',
			'CC' => 'AUD',
			'CO' => 'COP',
			'KM' => 'KMF',
			'CG' => 'XAF',
			'CD' => 'CDF',
			'CK' => 'NZD',
			'CR' => 'CRC',
			'HR' => 'HRK',
			'CU' => 'CUP',
			'CY' => 'EUR',
			'CZ' => 'CZK',
			'DK' => 'DKK',
			'DJ' => 'DJF',
			'DM' => 'XCD',
			'DO' => 'DOP',
			'EC' => 'ECS',
			'EG' => 'EGP',
			'SV' => 'SVC',
			'GQ' => 'XAF',
			'ER' => 'ERN',
			'EE' => 'EUR',
			'ET' => 'ETB',
			'FK' => 'FKP',
			'FO' => 'DKK',
			'FJ' => 'FJD',
			'FI' => 'EUR',
			'FR' => 'EUR',
			'GF' => 'EUR',
			'TF' => 'EUR',
			'GA' => 'XAF',
			'GM' => 'GMD',
			'GE' => 'GEL',
			'DE' => 'EUR',
			'GH' => 'GHS',
			'GI' => 'GIP',
			'GR' => 'EUR',
			'GL' => 'DKK',
			'GD' => 'XCD',
			'GP' => 'EUR',
			'GU' => 'USD',
			'GT' => 'QTQ',
			'GG' => 'GGP',
			'GN' => 'GNF',
			'GW' => 'GWP',
			'GY' => 'GYD',
			'HT' => 'HTG',
			'HM' => 'AUD',
			'HN' => 'HNL',
			'HU' => 'HUF',
			'IS' => 'ISK',
			'IN' => 'INR',
			'ID' => 'IDR',
			'IR' => 'IRR',
			'IQ' => 'IQD',
			'IE' => 'EUR',
			'IM' => 'GBP',
			'IL' => 'ILS',
			'IT' => 'EUR',
			'JM' => 'JMD',
			'JP' => 'JPY',
			'JE' => 'GBP',
			'JO' => 'JOD',
			'KZ' => 'KZT',
			'KE' => 'KES',
			'KI' => 'AUD',
			'KP' => 'KPW',
			'KR' => 'KRW',
			'KW' => 'KWD',
			'KG' => 'KGS',
			'LA' => 'LAK',
			'LV' => 'EUR',
			'LB' => 'LBP',
			'LS' => 'LSL',
			'LR' => 'LRD',
			'LY' => 'LYD',
			'LI' => 'CHF',
			'LT' => 'EUR',
			'LU' => 'EUR',
			'MK' => 'MKD',
			'MG' => 'MGA',
			'MW' => 'MWK',
			'MY' => 'MYR',
			'MV' => 'MVR',
			'ML' => 'XOF',
			'MT' => 'EUR',
			'MH' => 'USD',
			'MQ' => 'EUR',
			'MR' => 'MRO',
			'MU' => 'MUR',
			'YT' => 'EUR',
			'MX' => 'MXN',
			'FM' => 'USD',
			'MD' => 'MDL',
			'MC' => 'EUR',
			'MN' => 'MNT',
			'ME' => 'EUR',
			'MS' => 'XCD',
			'MA' => 'MAD',
			'MZ' => 'MZN',
			'MM' => 'MMK',
			'NA' => 'NAD',
			'NR' => 'AUD',
			'NP' => 'NPR',
			'NL' => 'EUR',
			'AN' => 'ANG',
			'NC' => 'XPF',
			'NZ' => 'NZD',
			'NI' => 'NIO',
			'NE' => 'XOF',
			'NG' => 'NGN',
			'NU' => 'NZD',
			'NF' => 'AUD',
			'MP' => 'USD',
			'NO' => 'NOK',
			'OM' => 'OMR',
			'PK' => 'PKR',
			'PW' => 'USD',
			'PA' => 'PAB',
			'PG' => 'PGK',
			'PY' => 'PYG',
			'PE' => 'PEN',
			'PH' => 'PHP',
			'PN' => 'NZD',
			'PL' => 'PLN',
			'PT' => 'EUR',
			'PR' => 'USD',
			'QA' => 'QAR',
			'RE' => 'EUR',
			'RO' => 'RON',
			'RU' => 'RUB',
			'RW' => 'RWF',
			'SH' => 'SHP',
			'KN' => 'XCD',
			'LC' => 'XCD',
			'PM' => 'EUR',
			'VC' => 'XCD',
			'WS' => 'WST',
			'SM' => 'EUR',
			'ST' => 'STD',
			'SA' => 'SAR',
			'SN' => 'XOF',
			'RS' => 'RSD',
			'SC' => 'SCR',
			'SL' => 'SLL',
			'SG' => 'SGD',
			'SK' => 'EUR',
			'SI' => 'EUR',
			'SB' => 'SBD',
			'SO' => 'SOS',
			'ZA' => 'ZAR',
			'GS' => 'GBP',
			'SS' => 'SSP',
			'ES' => 'EUR',
			'LK' => 'LKR',
			'SD' => 'SDG',
			'SR' => 'SRD',
			'SJ' => 'NOK',
			'SZ' => 'SZL',
			'SE' => 'SEK',
			'CH' => 'CHF',
			'SY' => 'SYP',
			'TW' => 'TWD',
			'TJ' => 'TJS',
			'TZ' => 'TZS',
			'TH' => 'THB',
			'TG' => 'XOF',
			'TK' => 'NZD',
			'TO' => 'TOP',
			'TT' => 'TTD',
			'TN' => 'TND',
			'TR' => 'TRY',
			'TM' => 'TMT',
			'TC' => 'USD',
			'TV' => 'AUD',
			'UG' => 'UGX',
			'UA' => 'UAH',
			'AE' => 'AED',
			'GB' => 'GBP',
			'US' => 'USD',
			'UM' => 'USD',
			'UY' => 'UYU',
			'UZ' => 'UZS',
			'VU' => 'VUV',
			'VE' => 'VEF',
			'VN' => 'VND',
			'VI' => 'USD',
			'WF' => 'XPF',
			'EH' => 'MAD',
			'YE' => 'YER',
			'ZM' => 'ZMW',
			'ZW' => 'ZWD',
		);

		return isset( $arg[ $country_code ] ) ? apply_filters( 'rtcl_get_currency_code', $arg[ $country_code ], $arg, $country_code ) : '';
	}
}