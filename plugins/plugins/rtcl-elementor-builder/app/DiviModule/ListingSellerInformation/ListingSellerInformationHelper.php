<?php

namespace  RtclElb\DiviModule\ListingSellerInformation;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Text;
use Rtcl\Models\Listing;
use RtclPro\Helpers\Fns;

Class ListingSellerInformationHelper {
	
	public $settings;
	public function __construct($settings) {
		$this->settings = $settings;
	}
	public function seller_contact_email( $listing ) {
		$settings = $this->settings;
		if ( is_user_logged_in() && get_current_user_id() === $listing->get_author_id() ) {
			return;
		}
		if (!empty($settings['rtcl_contact_btn_text'])){
			$contact_btn_text = $settings['rtcl_contact_btn_text'];
		} else {
			$contact_btn_text = Text::get_single_listing_email_button_text();
		}
		?>
		<div class='rtcl-do-email list-group-item'>
			<div class='media'>
				<span class='rtcl-icon rtcl-icon-mail mr-2'></span>
				<div class='media-body'>
					<a class="rtcl-do-email-link" href='#'>
						<span><?php echo esc_html( $contact_btn_text ); ?></span>
					</a>
				</div>
			</div>
			<?php $listing->email_to_seller_form(); ?>
		</div>
		<?php
	}

	public function add_chat_link( $listing ) {
		$settings = $this->settings;
		$chat_link_available = Fns::is_enable_chat() && is_a( $listing, Listing::class )
			&& ( ( is_user_logged_in() && $listing->get_author_id() !== get_current_user_id() )
				|| ! is_user_logged_in() ) ;
		$chat_link_available = apply_filters( 'rtcl_is_chat_link_available', $chat_link_available, $listing );
		if ( $chat_link_available ) {
			$chat_btn_class = [ 'rtcl-chat-link' ];
			$chat_url       = Link::get_my_account_page_link();
			if ( is_user_logged_in() ) {
				$chat_url = '#';
				array_push( $chat_btn_class, 'rtcl-contact-seller' );
			} else {
				array_push( $chat_btn_class, 'rtcl-no-contact-seller' );
			}

			if ($settings['rtcl_chat_btn_text']){
				$chat_text = $settings['rtcl_chat_btn_text'];
			} else {
				$chat_text = esc_html__( 'Chat', 'rtcl-elementor-builder' );
			}
			?>
			<div class='rtcl-contact-seller list-group-item'>
				<a class="<?php echo esc_attr( implode( ' ', $chat_btn_class ) ) ?>"
				   href="<?php echo esc_url( $chat_url ) ?>"
				   data-listing_id="<?php echo absint( $listing->get_id() ) ?>">
					<i class='rtcl-icon rtcl-icon-chat mr-1'> </i><?php echo esc_html( $chat_text ); ?>
				</a>
			</div>
		<?php }
	}

	/**
	 * @param Listing $listing
	 */
	public function seller_website( $listing ) {
		$settings = $this->settings;
		if ( is_a( $listing, Listing::class ) && $website = get_post_meta( $listing->get_id(), 'website', true ) ) {

			if ($settings['rtcl_show_seller_website_text']){
				$website_text = $settings['rtcl_show_seller_website_text'];
			} else {
				$website_text = esc_html__( 'Visit Website', 'rtcl-elementor-builder' );
			}

			?>
			<div class='rtcl-website list-group-item'>
				<a class="rtcl-website-link rtcl-btn btn btn-primary" href="<?php echo esc_url( $website ); ?>"
				   target="_blank"<?php echo Functions::is_external( $website ) ? ' rel="nofollow"' : ''; ?>>
					<span class='rtcl-icon rtcl-icon-globe text-white'></span><?php echo esc_html( $website_text ); ?>
				</a>
			</div>
			<?php
		}
	}

	public function listing_user_online_status( $listing ) {
		$settings = $this->settings;

		if ($settings['rtcl_offline_status_text']){
			$offline_text = $settings['rtcl_offline_status_text'];
		} else {
			$offline_text = esc_html__( 'Offline Now', 'rtcl-elementor-builder' );
		}

		if ($settings['rtcl_online_status_text']){
			$online_text = $settings['rtcl_online_status_text'];
		} else {
			$online_text = esc_html__( 'Online Now', 'rtcl-elementor-builder' );
		}

		$status_text  = apply_filters( 'rtcl_user_offline_text', $offline_text );
		$status       = Fns::is_online( $listing->get_owner_id() );
		$status_class = $status ? 'online' : 'offline';
		if ( $status ) {
			$status_text = apply_filters( 'rtcl_user_online_text', $online_text );
		}
		?>
		<div class="list-group-item rtcl-user-status <?php echo esc_attr( $status_class ); ?>">
			<span><?php echo esc_html( $status_text ); ?></span>
		</div>
		<?php
	}

}