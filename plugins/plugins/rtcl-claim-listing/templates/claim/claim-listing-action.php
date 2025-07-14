<?php
/**
 * Claim Listing Form
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 */

use RtclClaimListing\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<li class='list-group-item rtin-icon-common'>
	<?php if ( is_user_logged_in() ): ?>
        <a href="javascript:void(0)" data-toggle="modal" data-target="#rtcl-claim-listing-modal">
            <span class="rtcl-icon rtcl-icon-exchange"></span>
			<?php echo esc_html(Functions::get_claim_action_title()); ?>
        </a>
	<?php else: ?>
        <a href="javascript:void(0)" class="rtcl-require-login">
            <span class="rtcl-icon rtcl-icon-exchange"></span>
            <?php echo esc_html(Functions::get_claim_action_title()); ?>
        </a>
	<?php endif; ?>
</li>