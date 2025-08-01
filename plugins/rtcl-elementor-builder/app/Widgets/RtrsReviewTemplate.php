<?php
/**
 * Main Elementor ListingReview Class
 *
 * ListingReview main class
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

use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\RtrsReviewSettings;

/**
 * ListingReview class
 */
class RtrsReviewTemplate extends RtrsReviewSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Review Schema - Listing Review', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rtrs-listing-review';
		parent::__construct( $data, $args );
	}
	/**
	 * Rravatar Output.
	 *
	 * @return mixed
	 */
	public function gravatar_wrapper( $gravatar ) {
		return '<div class="gravatar-img">' . $gravatar . '</div>';
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		global $listing, $post, $comments;
		$_post    = $post;
		$_listing = $listing;
		$listing  = $this->listing;
		if ( is_singular( 'rtcl_builder' ) ) {
			$post = get_post( $this->listing->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
		add_filter( 'rtcl_review_gravatar_image', [ $this, 'gravatar_wrapper' ] );
		$settings       = $this->get_settings();
		$template_style = 'single/review-schema-widgets';
		$data           = [
			'template'              => $template_style,
			'instance'              => $settings,
			'listing'               => $this->listing,
			'default_template_path' => rtclElb()->get_plugin_template_path(),
		];
		$data           = apply_filters( 'rtcl_el_listing_page_review_data', $data );
		Functions::get_template( $data['template'], $data, '', $data['default_template_path'] );
		$listing = $_listing; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$post    = $_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
}
