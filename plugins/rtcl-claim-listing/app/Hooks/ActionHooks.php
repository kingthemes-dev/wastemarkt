<?php

namespace RtclClaimListing\Hooks;

use Rtcl\Helpers\Functions;
use RtclClaimListing\Helpers\Functions as ClaimFunctions;

class ActionHooks {

	public static function init() {
		// add claim listing action
		add_action( 'rtcl_single_action_after_list_item', [ __CLASS__, 'get_listing_claim_form' ] );
		// add claim listing form
		add_action( 'rtcl_single_listing_after_action', [ __CLASS__, 'get_listing_form_popup' ] );
		add_action( 'rtcl_listing_badges', [ __CLASS__, 'claim_badge' ], 20 );
	}

	public static function get_listing_claim_form() {
		if ( ClaimFunctions::claim_listing_enable() ) {
			Functions::get_template( "claim/claim-listing-action", '', '', rtclClaimListing()->get_plugin_template_path() );
		}
	}

	public static function get_listing_form_popup( $listing_id ) {
		if ( is_user_logged_in() ) {
			Functions::get_template( "claim/claim-popup-form", [
				'listing_id' => $listing_id,
				'user_id'    => get_current_user_id(),
			], '', rtclClaimListing()->get_plugin_template_path() );
		}
	}

	public static function claim_badge( $listing ) {
		if ( Functions::is_listing() && ClaimFunctions::is_enable_claim_badge() ) {
			$claimed = get_post_meta( $listing->get_id(), 'rtcl_claimed_listing', true );
			if ( 'yes' === $claimed ) {
				?>
                <span class="badge rtcl-claim-badge"><?php esc_html_e( 'Claimed', 'rtcl-claim-listing' ); ?></span>
				<?php
			}
		}
	}

}