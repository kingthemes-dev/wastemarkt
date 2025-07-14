<?php

use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

class RtclMc_Admin {

	protected static $instance = null;

	final public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private $version;

	private function __construct() {
		$this->version = RTCLMC_VERSION;
		add_action( 'admin_menu', [ $this, 'currency_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_script' ] );
		add_action( 'wp_ajax_rtclmc_save_settings', [ $this, 'rtclmc_save_settings' ] );
		add_filter( 'rtcl_licenses', [ $this, 'license' ], 30 );
	}

	public function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCLMC_FILE,
			'api_data'    => [
				'key_name'    => 'license_rtclmc_key',
				'status_name' => 'license_rtclmc_status',
				'action_name' => 'rtclmc_manage_licensing',
				'product_id'  => 176439,
				'version'     => RTCLMC_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Multi Currency plugin license key', 'rtcl-multi-currency' ),
			],
		];

		return $licenses;
	}

	public function update_currency_exchange() {
		check_ajax_referer( 'rtclmc-admin-settings-nonce', '_ajax_nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'error' => 'Sorry you are not allowed to do this.' ] );
		}
		$original_price   = sanitize_text_field( $_POST['original_price'] );
		$other_currencies = sanitize_text_field( $_POST['other_currencies'] );
		$data             = RtclMc_Data::instance();
		$rates            = $data->get_exchange( $original_price, $other_currencies );
		wp_send_json_success( $rates );
	}

	public static function rtclmc_save_settings() {
		check_ajax_referer( 'rtclmc-admin-settings-nonce', '_ajax_nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Sorry you are not allowed to do this.' ] );
		}
		if ( empty( $_POST['settings'] ) ) {
			wp_send_json_error( [ 'message' => __( 'Settings not found', 'rtcl-multi-currency' ) ] );
		}
		$data = $_POST['settings'];
		$settings        = [
			'activeTab'                    => sanitize_text_field( $data['activeTab'] ),
			'enable'                       => filter_var( $data['enable'], FILTER_VALIDATE_BOOLEAN ),
			'type'                         => in_array( $data['type'], [
				'static',
				'dynamic'
			] ) ? $data['type'] : 'static',
			'allowed_currency_type'        => in_array( $data['allowed_currency_type'], [
				'all',
				'selected'
			] ) ? $data['allowed_currency_type'] : 'all',
			'use_session'                  => filter_var( $data['use_session'], FILTER_VALIDATE_BOOLEAN ),
			'enable_switch_currency_by_js' => $data['enable_switch_currency_by_js'] === true,
			'update_exchange_rate'         => absint( $data['update_exchange_rate'] ),
			'rate_decimals'                => absint( $data['rate_decimals'] ),
			'enable_send_email'            => filter_var( $data['enable_send_email'], FILTER_VALIDATE_BOOLEAN ),
			'currency_default'             => sanitize_text_field( $data['currency_default'] ),
			'enable_design'                => filter_var( $data['enable_design'], FILTER_VALIDATE_BOOLEAN ),
			'design_position'              => in_array(
				$data['design_position'],
				[
					'left',
					'right',
				]
			) ? $data['design_position'] : 'left',
			'enable_collapse'              => filter_var( $data['enable_collapse'], FILTER_VALIDATE_BOOLEAN ),
			'disable_collapse'             => filter_var( $data['disable_collapse'], FILTER_VALIDATE_BOOLEAN ),
			'max_height'                   => absint( $data['max_height'] ),
			'sidebar_style'                => absint( $data['sidebar_style'] ),
			'title'                        => sanitize_text_field( $data['title'] ),
			'text_color'                   => sanitize_text_field( $data['text_color'] ),
			'background_color'             => sanitize_text_field( $data['background_color'] ),
			'main_color'                   => sanitize_text_field( $data['main_color'] ),
			'auto_detect'                  => sanitize_text_field( $data['auto_detect'] ),
			'finance_api'                  => sanitize_text_field( $data['finance_api'] ),
			'finance_api_key'              => sanitize_text_field( $data['finance_api_key'] ),
			'email_custom'                 => sanitize_email( $data['email_custom'] ),
			'geo_ip'                       => absint( $data['geo_ip'] ),
			'enable_currency_by_country'   => filter_var( $data['enable_currency_by_country'], FILTER_VALIDATE_BOOLEAN ),
		];
		$_cDefault       = null;
		$defaultCurrency = rtclmc_get_default_currency();
		if ( is_array( $data['currencies'] ) && ! empty( $data['currencies'] ) ) {
			$temp_c     = [];
			$currencies = [];
			foreach ( $data['currencies'] as $_currency ) {
				if ( ! empty( $_currency['code'] ) && ! in_array( $_currency['code'], $temp_c ) ) {
					$temp_c[]  = $_currency['code'];
					$_currency = wp_parse_args(
						$_currency,
						[
							'rate'          => '',
							'rate_fee'      => '',
							'rate_fee_type' => '',
							'decimals'      => '',
							'countries'     => [],
							'hide'          => false,
						]
					);
					$_c        = [
						'code'          => sanitize_text_field( $_currency['code'] ),
						'pos'           => sanitize_text_field( $_currency['pos'] ),
						'rate'          => floatval( $_currency['rate'] ),
						'rate_fee'      => floatval( $_currency['rate_fee'] ),
						'rate_fee_type' => in_array(
							$_currency['rate_fee_type'],
							[
								'percentage',
								'fixed',
							]
						) ? $_currency['rate_fee_type'] : 'percentage',
						'decimals'      => absint( $_currency['decimals'] ),
						'hide'          => $_currency['hide'] === true,
					];
					if ( $settings['enable_currency_by_country'] && is_array( $_currency['countries'] ) && ! empty( $_currency['countries'] ) ) {
						$_c['countries'] = array_map( 'sanitize_text_field', $_currency['countries'] );
					}
					if ( $settings['currency_default'] === $_currency['code'] ) {
						$_cDefault = $_c;
					}
					$currencies[] = $_c;
				}
			}
			$settings['currencies'] = $currencies;
		} else {
			$settings['currencies'][] = $defaultCurrency;
		}
		if ( $_cDefault && ( $_cDefault['code'] !== $defaultCurrency['code'] || $_cDefault['pos'] !== $defaultCurrency['pos'] ) ) {
			$gSettings                      = Functions::get_option( 'rtcl_general_settings' );
			$gSettings['currency']          = $_cDefault['code'];
			$gSettings['currency_position'] = $_cDefault['pos'];
			update_option( 'rtcl_general_settings', $gSettings );
		}
		update_option( 'rtcl_multi_currency_settings', $settings );
		delete_transient( 'rtclmc_update_exchange_rate' );
		wp_send_json_success( $settings );
	}

	public function load_admin_script() {
		if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'rtcl-multi-currency' ) {
			wp_enqueue_style( 'rtclmc-admin', RTCLMC_URL . 'assets/css/admin.css' );
			wp_enqueue_script( 'rtclmc-admin', RTCLMC_URL . 'assets/js/admin.min.js', [ 'jquery' ], $this->version, true );
			$currencyPositions = [];
			foreach ( Options::get_currency_positions() as $posKey => $posLabel ) {
				$currencyPositions[] = [
					'label' => $posLabel,
					'value' => $posKey,
				];
			}
			$currency_list = [];
			foreach ( Options::get_currencies() as $currencyKey => $currencyLabel ) {
				$currency_list[] = [
					'label' => $currencyLabel,
					'value' => $currencyKey,
				];
			}
			wp_localize_script(
				'rtclmc-admin',
				'rtclmcParams',
				[
					'ajaxUrl'                       => admin_url( 'admin-ajax.php' ),
					'_ajax_nonce'                   => wp_create_nonce( 'rtclmc-admin-settings-nonce' ),
					'currency_list'                 => $currency_list,
					'currency_positions'            => $currencyPositions,
					'fee_type_list'                 => rtclmc_get_currency_fee_type_list(),
					'finance_api_list'              => rtclmc_get_finance_api_list(),
					'exchange_update_duration_list' => rtclmc_get_exchange_update_duration_list(),
					'sidebar_style_list'            => rtclmc_get_sidebar_style_list(),
					'design_position_list'          => rtclmc_get_design_position_list(),
					'countries'                     => rtcl()->countries->get_countries(),
					'auto_detect_list'              => rtclmc_get_auto_detect_list(),
					'settings'                      => RtclMc_Data::instance()->get_settings(),
				]
			);
		}
	}

	public function currency_menu() {
		add_menu_page(
			esc_html__( 'Classified Listing - Multi Currency', 'rtcl-multi-currency' ),
			esc_html__( 'Multi Currency', 'rtcl-multi-currency' ),
			'manage_rtcl_options',
			'rtcl-multi-currency',
			[ &$this, 'display_multi_currency_view' ],
			RTCLMC_URL . 'assets/img/icon-currency.png',
			6
		);
	}

	public function display_multi_currency_view() {
		include_once dirname( __FILE__ ) . '/views/html-admin-settings.php';
	}
}

RtclMc_Admin::instance();
