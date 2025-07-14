<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       3.1.16
 *
 * @var array $options ;
 */

use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;

$user_id = get_current_user_id();
?>
<?php do_action( 'rtcl_marketplace_before_payout_method' ); ?>
<div class="rtcl-payout-history-wrap">
    <div class="rtcl-MyAccount-content-inner">
        <h3><?php _e( 'Select payout method', 'rtcl-marketplace' ); ?></h3>
		<?php do_action( 'rtcl_marketplace_payout_method', $options, $user_id ); ?>
    </div>
</div>