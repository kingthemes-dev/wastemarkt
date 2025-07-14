<?php
/**
 * Main Elementor ListingSellerInformation Class
 *
 * ListingSellerInformation main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Rtcl\Helpers\Text;
use Rtcl\Helpers\Link;
use RtclPro\Helpers\Fns;
use Rtcl\Models\Listing;
use Rtcl\Helpers\Functions;
use Rtcl\Controllers\Hooks\TemplateHooks;
use RtclElb\Widgets\WidgetSettings\ListingSellerInfoSettings;
use RtclPro\Controllers\Hooks\TemplateHooks as TemplateHooksPro;

/**
 * ListingSellerInformation class
 */
class ListingSellerInformation extends ListingSellerInfoSettings {
	
	/**
	 * Template builder related traits.
	 */
	// use ELTempleateBuilderTraits;
	
	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Seller Information', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-seller-information';
		parent::__construct( $data, $args );
	}

	/**
	 * Seller Contact Form
	 * 
	 * @param $listing
	 *
	 * @return void
	 */
	public function seller_contact_email( $listing ) {
		$settings = $this->get_settings();
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
		$settings = $this->get_settings();
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
		$settings = $this->get_settings();
		if ( is_a( $listing, Listing::class ) && $website = get_post_meta( $listing->get_id(), 'website', true ) ) {
			
			if ($settings['rtcl_show_seller_website_text']){
				$website_text = $settings['rtcl_show_seller_website_text'];
			} else {
				$website_text = esc_html__( 'Visit Website', 'rtcl-elementor-builder' );
			}
			
			?>
			<div class='rtcl-website list-group-item'>
				<a class="rtcl-website-link btn btn-primary" href="<?php echo esc_url( $website ); ?>"
				   target="_blank"<?php echo Functions::is_external( $website ) ? ' rel="nofollow"' : ''; ?>>
					<span class='rtcl-icon rtcl-icon-globe text-white'></span><?php echo esc_html( $website_text ); ?>
				</a>
			</div>
			<?php
		}
	}

	public function listing_user_online_status( $listing ) {
		$settings = $this->get_settings();
		
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

	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings       = $this->get_settings();
		add_filter( 'rtcl_is_chat_link_available', '__return_true' );
		
		/* === Contact Form === */
		remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_email' ], 30 );
		if ( $settings['rtcl_show_contact_form'] ) {
			add_action( 'rtcl_listing_seller_information', [ $this, 'seller_contact_email' ], 30 );
		}

		/* === Chat Form === */
		if ( class_exists( TemplateHooksPro::class ) ) {
			remove_action( 'rtcl_listing_seller_information', [ TemplateHooksPro::class, 'add_chat_link' ], 40 );
			if ( $settings['rtcl_add_chat_link'] ) {
				add_action( 'rtcl_listing_seller_information', [ $this, 'add_chat_link' ], 40 );
			}
		}
		
		/* === Seller Website === */
		remove_action( 'rtcl_listing_seller_information', [ TemplateHooks::class, 'seller_website' ], 50 );
		if ( $settings['rtcl_show_seller_website'] ) {
			add_action( 'rtcl_listing_seller_information', [ $this, 'seller_website' ], 50 );
		}
		
		/* === User Online Status === */
		if ( class_exists( TemplateHooksPro::class ) ) { // ✅ Works correctly
			remove_action( 'rtcl_listing_seller_information', [ TemplateHooksPro::class, 'add_user_online_status' ], 50 );
			if ( $settings['rtcl_add_user_online_status'] ) {
				add_action( 'rtcl_listing_seller_information', [ $this, 'listing_user_online_status' ], 50 );
			}
		}
		
		$template_style = 'single/seller-information';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_actions_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );

		if ( $settings['rtcl_show_contact_form'] ) {
			remove_action( 'rtcl_listing_seller_information', [ $this, 'seller_contact_email' ], 30 );
		}

		if ( class_exists( TemplateHooksPro::class ) ) {
			if ( $settings['rtcl_add_chat_link'] ) {
				remove_action( 'rtcl_listing_seller_information', [ $this, 'add_chat_link' ], 40 );
			}
		}

		if ( class_exists( TemplateHooksPro::class ) ) {
			if ( $settings['rtcl_add_user_online_status'] ) {
				remove_action( 'rtcl_listing_seller_information', [ $this, 'listing_user_online_status' ], 50 );
			}
		}

		if ( $settings['rtcl_show_seller_website'] ) {
			remove_action( 'rtcl_listing_seller_information', [ $this, 'seller_website' ], 50 );
		}

	}
}
