<?php
/**
 * Listing Price unite field
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.0
 *
 * @var array $price_unit_list
 * @var array $price_unit
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (empty($price_units)) {
    return;
}
$labelClass = is_admin() ? 'col-sm-2' : 'col-sm-3';
$inputClass = is_admin() ? 'col-sm-10' : 'col-sm-9';
?>
<div class="row" id="rtcl-price-unit-wrap">
    <div class="<?php echo esc_attr( $labelClass ); ?> col-12">
        <label class="control-label"><?php esc_html_e( 'Price Unit', 'classilist' ); ?></label>
    </div>
    <div class="<?php echo esc_attr( $inputClass ); ?> col-12">
        <div class="form-group">
            <select class="form-control rtcl-select2" id="rtcl-price-unit" name="_rtcl_price_unit">
                <option value=""><?php esc_html_e("No unit", "classilist"); ?></option>
                <?php
                foreach ($price_unit_list as $unit_key => $unit) {
                    if (in_array($unit_key, $price_units)) {
                        echo sprintf('<option value="%s"%s>%s (%s)</label>',
                            esc_attr($unit_key),
                            $price_unit == $unit_key ? " selected" : null,
                            esc_html($unit['title']),
                            esc_html($unit['short'])
                        );
                    }
                }
                ?>
            </select>
        </div>
    </div>
</div>