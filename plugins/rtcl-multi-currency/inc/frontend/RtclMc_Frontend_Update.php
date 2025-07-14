<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RtclMc_Frontend_Update {
	protected static $instance = null;
	protected $settings;


	/**
	 * @param bool $new
	 *
	 * @return RtclMc_Frontend_Update
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
			add_action( 'init', [ $this, 'update_exchange_rate' ] );
		}
	}

	/**
	 * Fix Round
	 *
	 */
	public function update_exchange_rate() {
		$update = $this->settings->get_update_exchange_rate();
		if ( ! $update ) {
			return;
		}
		$api     = $this->settings->get_finance_api();
		$api_key = $this->settings->get_finance_api_key();
		if ( ! $api || ( in_array( $api, [ 'cuex', 'wise' ] ) && ! $api_key ) ) {
			return;
		}

		$check_data = get_transient( 'rtclmc_update_exchange_rate' );
		if ( $check_data ) {
			return;
		}

		switch ( $update ) {
			case 1;
				$time = 1800;
				break;
			case 2;
				$time = 3600;
				break;
			case 3;
				$time = 3600 * 6;
				break;
			case 4;
				$time = 3600 * 24;
				break;
			case 7;
				$time = 3600 * 24 * 2;
				break;
			case 8;
				$time = 3600 * 24 * 3;
				break;
			case 9;
				$time = 3600 * 24 * 4;
				break;
			case 10;
				$time = 3600 * 24 * 5;
				break;
			case 11;
				$time = 3600 * 24 * 6;
				break;
			case 5;
				$time = 3600 * 24 * 7;
				break;
			default:
				$time = 3600 * 24 * 30;
				break;
		}

		$settings = get_option( 'rtcl_multi_currency_settings', [] );

		if ( count( $settings['currencies'] ) ) {
			$list_currencies = [];
			foreach ( $settings['currencies'] as $currency ) {
				if ( ! empty( $currency['code'] ) ) {
					$list_currencies[] = $currency['code'];
				}
			}
			$rates = $this->settings->get_exchange( $this->settings->get_default_currency(), implode( ',', $list_currencies ) );
			set_transient( 'rtclmc_update_exchange_rate', 1, $time );
			if ( count( $rates ) == count( $list_currencies ) ) {
				$updatedCurrencies = [];
				foreach ( $settings['currencies'] as $currency ) {
					$currency['rate']    = isset( $rates[ $currency['code'] ] ) ? $rates[ $currency['code'] ] : 1;
					$updatedCurrencies[] = $currency;
				}
				$settings['currencies'] = $updatedCurrencies;
				update_option( 'rtcl_multi_currency_settings', $settings );
				$this->send_email();
			}
		}

	}


	/**
	 * Send notification
	 */
	private function send_email() {

		if ( $this->settings->check_send_email() ) {
			$admin_email = $this->settings->get_email_custom();
			if ( ! $admin_email ) {
				$admin_email = get_option( 'admin_email' );
			}
			$list_currencies = $this->settings->get_currencies();
			ob_start(); ?>
            <table cellpadding="2" cellspacing="3">
                <tr>
                    <th><?php echo esc_html__( 'Currency', 'rtcl-multi-currency' ) ?></th>
                    <th><?php echo esc_html__( 'Rate', 'rtcl-multi-currency' ) ?></th>
                </tr>
				<?php if ( count( $list_currencies ) ) {
					foreach ( $list_currencies as $currency ) {
						?>
                        <tr>
                            <td><?php echo esc_html( $currency['code'] ) ?></td>
                            <td><?php echo esc_html( $currency['rate'] ) ?></td>
                        </tr>
					<?php }
				} ?>
            </table>
			<?php
			$content = ob_get_clean();

			if ( $admin_email ) {
				$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
				wp_mail( $admin_email, esc_html__( 'Exchange rate is updated', 'rtcl-multi-currency' ), esc_html__( 'You can check at ', 'rtcl-multi-currency' ) . get_option( 'siteurl' ) . '<br/>' . $content, $headers );
			}
		}
	}
}

RtclMc_Frontend_Update::instance();