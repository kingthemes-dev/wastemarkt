<?php
/**
 * Listing Price unite field
 *
 * @author     RadiusTheme
 * @package    rtcl-multi-currency/templates
 * @version    1.0.0
 *
 * @var string $rtcl_price_currency
 * @var string $default_currency
 * @var array $currencies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="rtcl-pricing-item rtcl-form-group form-group">
    <label for="rtcl-price-currency">
		<?php esc_html_e( 'Currency', 'rtcl-multi-currency' ); ?>
        <span class="require-star">*</span>
    </label>
    <select required class="form-control rtcl-select2" id="rtcl-price-currency" name="rtcl_price_currency">
		<?php
		if ( ! empty( $currencies ) ) {
			foreach ( $currencies as $currencyCode => $currencyLabel ) {
				$selected = $rtcl_price_currency ? ( $rtcl_price_currency === $currencyCode ? ' selected' : '' ) : ( $default_currency === $currencyCode ? ' selected' : '' );
				?>
                <option value="<?php echo esc_attr( $currencyCode ) ?>" <?php echo $selected; ?>><?php echo esc_html( $currencyLabel ); ?></option>
				<?php
			}
		}
		?>
    </select>
</div>