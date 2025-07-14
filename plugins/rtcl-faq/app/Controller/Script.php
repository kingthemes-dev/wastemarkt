<?php

namespace RtclFaq\Controller;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclFaq\Traits\SingletonTraits;

/**
 * Script class
 */
class Script {
	use SingletonTraits;

	/**
	 * Class Constructor
	 */
	public function __construct() {
		// Load Front-end script.
		add_action( 'wp_footer', [ __CLASS__, 'frontend_faq_css' ] );

		// Load Admin Scripts.
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'meta_form_assets' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'meta_form_assets' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'frontend_asset' ] );
	}



	/**
	 * Load Script
	 *
	 * @param $screen
	 *
	 * @return void
	 */
	public static function meta_form_assets() {
		global $pagenow, $post_type;
		$listing_faq_limit = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_faq_limit' ) ?? '0';
		$is_listing_edit   = in_array( $pagenow, [ 'edit.php', 'post.php', 'post-new.php' ] ) && rtcl()->post_type == $post_type;

		if ( Functions::is_listing_form_page() || $is_listing_edit ) {
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_style( 'rtcl-faq-meta', RTCL_FAQ_URL . '/assets/css/faq-meta.css', '', '1.0.0' );
			wp_enqueue_script( 'rtcl-faq-meta', RTCL_FAQ_URL . '/assets/js/faq-meta.js', [ 'jquery-ui-draggable' ], '1.0.0', true );
			wp_localize_script(
				'rtcl-faq-meta',
				'rtclFaq',
				[
					'faq_limit' => $listing_faq_limit,
				]
			);
		}
	}

	/**
	 * FAQ CSS
	 *
	 * @return void
	 */
	public static function frontend_asset() {
		if ( is_singular( 'rtcl_listing' ) ) {
			wp_enqueue_style( 'rtcl-faq', RTCL_FAQ_URL . '/assets/css/faq.css', '', '1.0.0' );
		}
	}

	/**
	 * FAQ CSS
	 *
	 * @return void
	 */
	public static function frontend_faq_css() {
		$listing_faq_close_others = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_faq_close_others' ) ?? 'no';

		?>
		<script>
			jQuery(document).ready(function () {
                var faqCollapseable = '<?php echo esc_html($listing_faq_close_others) ?>';
                jQuery(".rtcl-faq-accordion").each(function(){
                    if(jQuery(this).hasClass('active')){
                        jQuery(this).next('.panel').slideDown(200);
                    }
                    jQuery(this).on('click', function (e) {
                        e.preventDefault();

                        if(jQuery(this).hasClass('active')){
                            // If this item is already active, just slide it up and remove the class
                            jQuery(this).removeClass('active');
                            jQuery(this).next('.panel').slideUp(200);
                        } else {
                            // Otherwise, proceed with the toggle logic
                            if('yes' == faqCollapseable) {
                                jQuery(".rtcl-faq-accordion.active + .panel").slideUp(200);
                                jQuery(".rtcl-faq-accordion.active").removeClass('active');
                            }
                            jQuery(this).addClass('active');
                            jQuery(this).next('.panel').slideDown(200);
                        }
                    });
                });
            })
		</script>
		<?php
	}
}