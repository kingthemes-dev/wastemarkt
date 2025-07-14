<?php
/**
 * Dependencies class.
 *
 * @package RadiusTheme\BBoss
 */
namespace RadiusTheme\COUPON\Controllers\Admin;

use Rtcl;
use RadiusTheme\COUPON\Traits\SingletonTrait;

/**
 * Frontend Controller Class.
 */
class AdminNotices {
	/**
	 * Singleton Function.
	 */
	use SingletonTrait;
	/**
	 * Minimum Version
	 */
	const MIN_RTCL = '2.2.13';
	/**
	 * Missing required files
	 *
	 * @var array
	 */
	private $missing = [];
	/**
	 * Allowed or not.
	 *
	 * @var boolean
	 */
	private $allOk = true;
	/**
	 * Initialize function
	 *
	 * @return void
	 */
	public function init() {
		$this->check();
	}
	/**
	 * Check Plugin compatibility.
	 *
	 * @return bool
	 */
	public function check() {

		if ( ! class_exists( Rtcl::class ) ) {
			$link                                = esc_url(
				add_query_arg(
					[
						'tab'       => 'plugin-information',
						'plugin'    => 'classified-listing',
						'TB_iframe' => 'true',
						'width'     => '640',
						'height'    => '500',
					],
					admin_url( 'plugin-install.php' )
				)
			);
			$this->missing['Classified Listing'] = $link;
			$this->allOk                         = false;
		} elseif ( defined( 'RTCL_VERSION' ) && version_compare( RTCL_VERSION, self::MIN_RTCL, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'old_rtcl_warning' ] ); 
			$this->allOk = false;
		}
		
		// Check if required plugins installed and activated.
		if ( ! empty( $this->missing ) ) {
			add_action( 'admin_notices', [ $this, 'missing_plugins_warning' ] );
		}

		return $this->allOk;
	}

	/**
	 * Adds admin notice.
	 *
	 * @return void
	 */
	public function missing_plugins_warning() {

		$missing = '';
		$counter = 0;
		foreach ( $this->missing as $title => $url ) {
			$counter++;
			if ( count( $this->missing ) === $counter ) {
				$sep = '';
			} elseif ( count( $this->missing ) - 1 === $counter ) {
				$sep = ' ' . esc_html__( 'and', 'rtcl-coupon' ) . ' ';
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
		<div class="notice notice-error">
			<p>
				<strong>Classified Listing – Coupon</strong> is enabled but not effective. It requires 
				<?php
				echo wp_kses(
					$missing,
					[
						'strong' => [],
						'a'      => [
							'href'  => true,
							'class' => true,
						],
					]
				);
				?>
				in order to work.
			</p>
		</div>
		<?php
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function old_rtcl_warning() {
		$link = add_query_arg(
			[
				'tab'       => 'plugin-information',
				'plugin'    => 'classified-listing',
				'TB_iframe' => 'true',
				'width'     => '640',
				'height'    => '500',
			],
			admin_url( 'plugin-install.php' )
		);
		?>
		<div class="notice notice-error">
			<p>
				<strong>Classified Listing – Coupon</strong> is enabled but some feature will not effective. It is not fully compatible with <a class="thickbox open-plugin-details-modal" href="<?php echo esc_url( $link ); ?>">Classified Listing</a> versions prior <?php echo esc_html( self::MIN_RTCL ); ?>.
			</p>
		</div>
		<?php
	}


}
