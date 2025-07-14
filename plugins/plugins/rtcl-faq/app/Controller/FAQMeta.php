<?php

namespace RtclFaq\Controller;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use RtclFaq\Traits\SingletonTraits;
use RtclFaq\Helpers\Fns;

/**
 * FilterHooks Class
 */
class FAQMeta {

	use SingletonTraits;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'rtcl_listing_details_meta_box', [ __CLASS__, 'add_faq_meta' ] );
		add_action( 'rtcl_listing_update_metas_at_admin', [ __CLASS__, 'save_faq_meta' ], 10, 2 );
		add_action( 'rtcl_listing_form_after_save_or_update', [ __CLASS__, 'save_faq_meta_form' ], 10, 5 );
	}

	/**
	 * Add FAQ Meta
	 *
	 * @return void
	 */
	public static function add_faq_meta() {

		if ( ! Fns::is_active_faq() ) {
			return;
		}

		add_meta_box(
			'rtcl_listing_faq',
			__( 'Listing FAQ', 'rtcl-faq' ),
			[ __CLASS__, 'listing_faq' ],
			rtcl()->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Listing FAQ
	 *
	 * @param $post
	 *
	 * @return void
	 */
	public static function listing_faq( $post ) {
		$listing_id = $post->ID;
		$faqs       = get_post_meta( $listing_id, 'rtcl_faqs', true );
		?>
		<div class="form-group">
			<div class="rtcl-faq-wrapper">
				<div id="rtcl-faq-items">
					<?php
					if ( $faqs ) :
						foreach ( $faqs as $index => $faq ) :
							?>
							<div class="faq-item">
								<textarea class="faq-title-input" name="rtcl_faq_title[]" rows="4"
										  placeholder="<?php esc_attr_e( 'FAQ Title', 'rtcl-faq' ); ?>"><?php echo esc_textarea( $faq['title'] ); ?></textarea>
								<textarea class="faq-content-input" name="rtcl_faq_content[]" rows="4"
										  placeholder="<?php esc_attr_e( 'FAQ Content', 'rtcl-faq' ); ?>"><?php echo esc_textarea( $faq['content'] ); ?></textarea>
								<input type="hidden" class="faq-item-index" name="rtcl_faq_index[]"
									   value="<?php echo esc_attr( $index ); ?>">
								<button class="rtcl-remove-faq"><?php esc_html_e( 'Remove', 'rtcl-faq' ); ?></button>
								<span class="rtcl-faq-move">☰</span>
							</div>
							<?php
						endforeach;
					else :
						?>
						<div class="faq-item">
							<textarea class="faq-title-input" name="rtcl_faq_title[]" rows="4"
									  placeholder="<?php esc_attr_e( 'FAQ Title', 'rtcl-faq' ); ?>"></textarea>
							<textarea class="faq-content-input" name="rtcl_faq_content[]" rows="4"
									  placeholder="<?php esc_attr_e( 'FAQ Content', 'rtcl-faq' ); ?>"></textarea>
							<input type="hidden" class="faq-item-index" name="rtcl_faq_index[]" value="0">
							<button class="rtcl-remove-faq"><?php esc_html_e( 'Remove', 'rtcl-faq' ); ?></button>
							<span class="rtcl-faq-move">☰</span>
						</div>
						<?php
					endif;

					?>
				</div>

				<div class="faq-bottom-wrapper">
					<button id="add-rtcl-faq" class="add-faq-button"><?php esc_html_e( 'Add New FAQ', 'rtcl-faq' ); ?></button>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Save FAQ meta value
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function save_faq_meta( $post_id, $post ) {
		if ( ! Fns::is_active_faq() ) {
			return $post_id;
		}
		$faqs = [];

		// No need to check the nonce because the nonce has checked by the hook.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['rtcl_faq_title'] ) && ! empty( $_POST['rtcl_faq_content'] ) ) {

			$titles   = $_POST['rtcl_faq_title']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$contents = $_POST['rtcl_faq_content']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

			foreach ( $titles as $index => $title ) {
				if ( empty( $title ) ) {
					continue;
				}
				$faqs[] = [
					'title'   => sanitize_text_field( $title ),
					'content' => sanitize_textarea_field( $contents[ $index ] ),
				];
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		update_post_meta( $post_id, 'rtcl_faqs', $faqs );
	}


	/**
	 * Save front-end form faq
	 *
	 * @param $listing
	 * @param $type
	 * @param $cat_id
	 * @param $new_listing_status
	 * @param $request_data
	 *
	 * @return void
	 */
	public static function save_faq_meta_form( $listing, $type, $cat_id, $new_listing_status, $request_data = [ 'data' => '' ] ) {
		if ( ! Fns::is_active_faq() ) {
			return;
		}
		$data = $request_data['data'];

		$faqs = [];
		if ( ! empty( $data['rtcl_faq_title'] ) && ! empty( $data['rtcl_faq_content'] ) ) {

			$titles   = $data['rtcl_faq_title']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$contents = $data['rtcl_faq_content']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

			foreach ( $titles as $index => $title ) {
				if ( empty( $title ) ) {
					continue;
				}
				$faqs[] = [
					'title'   => sanitize_text_field( $title ),
					'content' => sanitize_textarea_field( $contents[ $index ] ),
				];
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		update_post_meta( $listing->get_id(), 'rtcl_faqs', $faqs );
	}
}
