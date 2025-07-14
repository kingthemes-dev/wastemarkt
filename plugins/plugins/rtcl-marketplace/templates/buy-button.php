<?php
/**
 * @var string $button_text
 * @var bool   $is_enable_quantity
 * @var bool   $is_enable_buy_button
 * @var int    $listing_id
 */

if ( ! $is_enable_buy_button ) {
	return;
}

global $listing;

if ( ! $listing ) {
	$listing = rtcl()->factory->get_listing( $listing_id );
}

if ( class_exists( 'RtclPro' ) && \RtclPro\Helpers\Fns::is_mark_as_sold( $listing->get_id() ) ) {
	return;
}

$selected_cats = \RtclMarketplace\Helpers\Functions::get_marketplace_categories();
$selected_cats = is_array( $selected_cats ) ? $selected_cats : [];
$listing_cat   = $listing->get_parent_category()->term_id ?? 0;

if ( ! empty( $selected_cats ) && ! in_array( $listing_cat, $selected_cats ) ) {
	return;
}

do_action( 'rtcl_marketplace_before_buy_button' );
?>
    <div class="rtcl-add-to-cart-form-wrapper">
        <form action="<?php echo esc_url( get_the_permalink() ); ?>" method="post" class="rtcl-add-to-cart-form">
            <input name="add-to-cart" type="hidden" value="<?php echo esc_attr( $listing->get_id() ); ?>"/>
			<?php if ( $is_enable_quantity ) : ?>
                <input name="quantity" type="number" value="1"
                       min="1" <?php echo esc_attr( \RtclMarketplace\Helpers\Functions::get_max_attribute( $listing->get_id() ) ); ?>/>
			<?php endif; ?>
            <input name="submit" type="submit" class="btn btn-primary"
                   value="<?php echo esc_html( $button_text ); ?>"
				<?php
				echo \RtclMarketplace\Helpers\Functions::disable_cart_button( $listing->get_id() ) ? 'disabled'
					: '';
				?>
            />
        </form>
    </div>
<?php
do_action( 'rtcl_marketplace_after_buy_button' );