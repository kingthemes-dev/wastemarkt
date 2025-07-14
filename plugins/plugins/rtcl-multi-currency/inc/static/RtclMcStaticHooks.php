<?php

use Rtcl\Helpers\Functions;
use Rtcl\Models\Form\Form;
use Rtcl\Models\Listing;
use Rtcl\Resources\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMcStaticHooks {
	protected static $instance = null;


	final public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'rtcl_listing_form_price_items', [ $this, 'currency_selector' ] );
		add_action( 'rtcl_listing_form_after_save_or_update', [ $this, 'update_price_currency_frontend' ], 10, 5 );
		add_filter( 'rtcl_listing_price_currency_symbol', [ $this, 'listing_price_currency' ], 10, 2 );
		add_action( 'rtcl_listing_update_metas_at_admin', [ $this, 'update_price_currency_admin' ], 10, 2 );
		add_filter( 'rtcl_price_args', [ &$this, 'price_argument' ] );
		add_filter( 'rtcl_rest_api_listing_data', [ &$this, 'add_static_currency' ], 10, 2 );
		add_filter( 'rtcl_rest_api_config_data', [ &$this, 'multiCurrencyConfig' ] );

		// Form builder support
		add_filter( 'rtcl_fb_localized_options', [ __CLASS__, 'mc_localize_fb_options' ] );
		add_filter( 'rtcl_fb_field_value_pricing', [ __CLASS__, 'pricing_currency_form_data' ], 10, 3 );
	}


	/**
	 * @param array | static | null $value
	 * @param array | null          $field
	 * @param Listing               $listing
	 *
	 * @return array
	 */
	public static function pricing_currency_form_data( $value, $field, $listing ) {
		if ( ! is_a( $listing, Listing::class ) ) {
			return $value;
		}
		$value['currency'] = get_post_meta( $listing->get_id(), 'rtcl_price_currency', true );

		return $value;
	}

	public static function mc_localize_fb_options( $params ) {
		$ac_type       = RtclMc_Data::instance()->get_allowed_currency_type();
		$sltCurrencies = RtclMc_Data::instance()->get_currencies();
		$currencyList  = Options::get_currency_list();
		$currencies    = [];
		if ( $ac_type === 'selected' && ! empty( $sltCurrencies ) ) {
			foreach ( $sltCurrencies as $_currency ) {
				$currencies[] = [
					'id'     => $_currency['code'],
					'name'   => $currencyList[ $_currency['code'] ],
					'symbol' => Functions::get_currency_symbol( $_currency['code'] )
				];
			}
		} else {
			foreach ( $currencyList as $code => $name ) {
				$currencies[] = [
					'id'     => $code,
					'name'   => $name,
					'symbol' => Functions::get_currency_symbol( $code )
				];
			}
		}
		$params['multi_currency'] = [
			'type'          => RtclMc_Data::instance()->get_allowed_currency_type(),
			'default'       => RtclMc_Data::instance()->get_default_currency(),
			'currency_list' => $currencies
		];

		return $params;
	}

	public function add_static_currency( $data, $listing ) {
		if ( $listing && $currency_id = rtclmc_get_listing_price_currency( $listing ) ) {
			$data['currency'] = [
				'id'     => $currency_id,
				'symbol' => Functions::get_currency_symbol( $currency_id )
			];
		}

		return $data;
	}

	public function multiCurrencyConfig( $config ) {
		$allowed_currency_type = RtclMc_Data::instance()->get_allowed_currency_type();
		$sltCurrencies         = RtclMc_Data::instance()->get_currencies();
		$currencyList          = Options::get_currency_list();

		$currencies = [];
		if ( $allowed_currency_type === 'selected' && ! empty( $sltCurrencies ) ) {
			foreach ( $sltCurrencies as $_currency ) {
				$currencies[] = [
					'id'     => $_currency['code'],
					'name'   => $currencyList[ $_currency['code'] ],
					'symbol' => Functions::get_currency_symbol( $_currency['code'] )
				];
			}
		} else {
			foreach ( $currencyList as $code => $name ) {
				$currencies[] = [
					'id'     => $code,
					'name'   => $name,
					'symbol' => Functions::get_currency_symbol( $code )
				];
			}
		}

		$config['multiCurrency'] = [
			'type'         => RtclMc_Data::instance()->get_type(),
			'currencyList' => $currencies
		];

		return $config;
	}

	public function listing_price_currency( $symbol, $listing ) {
		if ( $listing ) {
			$symbol = Functions::get_currency_symbol( rtclmc_get_listing_price_currency( $listing ) );
		}

		return $symbol;
	}


	public function price_argument( $args ) {
		if ( ! empty( $args['listing'] ) ) {
			$args['currency_symbol'] = Functions::get_currency_symbol( rtclmc_get_listing_price_currency( $args['listing'] ) );
		}

		return $args;
	}


	/**
	 * @param Listing $listing
	 * @param string  $type
	 * @param int     $category_id
	 * @param string  $new_listing_status
	 * @param array   $request_data
	 *
	 * @return void
	 */
	public function update_price_currency_frontend( $listing, $type, $category_id, $new_listing_status, $request_data = [ 'data' => '' ] ) {
		if ( ! is_a( $listing, Listing::class ) ) {
			return;
		}

		$data = $request_data['data'];

		if ( Functions::isEnableFb() ) {
			$form_id = $listing->get_form_id();
			if ( empty( $form_id ) ) {
				return;
			}
			$form = Form::query()->find( $form_id );
			$form = apply_filters( 'rtcl_fb_form', $form );
			if ( empty( $form ) || empty( $fields = $form->fields ) ) {
				return;
			}

			$pricingField = null;
			foreach ( $fields as $field ) {
				if ( 'pricing' === $field['element'] ) {
					$pricingField = $field;
					break;
				}
			}
			if ( empty( $pricingField ) ) {
				return;
			}

			if ( ! empty( $data['formData'] ) && is_string( $data['formData'] ) ) {
				parse_str( $_POST['formData'], $formData );
				$pricingData    = ! empty( $formData['pricing'] ) ? $formData['pricing'] : [];
				$price_currency = sanitize_text_field( ! empty( $pricingData['currency'] ) ? $pricingData['currency'] : '' );
				$this->update_price_currency( $listing->get_id(), $price_currency );
			}

		} else {
			if ( ! empty( $data['rtcl_price_currency'] ) ) {
				return;
			}
			$price_currency = sanitize_text_field( $data['rtcl_price_currency'] );
			$this->update_price_currency( $listing->get_id(), $price_currency );
		}
	}

	/**
	 * @param int   $post_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function update_price_currency_admin( $post_id, $data ) {
		if ( ! empty( $data['rtcl_price_currency'] ) ) {
			$price_currency = sanitize_text_field( $data['rtcl_price_currency'] );
			$this->update_price_currency( $post_id, $price_currency );
		}
	}

	/**
	 * @param $listing_id
	 * @param $price_currency
	 *
	 * @return bool|int|void
	 */
	private function update_price_currency( $listing_id, $price_currency ) {
		if ( ! $listing_id || ! $price_currency ) {
			return;
		}
		$allowed_currency_type = RtclMc_Data::instance()->get_allowed_currency_type();
		$sltCurrencies         = RtclMc_Data::instance()->get_currencies();
		$found                 = false;
		if ( $allowed_currency_type === 'selected' && ! empty( $sltCurrencies ) ) {
			foreach ( $sltCurrencies as $currency ) {
				if ( $currency['code'] === $price_currency ) {
					$found = true;
					break;
				}
			}
		} else {
			$currencyList = Options::get_currencies();
			if ( isset( $currencyList[ $price_currency ] ) ) {
				$found = true;
			}
		}
		if ( $found ) {
			return update_post_meta( $listing_id, 'rtcl_price_currency', $price_currency );
		}

		return false;
	}

	/**
	 * @param Listing $listing
	 *
	 * @return void
	 */
	public function currency_selector( $listing ) {
		if ( ! $listing ) {
			global $listing;
		}

		$allowed_currency_type = RtclMc_Data::instance()->get_allowed_currency_type();
		$sltCurrencies         = RtclMc_Data::instance()->get_currencies();
		$currencyList          = Options::get_currencies();
		$currencies            = [];
		if ( $allowed_currency_type === 'selected' && ! empty( $sltCurrencies ) ) {
			foreach ( $sltCurrencies as $currency ) {
				$currencies[ $currency['code'] ] = $currencyList[ $currency['code'] ];
			}
		} else {
			$currencies = $currencyList;
		}
		Functions::get_template(
			'multi-currency/price-currency',
			[
				'listing'             => $listing,
				'default_currency'    => RtclMc_Data::instance()->get_default_currency(),
				'currencies'          => $currencies,
				'rtcl_price_currency' => $listing ? get_post_meta( $listing->get_id(), 'rtcl_price_currency', true ) : ''
			],
			'',
			RTCLMC_PATH . 'templates/'
		);
	}
}

RtclMcStaticHooks::instance();