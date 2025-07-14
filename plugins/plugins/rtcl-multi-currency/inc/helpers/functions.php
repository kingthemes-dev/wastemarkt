<?php

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// All function should have check and prefix rtcl_mc_

if ( ! function_exists( 'rtclmc_get_default_currency' ) ) {
	function rtclmc_get_default_currency() {
		$currency = Functions::get_option_item( 'rtcl_general_settings', 'currency', 'USD' );

		return [
			'code'          => $currency,
			'pos'           => Functions::get_option_item( 'rtcl_general_settings', 'currency_position', 'left' ),
			'rate'          => 1,
			'rate_fee'      => 0,
			'rate_fee_type' => 'percentage',
			'decimals'      => Functions::get_price_decimals(),
			'hide'          => false,
		];
	}
}


if ( ! function_exists( 'rtclmc_get_currency_fee_type_list' ) ) {
	function rtclmc_get_currency_fee_type_list() {
		return [
			[ 'label' => 'percentage', 'value' => '%' ],
			[ 'label' => 'fixed', 'value' => 'Fixed' ]
		];
	}
}
if ( ! function_exists( 'rtclmc_get_finance_api_list' ) ) {
	function rtclmc_get_finance_api_list() {
		return [
			[ "label" => __( "Select one", 'rtcl-multi-currency' ), "value" => "" ],
			[ "label" => __( "Google Finance", 'rtcl-multi-currency' ), "value" => "google" ],
			[ "label" => __( "Yahoo Finance", 'rtcl-multi-currency' ), "value" => "yahoo" ],
			[ "label" => __( "Cuex", 'rtcl-multi-currency' ), "value" => "cuex" ],
			[ "label" => __( "Wise (TransferWise)", 'rtcl-multi-currency' ), "value" => "wise" ],
		];
	}
}

if ( ! function_exists( 'rtclmc_get_exchange_update_duration_list' ) ) {
	function rtclmc_get_exchange_update_duration_list() {
		return [
			[ "label" => __( "No", 'rtcl-multi-currency' ), "value" => 0 ],
			[ "label" => __( "30 Minutes", 'rtcl-multi-currency' ), "value" => 1 ],
			[ "label" => __( "1 Hour", 'rtcl-multi-currency' ), "value" => 2 ],
			[ "label" => __( "6 Hours", 'rtcl-multi-currency' ), "value" => 3 ],
			[ "label" => __( "1 Day", 'rtcl-multi-currency' ), "value" => 4 ],
			[ "label" => __( "2 Days", 'rtcl-multi-currency' ), "value" => 5 ],
			[ "label" => __( "3 Days", 'rtcl-multi-currency' ), "value" => 6 ],
			[ "label" => __( "4 Days", 'rtcl-multi-currency' ), "value" => 7 ],
			[ "label" => __( "5 Days", 'rtcl-multi-currency' ), "value" => 8 ],
			[ "label" => __( "6 Days", 'rtcl-multi-currency' ), "value" => 9 ],
			[ "label" => __( "1 Week", 'rtcl-multi-currency' ), "value" => 10 ],
			[ "label" => __( "2 Weeks", 'rtcl-multi-currency' ), "value" => 11 ],
		];
	}
}
if ( ! function_exists( 'rtclmc_get_sidebar_style_list' ) ) {
	function rtclmc_get_sidebar_style_list() {
		return [
			[ "label" => __( "Default", 'rtcl-multi-currency' ), "value" => 0 ],
			[ "label" => __( "Symbol", 'rtcl-multi-currency' ), "value" => 1 ],
			[ "label" => __( "Flag", 'rtcl-multi-currency' ), "value" => 2 ],
			[ "label" => __( "Flag + Currency code", 'rtcl-multi-currency' ), "value" => 3 ],
			[ "label" => __( "Flag + Currency symbol", 'rtcl-multi-currency' ), "value" => 4 ]
		];
	}
}
if ( ! function_exists( 'rtclmc_get_design_position_list' ) ) {
	function rtclmc_get_design_position_list() {
		return [
			[ "label" => __( "Left", 'rtcl-multi-currency' ), "value" => 'left' ],
			[ "label" => __( "Right", 'rtcl-multi-currency' ), "value" => 'right' ]
		];
	}
}
if ( ! function_exists( 'rtclmc_get_auto_detect_list' ) ) {
	function rtclmc_get_auto_detect_list() {
		return [
			[ 'value' => '', 'label' => __( "No", "rtcl-multi-currency" ) ],
			[ 'value' => 'auto', 'label' => __( "Auto select currency", "rtcl-multi-currency" ) ],
			[ 'value' => 'approximate', 'label' => __( "Approximate Price", "rtcl-multi-currency" ) ],
			[ 'value' => 'polylang', 'label' => __( "Language Polylang", "rtcl-multi-currency" ) ],
			[ 'value' => 'translatepress', 'label' => __( "TranslatePress Multilingual", "rtcl-multi-currency" ) ],
		];
	}
}

if ( ! function_exists( 'rtclmc_get_listing_price_currency' ) ) {
	/**
	 * @param Listing $listing
	 *
	 * @return array[]
	 */
	function rtclmc_get_listing_price_currency( $listing ) {
		return get_post_meta( $listing->get_id(), 'rtcl_price_currency', true );
	}
}