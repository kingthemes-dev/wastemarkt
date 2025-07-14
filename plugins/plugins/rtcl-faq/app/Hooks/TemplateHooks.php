<?php

namespace RtclFaq\Hooks;

use Rtcl\Helpers\Functions;
use RtclFaq\Helpers\Fns;
use RtclFaq\Traits\SingletonTraits;

/**
 * Shortcode class
 */
class TemplateHooks {
	use SingletonTraits;

	/**
	 * Class Constructor
	 */
	public function __construct() {
		add_action( 'init', [ __CLASS__, 'rtcl_hooks' ] );
	}


	/**
	 * Classified Hooks
	 *
	 * @return void
	 */
	public static function rtcl_hooks() {
		if ( ! Fns::is_active_faq() ) {
			return;
		}
		$listing_faq_title = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_faq_position' );
		if ( 'sidebar' === $listing_faq_title ) {
			add_action( 'rtcl_after_single_listing_sidebar', [ __CLASS__, 'rtcl_listing_faq' ], 25 );
		} else {
			add_action( 'rtcl_single_listing_content_end', [ __CLASS__, 'rtcl_listing_faq' ], 5 );
		}
		add_action( 'rtcl_single_listing_faq', [ __CLASS__, 'rtcl_listing_faq' ] );
		add_action( 'rtcl_listing_form', [ __CLASS__, 'listing_faq' ], 25 );
	}

	/**
	 *
	 * @param $listing
	 *
	 * @return void
	 */
	public static function rtcl_listing_faq( $listing ) {
		if ( $listing ) {
			$listing_id = is_object( $listing ) ? $listing->get_id() : $listing;
		} else {
			global $listing;
			$listing_id = $listing->get_id();
		}

		$faqs                 = get_post_meta( $listing_id, 'rtcl_faqs', true );
		$listing_faq_title    = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_faq_title' );
		$is_active_frist_item = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_faq_active_first_item' ) ?? 'no';

		if ( empty( $faqs ) ) {
			return;
		}
		do_action( 'rtcl_single_faq_start' );
		?>
		<div class="rtcl-faq-wrapper">
			<?php
			if ( $listing_faq_title ) {
				echo "<h3 class='rtcl-faq-title'>" . esc_html( $listing_faq_title ) . '</h3>';
			}

			foreach ( $faqs as $index => $faq ) :
				$active       = ( 'yes' == $is_active_frist_item && '0' == $index ) ? 'active' : '';
				$faq_settings = [
					'active'  => $active,
					'title'   => $faq['title'],
					'content' => $faq['content'],
				];
				Functions::get_template( 'listing/faq', $faq_settings, '', rtcl_faq()->get_plugin_template_path() );
				endforeach;

			?>
		</div>
		<?php
		do_action( 'rtcl_single_faq_end' );
	}


	/**
	 * Listing FAQ Form
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public static function listing_faq( $post_id ) {
		$data = [
			'post_id' => $post_id,
			'faqs'    => get_post_meta( $post_id, 'rtcl_faqs', true ),
		];

		Functions::get_template( 'listing-form/faq-form', $data, '', rtcl_faq()->get_plugin_template_path() );
	}
}
