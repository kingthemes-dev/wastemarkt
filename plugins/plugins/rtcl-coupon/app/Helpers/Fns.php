<?php
/**
 * Install class.
 *
 * @package RadiusTheme\COUPON
 */

namespace RadiusTheme\COUPON\Helpers;

/**
 * Helpers class.
 */
class Fns {

	/**
	 * Date to Timestamp
	 *
	 * @param [type] $date date.
	 * @return string||number
	 */
	public static function date_to_timestamp( $date ) {
		if ( '' !== $date ) {
			return strtotime( $date );
		}
		return '';
	}
	/**
	 * Date to Timestamp
	 *
	 * @param [type] $date date.
	 * @return string||number
	 */
	public static function timestamp_to_date( $date ) {
		if ( '' !== $date ) {
			return date( 'Y-m-d', $date );
		}
		return '';
	}

	/**
	 * Date to Timestamp
	 *
	 * @return object
	 */
	public static function get_pricing() {
		$regular_pricing = get_posts(
			apply_filters(
				'rtcl_coupon_get_all_pricing_query_args',
				[
					'post_type'        => rtcl()->post_type_pricing,
					'posts_per_page'   => - 1,
					'post_status'      => 'publish',
					'orderby'          => 'menu_order',
					'order'            => 'ASC',
					'suppress_filters' => false,
				]
			)
		);
		return $regular_pricing;
	}

	/**
	 * Pricing type Depended field.
	 *
	 * @param array $coupon_meta post meta.
	 * @return void
	 */
	public static function pricing_type_dependent_field( $coupon_meta = [] ) {
		$all_pricing = self::get_pricing(); 
		?>
		<p class="form-field">
			<label><?php esc_html_e( 'Include Pricing', 'rtcl-coupon' ); ?></label>
			<select multiple class="short coupon-select2" name="rtcl_pricing_include[]">
				<?php
				$pricing_include = ! empty( $coupon_meta['include_pricing'] ) ? maybe_unserialize( $coupon_meta['include_pricing'] ) : [];
				if ( is_array( $all_pricing ) && count( $all_pricing ) ) {
					foreach ( $all_pricing as $key => $pricing ) {
						$selected = in_array( $pricing->ID, $pricing_include ) ? 'selected' : '';
						?>
						<option <?php echo esc_html( $selected ); ?>  value="<?php echo absint( $pricing->ID ); ?>"> <?php echo esc_html( $pricing->post_title ); ?> </option>
						<?php
					}
				}
				?>
			</select>	
			<span class="rtcl-help-tip" data-tip="<?php esc_html_e( 'The coupon is only for selected Pricing.', 'rtcl-coupon' ); ?>" ></span>				
		</p>
		<p class="form-field">
			<label><?php esc_html_e( 'Exclude Pricing', 'rtcl-coupon' ); ?></label>
			<select multiple class="short coupon-select2" name="rtcl_pricing_exclude[]">
				<?php
				$pricing_exclude = ! empty( $coupon_meta['exclude_pricing'] ) ? maybe_unserialize( $coupon_meta['exclude_pricing'] ) : [];
				if ( is_array( $all_pricing ) && count( $all_pricing ) ) {
					foreach ( $all_pricing as $key => $pricing ) {
						$selected = in_array( $pricing->ID, $pricing_exclude ) ? 'selected' : '';
						?>
						<option <?php echo esc_html( $selected ); ?>  value="<?php echo absint( $pricing->ID ); ?>"> <?php echo esc_html( $pricing->post_title ); ?> </option>
						<?php
					}
				}
				?>
			</select>	
			<span class="rtcl-help-tip" data-tip="<?php esc_html_e( 'The coupon is not for selected Pricing.', 'rtcl-coupon' ); ?>" ></span>				

		</p>
		<?php
	}

	/**
	 * Share option Modal content
	 *
	 * @return void
	 */
	public static function reset_coupons_session() {
		rtcl()->session->set( 'rtcl_applied_coupon', '' );
		$total = [
			'subtotal'       => '',
			'discount_total' => '',
			'total'          => '',
		];
		rtcl()->session->set( 'rtcl_checkout_totals', $total );
	}


}
