<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\ClassiList;

use Rtcl\Models\Listing;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;

if (!class_exists('RtclPro')) return;

$id = get_the_id();
$listing = new Listing( $id );
$email   = get_post_meta( $id, 'email', true );
$website = get_post_meta( $id, 'website', true );
$phone    = get_post_meta( $id, 'phone', true );
$whatsapp = get_post_meta( $id, '_rtcl_whatsapp_number', true );
$has_contact_form        = Functions::get_option_item( 'rtcl_moderation_settings', 'has_contact_form', false, 'checkbox');
$alternate_contact_form  = Functions::get_option_item( 'rtcl_moderation_settings', 'alternate_contact_form_shortcode');


$status = apply_filters( 'rtcl_user_offline_text', 'offline' );
$status_text = esc_html__( 'Offline Now', 'listygo' );
if ( Fns::is_online( $listing->get_owner_id() ) ) {
	$status = apply_filters( 'rtcl_user_online_text', 'online' );
	$status_text = esc_html__( 'Online Now', 'listygo' );
}
?>
<div class="classified-seller-info widget">
	<h3 class="widgettitle"><?php esc_html_e( 'Seller Information', 'classilist' );?></h3>
	<div class="rtin-box">

		<?php if ( $listing->can_show_user() ): ?>
			<div class="rtin-box-each media rtin-name">
				<div class="rtin-left pull-left">
					<i class="fa fa-user" aria-hidden="true"></i>
					<span class="<?php echo esc_attr( $status ); ?> js-btn-tooltip" data-toggle="tooltip" data-placement="top" data-trigger="hover" title="<?php echo esc_attr( $status_text ); ?>"></span>
				</div>
				<div class="media-body">
					<div class="rtin-label"><?php esc_html_e( 'Posted By', 'classilist' );?></div>
					<div class="rtin-title">
						<?php $listing->the_author();?>
						<?php do_action('rtcl_after_author_meta', $listing->get_owner_id() ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if (Fns::registered_user_only('listing_seller_information') && !is_user_logged_in()) { ?>
            <p class="login-message"><?php echo wp_kses(sprintf(__("Please <a href='%s'>login</a> to view the seller information.", "classima"), esc_url(Link::get_my_account_page_link())), ['a' => ['href' => []]]); ?></p>
        <?php } else { ?>

			<?php if ( $address = Listing_Functions::get_single_contact_address( $listing ) ): ?>
				<div class="rtin-box-each media rtin-location">
					<div class="rtin-left pull-left"><i class="fas fa-map-marker-alt"></i></div>
					<div class="media-body">
						<div class="rtin-label"><?php esc_html_e( 'Location', 'classilist' );?></div>
						<div class="rtin-title"><?php echo wp_kses_post( $address );?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $phone || $whatsapp ): ?>
				<div class="rtin-box-each media rtin-phone">
					<div class="rtin-left pull-left"><i class="fas fa-mobile-alt"></i></div>
					<div class="media-body">
						<div class="rtin-label"><?php esc_html_e( 'Contact Number', 'classilist' ); ?></div>
						<div class="rtin-title"><?php Listing_Functions::the_phone( $phone, $whatsapp ); ?></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $website ): ?>
				<div class="rtin-box-each media rtin-website">
					<div class="rtin-left pull-left"><i class="fa fa-globe" aria-hidden="true"></i></div>
					<div class="media-body">
						<div class="rtin-label"><?php esc_html_e( 'Visit Website', 'classilist' ); ?></div>
						<div class="rtin-title"><a href="<?php echo esc_url( $website ); ?>" target="_blank"><?php esc_html_e( 'Click Here', 'classilist' ); ?></a></div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			if (Fns::is_enable_chat() && ((is_user_logged_in() && $listing->get_author_id() !== get_current_user_id()) || !is_user_logged_in())):
				$chat_btn_class = ['rtcl-chat-link'];
				$chat_url = Link::get_my_account_page_link();
				if (is_user_logged_in()) {
					$chat_url = '#';
					array_push($chat_btn_class, 'rtcl-contact-seller');
				} else {
					array_push($chat_btn_class, 'rtcl-no-contact-seller');
				}
			?>
				<div class="rtin-box-each media rtin-chat">
					<div class="rtin-left pull-left"><i class="fa fa-comments" aria-hidden="true"></i></div>
					<div class="media-body">
						<div class="rtin-title"><a class="<?php echo esc_attr(implode(' ', $chat_btn_class)); ?>" data-listing_id="<?php the_ID(); ?>" href="<?php echo esc_url($chat_url) ?>"><?php esc_html_e( 'Chat with Seller', 'classilist' ); ?></a></div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $has_contact_form && ( $email || $alternate_contact_form ) ) : ?>
				<div class="rtin-box-each media rtin-email">
					<div class="rtin-left pull-left"><i class="fa fa-envelope" aria-hidden="true"></i></div>
					<div class="media-body">
						<div class="rtin-title"><a data-toggle="modal" data-target="#classilist-mail-to-seller" href="#"><?php esc_html_e( 'Email to Seller', 'classilist' ); ?></a></div>
					</div>
				</div>
			<?php endif; ?>
		<?php } ?>
	</div>
</div>