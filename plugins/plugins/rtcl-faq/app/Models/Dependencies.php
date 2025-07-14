<?php

namespace RtclFaq\Models;

use Rtcl;
use RtclFaq\Traits\SingletonTraits;

/**
 * Dependencies class
 */
class Dependencies {

	use SingletonTraits;

	/**
	 * Store minimum RTCL version
	 */
	const MIN_RTCL = '2.6.6';

	/**
	 * Store error message
	 *
	 * @var array
	 */
	private array $missing = [];

	/**
	 * Check everything is ok or not
	 *
	 * @var bool
	 */
	private bool $allOk = true;

	/**
	 * Class constructor.
	 */
	final private function __construct() {
	}

	/**
	 * Check RTCL main plugin is existing or not
	 *
	 * @return bool
	 */
	public function check(): bool {

		if ( ! class_exists( Rtcl::class ) ) {
			$link                                = esc_url(
				add_query_arg(
					array(
						'tab'       => 'plugin-information',
						'plugin'    => 'classified-listing',
						'TB_iframe' => 'true',
						'width'     => '640',
						'height'    => '500',
					),
					admin_url( 'plugin-install.php' )
				)
			);
			$this->missing['Classified Listing'] = $link;
			$this->allOk                         = false;
		} elseif ( defined( 'RTCL_VERSION' ) && version_compare( RTCL_VERSION, self::MIN_RTCL, '<' ) ) {
			add_action( 'admin_notices', [ $this, '_old_rtcl_warning' ] );
			$this->allOk = false;
		}

		if ( ! empty( $this->missing ) ) {
			add_action( 'admin_notices', [ $this, '_missing_plugins_warning' ] );
		}

		return $this->allOk;
	}


	/**
	 * Adds admin notice if the main plugin is missing
	 *
	 * @return void
	 */
	public function _missing_plugins_warning(): void {

		$missing = '';
		$counter = 0;
		foreach ( $this->missing as $title => $url ) {
			$counter++;
			if ( count( $this->missing ) == $counter ) {
				$sep = '';
			} elseif ( count( $this->missing ) - 1 == $counter ) {
				$sep = ' ' . __( 'and', 'rtcl-faq' ) . ' ';
			} else {
				$sep = ', ';
			}
			if ( 'Classified Listing' === $title ) {
				$missing .= '<a class="thickbox open-plugin-details-modal" href="' . $url . '">' . $title . '</a>' . $sep;
			} else {
				$missing .= '<a href="' . $url . '">' . $title . '</a>' . $sep;
			}
		}
		?>

		<div class="message error">
			<p>
			<?php
			echo wp_kses(
				sprintf(
					__(
						'<strong>Classified Listing - Tap Payment</strong> is enabled but not effective. It requires %s in order to work.',
						'rtcl-faq'
					),
					$missing
				),
				[
					'strong' => [],
					'a'      => [
						'href'  => true,
						'class' => true,
					],
				]
			);
			?>
					</p>
		</div>
		<?php
	}

	/**
	 * Admin notice if the RTCL plugin is old
	 *
	 * @return void
	 */
	public function _old_rtcl_warning() {
		$link    = esc_url(
			add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => 'classified-listing',
					'TB_iframe' => 'true',
					'width'     => '640',
					'height'    => '500',
				),
				admin_url( 'plugin-install.php' )
			)
		);
		$message
			= wp_kses(
				__(
					sprintf(
						'<strong>Classified Listing - Tap Payment</strong> is enabled but not effective. It is not compatible with <a class="thickbox open-plugin-details-modal" href="%1$s">Classified Listing</a> versions prior %2$s.',
						$link,
						self::MIN_RTCL
					),
					'rtcl-faq'
				),
				[
					'strong' => [],
					'a'      => [
						'href'  => true,
						'class' => true,
					],
				]
			);

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}
}
