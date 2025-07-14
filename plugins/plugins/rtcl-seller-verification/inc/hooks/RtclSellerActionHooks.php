<?php

use Rtcl\Helpers\Functions;

class RtclSellerActionHooks {

	public function __construct() {
		add_action( 'rtcl_account_my-documents_endpoint', [ __CLASS__, 'account_documents_endpoint' ] );
		add_action( 'rtcl_after_author_meta', [ __CLASS__, 'listing_verified_author' ], 10 );
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'listing_sidebar_verified_author' ], 5 );
		add_action( 'rtcl_after_store_title', [ __CLASS__, 'store_verified_sign' ] );
	}

	public static function account_documents_endpoint() {
		// Process output
		Functions::get_template( "myaccount/my-documents", '', '', rtclSellerVerification()->get_plugin_template_path() );
	}

	public static function listing_verified_author( $user_id ) {
		$status       = rtcl_sv_get_user_status( $user_id );
		$verifiedData = '';

		$iconClass = apply_filters( 'rtcl_sv_verified_icon_class', 'rtcl-icon-ok-circled' );

		if ( 1 === $status ) {
			$verifiedData .= "<span class='rtcl-sv-sign'><i class='" . esc_attr( $iconClass ) . "'></i><span class='verified-text'>" . esc_html__( 'Verified Seller', 'rtcl-seller-verification' ) . "</span></span>";
		}

		Functions::print_html( $verifiedData );
	}

	public static function store_verified_sign( $store ) {
		if ( is_object( $store ) ) {
			$user_id = $store->owner_id();
			if ( $user_id ) {
				self::listing_verified_author( $user_id );
			}
		}
	}

	public static function listing_sidebar_verified_author( $listing ) {
		$user_id = $listing->get_owner_id();

		$status       = rtcl_sv_get_user_status( $user_id );
		$verifiedData = '';

		if ( 1 === $status ) {
			$verifiedData .= "<div class='verified-author'><i class='rtcl-icon-ok-circled'></i><span class='verified-text'>" . esc_html__( 'Verified Seller', 'rtcl-seller-verification' ) . "</span></div>";
		}

		Functions::print_html( $verifiedData );
	}

}