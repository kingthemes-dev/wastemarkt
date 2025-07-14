<?php

use RadiusTheme\COUPON\Helpers\Fns;
use Rtcl\Resources\Options;
use RadiusTheme\COUPON\Models\Coupon;


$coupon_code = get_the_title( get_the_ID() );
$coupon      = new Coupon( $coupon_code );
$coupon_meta = $coupon->get_coupon_meta();
// $coupon_meta = ! empty( $coupon_meta[0] ) ? $coupon_meta[0] : [];

?>
<div class="rtcl-coupon-metabox-wrapper">
	<div class="tab">
		<button data-contentid="General" class="tablinks active" id="defaultOpen"><?php esc_html_e( 'General', 'rtcl-coupon' ); ?></button>
		<button data-contentid="Others" class="tablinks"><?php esc_html_e( 'Others', 'rtcl-coupon' ); ?></button>
	</div>

	<div id="General" class="tabcontent panel" style="display: block;">
		<div id="general_coupon_data" class="panel rtcl_options_panel" >			
			
			<p class=" form-field discount_type_field">
				<label for="discount_type"> <?php esc_html_e( 'Discount type', 'rtcl-coupon' ); ?> </label>
				<select id="discount_type" name="rtcl_discount_type" class="select short">
					<?php $discount_type = ! empty( $coupon_meta['discount_type'] ) ? $coupon_meta['discount_type'] : ''; ?>
					<option value="fixed_discount" <?php echo 'fixed_discount' === $discount_type ? 'selected' : ''; ?>><?php esc_html_e( 'Fixed discount', 'rtcl-coupon' ); ?></option>
					<option value="percent_discount" <?php echo 'percent_discount' === $discount_type ? 'selected' : ''; ?> ><?php esc_html_e( 'Percentage discount', 'rtcl-coupon' ); ?></option>
				</select>
			</p>
			<p class="form-field coupon_amount_field ">
				<?php $coupon_amount = ! empty( $coupon_meta['discount_amount'] ) ? absint( $coupon_meta['discount_amount'] ) : 0; ?>
				<label for="coupon_amount">
					<?php esc_html_e( 'Coupon amount', 'rtcl-coupon' ); ?>
				</label>
				<input type="number" class="short" name="rtcl_coupon_amount" id="coupon_amount" value="<?php echo esc_html( $coupon_amount ); ?>" placeholder="0" step="1" min="1" autocomplete="off"> 
				<span class="rtcl-help-tip" data-tip="<?php esc_html_e( 'Value of the coupon.', 'rtcl-coupon' ); ?>" > </span>
			</p>
			<p class="form-field expiry_date_field ">
				<label for="expiry_date"> 
					<?php esc_html_e( 'Coupon expiry date', 'rtcl-coupon' ); ?>
				</label>
				<?php
					$expiry = ! empty( $coupon_meta['expire_date'] ) ? $coupon_meta['expire_date'] : '';
					$expiry = Fns::timestamp_to_date( $expiry );
				?>
				<input type="text" class="date-picker short" name="rtcl_coupon_expiry" id="expiry_date" value="<?php echo esc_attr( $expiry ); ?>" placeholder="YYYY-MM-DD" autocomplete="off" /> 
				<span class="rtcl-help-tip" data-tip="<?php esc_html_e( 'The coupon will expire at 00:00:00 of this date.', 'rtcl-coupon' ); ?>" ></span>
			</p>
			<p class="form-field">
				<label><?php esc_html_e( 'Pricing Type', 'rtcl-coupon' ); ?></label>
				<select class="short pricing-type" name="rtcl_pricing_type" coupon-id='<?php echo absint( get_the_ID() ); ?>' coupon-meta='<?php echo json_encode( $coupon_meta ); ?>'>
					<option value=""> <?php esc_html_e( '--Select--', 'rtcl-coupon' ); ?> </option>
					<?php
					$pricing_type = ! empty( $coupon_meta['pricing_type'] ) ? $coupon_meta['pricing_type'] : '';
					$types        = Options::get_pricing_types();
					if ( $types ) {
						foreach ( $types as $key => $value ) {
							?>
						<option <?php echo $pricing_type === $key ? 'selected' : ''; ?> value="<?php echo esc_attr( $key ); ?>" ><?php echo esc_html( $value ); ?> </option>
							<?php
						}
					}
					?>
				</select>	
				<span class="rtcl-help-tip" data-tip="<?php esc_html_e( 'The coupon is only for selected Pricing Types.', 'rtcl-coupon' ); ?>" ></span>			
			</p>	
			<div id="depend_on_type">
				<?php
				if ( $pricing_type ) {
					add_filter(
						'rtcl_coupon_get_all_pricing_query_args',
						function( $args ) use ( $pricing_type ) {
							$args['meta_query'] = [
								[
									[
										'key'   => 'pricing_type',
										'value' => $pricing_type,
									],
								],
							];
							if ( 'regular' === $pricing_type ) {
								$args['meta_query'] = [
									[
										[
											'key'   => 'pricing_type',
											'value' => $pricing_type,
										],
										[
											'key'     => 'pricing_type',
											'compare' => 'NOT EXISTS',
										],
										'relation' => 'OR',
									],
								];
							}
							return $args;
						}
					);
				}
				?>
				<?php Fns::pricing_type_dependent_field( $coupon_meta ); ?>
			</div>		
		</div>
	</div>

	<div id="Others" class="tabcontent" style="display: none;">
		<div class="options_group">
			<p class="form-field usage_limit_field ">
				<?php
				$usage_limit = ! empty( $coupon_meta['usage_limit'] ) ? absint( $coupon_meta['usage_limit'] ) : '';
				?>
				<label for="usage_limit"><?php esc_html_e( 'Usage limit', 'rtcl-coupon' ); ?></label>
				<input type="number" class="short" name="rtcl_usage_limit" id="usage_limit" value="<?php echo esc_html( $usage_limit ); ?>" placeholder="Unlimited usage" step="1" min="1"> 
				<span class="rtcl-help-tip" data-tip="<?php esc_html_e( 'How many times this coupon can be used before it is void.', 'rtcl-coupon' ); ?>"></span>
			</p>
			<p class="form-field usage_limit_per_user_field ">
				<?php
				$limit_per_user = ! empty( $coupon_meta['per_user_limit'] ) ? absint( $coupon_meta['per_user_limit'] ) : '';
				?>
				<label for="usage_limit_per_user"><?php esc_html_e( 'Usage limit per user', 'rtcl-coupon' ); ?></label>
				<input type="number" class="short" name="rtcl_usage_limit_per_user" id="usage_limit_per_user" value="<?php echo esc_html( $limit_per_user ); ?>" placeholder="<?php esc_html_e( 'Unlimited usage', 'rtcl-coupon' ); ?>" step="1" min="0"> 
				<span class=" rtcl-help-tip" data-tip="<?php esc_html_e( 'How many times this coupon can be used by an individual user.', 'rtcl-coupon' ); ?>" ></span>
			</p>				
		</div>
	</div>
</div>
