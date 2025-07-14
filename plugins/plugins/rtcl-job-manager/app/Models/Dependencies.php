<?php

namespace RtclJobManager\Models;

class Dependencies {
	const MIN_RTCL = '3.0.12';

	private static $singleton = false;
	private array $missing = [];
	private bool $allOk = true;

	/**
	 * Create an inaccessible constructor.
	 */
	final private function __construct() {
	}


	/**
	 * Fetch an instance of the class.
	 */
	public static function getInstance() {
		if ( self::$singleton === false ) {
			self::$singleton = new self();
		}

		return self::$singleton;
	}

	/**
	 * @return bool
	 */
	public function check(): bool {

		if ( ! class_exists( \Rtcl::class ) ) {
			$link                                = esc_url(
				add_query_arg(
					array(
						'tab'       => 'plugin-information',
						'plugin'    => 'rtcl-job-manager',
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
	 * Adds admin notice.
	 */
	public function _missing_plugins_warning(): void {

		$missing = '';
		$counter = 0;
		foreach ( $this->missing as $title => $url ) {
			$counter ++;
			if ( $counter == sizeof( $this->missing ) ) {
				$sep = '';
			} elseif ( $counter == sizeof( $this->missing ) - 1 ) {
				$sep = ' ' . __( 'and', 'rtcl-job-manager' ) . ' ';
			} else {
				$sep = ', ';
			}
			$missing .= '<a class="thickbox open-plugin-details-modal" href="' . $url . '">' . $title . '</a>' . $sep;
		}
		?>

        <div class="message error">
            <p>
				<?php
				echo wp_kses(
					sprintf(
						__(
							'<strong>Classified Listing - Job Manager</strong> is enabled but not effective. It requires %s in order to work.',
							'rtcl-job-manager'
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

	public function _old_rtcl_warning(): void {
		$link = esc_url(
			add_query_arg(
				array(
					'tab'       => 'plugin-information',
					'plugin'    => 'rtcl-job-manager',
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
					'<strong>Classified Listing - Job Manager</strong> is enabled but not effective. It is not compatible with <a class="thickbox open-plugin-details-modal" href="%1$s">Classified Listing</a> versions prior %2$s.',
					$link,
					self::MIN_RTCL
				),
				'rtcl-job-manager'
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
