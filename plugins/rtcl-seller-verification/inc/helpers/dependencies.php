<?php

class RtclSvDependencies {
	const MIN_RTCL = '2.2.12';

	private static $singleton = false;
	/**
	 * @var bool
	 */
	private $allOk = true;

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
	public function check() {

		if ( defined( 'RTCL_VERSION' ) && version_compare( RTCL_VERSION, self::MIN_RTCL, '<' ) ) {
			add_action( 'admin_notices', [ $this, '_old_rtcl_warning' ] );
			$this->allOk = false;
		}

		return $this->allOk;
	}

	public function _old_rtcl_warning() {
		$link    = esc_url(
			add_query_arg(
				[
					'tab'       => 'plugin-information',
					'plugin'    => 'classified-listing',
					'TB_iframe' => 'true',
					'width'     => '640',
					'height'    => '500',
				], admin_url( 'plugin-install.php' )
			)
		);
		$message = wp_kses( __( sprintf( '<strong>Seller Verification</strong> is enabled but not effective. It is not compatible with <a class="thickbox open-plugin-details-modal" href="%1$s">Classified Listing</a> versions prior %2$s.',
			$link,
			self::MIN_RTCL
		), 'rtcl-seller-verification' ), [ 'strong' => [], 'a' => [ 'href' => true, 'class' => true ] ] );

		printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
	}
}