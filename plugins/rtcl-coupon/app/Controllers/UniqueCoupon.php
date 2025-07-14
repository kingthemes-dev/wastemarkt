<?php
/**
 * Main initialization class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Controllers;

use WP_Query;
use RadiusTheme\COUPON\Traits\SingletonTrait;

/**
 * Class UniqueCoupon
 */
class UniqueCoupon {

	/**
	 * Singleton Function.
	 */
	use SingletonTrait;

	/**
	 * The post title to be checked
	 *
	 * @var  string
	 */
	public $post_title = '';

	/**
	 * Function Initialization.
	 *
	 * @return   void
	 */
	public function init() {
		// Check uniqueness, when post is edited.
		add_filter( 'admin_notices', [ $this, 'uniqueness_admin_notice' ] );
	}

	/**
	 * Show an initial warning, if the title of a saved post is not unique
	 *
	 * @wp-hook admin_notices
	 */
	public function uniqueness_admin_notice() {
		global $post, $pagenow;

		// Don't show an initial warning on a new post.
		if ( 'post.php' !== $pagenow ) {
			return;
		}
		// Don't show an initial warning on a new post.
		if ( rtcl_coupon()->post_type_coupon !== $post->post_type ) {
			return;
		}
		// Only enable it on new posts/pages/CPTs.
		// Show no warning, when the title is empty.
		if ( empty( $post->post_title ) ) {
			return;
		}

		// Build the necessary args for the initial uniqueness check.
		$args = [
			'post__not_in' => [ $post->ID ],
			'post_type'    => rtcl_coupon()->post_type_coupon,
			'post_title'   => $post->post_title,
		];

		$response = $this->check_uniqueness( $args );

		// Don't show a message on init, if title is unique.
		if ( 'error' !== $response['status'] ) {
			return;
		}

		echo '<div id="rtcl-unique-title-message" class="' . esc_attr( $response['status'] ) . '"><p>' . esc_html( $response['message'] ) . '</p></div>';
	}

	/**
	 * Check for the uniqueness of the post.
	 *
	 * @param array|string $args The WP_QUERY arguments array or query string.
	 *
	 * @return array The status and message for the response
	 */
	public function check_uniqueness( $args ) {

		// Use the posts_where hook to add thr filter for the post_title, as it is not available through WP_Query args.
		add_filter( 'posts_where', [ $this, 'post_title_where' ], 10, 1 );

		// Providing a filter to overwrite the search arguments.
		$args = apply_filters( 'rtcl_unique_title_checker_arguments', $args );

		$post_type_object        = get_post_type_object( $args['post_type'] );
		$post_type_singular_name = $post_type_object->labels->singular_name;
		$post_type_name          = $post_type_object->labels->name;
		// Set post title to be checked.
		$this->post_title = $args['post_title'];

		$query       = new WP_Query( $args );
		$posts_count = $query->post_count;

		if ( empty( $posts_count ) ) {
			$response = [
				'message' => esc_html__( 'The chosen title is unique.', 'rtcl-coupon' ),
				'status'  => 'updated',
			];
		} else {
			// Translators: %1$d: posts count, %2$s: post type singular name, %3$s: post type plural name.
			$message = esc_html( sprintf( _n( 'There is %1$d duplicate %2$s found. The latest one will apply.', 'There are %1$d other duplicate %3$s found. The latest one will apply.', $posts_count, 'rtcl-coupon' ), $posts_count, $post_type_singular_name, $post_type_name ) ); // phpcs:ignore WordPress.WP.I18n.MismatchedPlaceholders
			$response = [
				'message' => $message,
				'status'  => 'error',
			];
		}
		// Remove filter for post_title.
		remove_filter( 'posts_where', [ $this, 'post_title_where' ], 10 );
		return $response;
	}

	/**
	 * Add the filter for the post_title to the WHERE clause
	 *
	 * @wp-hook wp_ajax_(action)
	 *
	 * @global wpdb  $wpdb  The data base object
	 *
	 * @param string $where The WHERE clause.
	 *
	 * @return string The new WHERE clause
	 */
	public function post_title_where( $where ) {
		global $wpdb;
		return $where . " AND $wpdb->posts.post_title = '" . esc_sql( $this->post_title ) . "'";
	}
}
