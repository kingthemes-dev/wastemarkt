<?php
/**
 * The template to display the Social profile
 *
 * @author  RadiousTheme
 * @package classified-listing/Templates
 * @var Rtcl\Models\Listing $listing
 */

use Rtcl\Controllers\SocialProfilesController;

?>
<?php

add_filter(
	'rtcl_social_profile_label',
	function( $text ) use ( $instance ) {
		if ( ! $instance['rtcl_hide_label'] || $instance['rtcl_hide_label'] === 'off' ) {
			$text = '';
		} elseif ( $instance['label_text'] ) {
			$text = $instance['label_text'];
		}
		return $text;
	}
);
add_filter(
	'rtcl_social_profiles_list',
	function( $options ) use ( $instance ) {
		if ( ! $instance['rtcl_hide_facebook'] || $instance['rtcl_hide_facebook'] === 'off') {
			unset( $options['facebook'] );
		}
		if ( ! $instance['rtcl_hide_twitter'] || $instance['rtcl_hide_twitter'] === 'off') {
			unset( $options['twitter'] );
		}
		if ( ! $instance['rtcl_hide_youtube'] || $instance['rtcl_hide_youtube'] === 'off') {
			unset( $options['youtube'] );
		}
		if ( ! $instance['rtcl_hide_instagram'] || $instance['rtcl_hide_instagram'] === 'off') {
			unset( $options['instagram'] );
		}
		if ( ! $instance['rtcl_hide_linkedIn'] || $instance['rtcl_hide_linkedIn'] === 'off') {
			unset( $options['linkedin'] );
		}
		if ( ! $instance['rtcl_hide_pinterest'] || $instance['rtcl_hide_pinterest'] === 'off') {
			unset( $options['pinterest'] );
		}
		if ( ! $instance['rtcl_hide_reddit'] || $instance['rtcl_hide_reddit'] === 'off') {
			unset( $options['reddit'] );
		}
		return $options;
	}
);

?>
<div class="rtcl el-single-addon social-profile">
	<?php do_action('rtcl_single_listing_social_profiles', $listing); ?>
</div>
