<?php
/**
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       3.1.16
 *
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use RtclMarketplace\Helpers\Functions as MarketplaceFunction;

$user_id        = get_current_user_id();
$method_url     = rtrim( Link::get_account_endpoint_url( 'marketplace-payout' ), '/' ) . '/method/';
$balance        = MarketplaceFunction::get_available_balance();
$minimum_payout = MarketplaceFunction::get_minimum_payout();

$withdraw_history = MarketplaceFunction::get_withdraw_history( get_current_user_id() );
?>

<div class="rtcl-payout-history-wrap">
    <div class="rtcl-payout-history-wrap-inner">
        <div class="rtcl-payout-info">
            <div>
                <strong><?php _e( 'Minimum Payout:' ); ?></strong>
                <span><?php echo wc_price( $minimum_payout ); ?></span>
				<?php
				$payout_method = MarketplaceFunction::get_current_selected_payout_method();
				if ( $payout_method ) {
					?>
                    <span> | </span>
                    <strong><?php _e( 'Selected Payout Method:', 'rtcl-marketplace' ); ?></strong>
                    <span><?php echo isset( $payout_method['method'] ) ? esc_html( MarketplaceFunction::get_payout_option_text( $payout_method['method'] ) )
							: ''; ?></span>
				<?php } ?>
            </div>
            <div>
				<?php
				$payout_method_btn_txt = empty( $payout_method ) ? __( 'Set payout method', 'rtcl-marketplace' )
					: __( 'Update payout method', 'rtcl-marketplace' );
				?>
                <a href="<?php echo esc_url( $method_url ); ?>" class="btn btn-primary"><?php echo esc_html( $payout_method_btn_txt ); ?></a>
            </div>
        </div>
        <div class="rtcl-marketplace-balance-wrapper">
            <div class="rtcl-marketplace-balance-single">
                <span class="payout-title"><?php _e( 'Available Balance', 'rtcl-marketplace' ); ?></span>
                <span class="payout-info"><?php echo wc_price( $balance ); ?></span>
				<?php if ( $balance && $balance >= $minimum_payout ): ?>
                    <a href="#" id="rtcl-payout-send-request" data-id="<?php echo get_current_user_id(); ?>" class=""><?php _e( 'Send a withdraw request',
							'rtcl-marketplace' ); ?></a>
				<?php endif; ?>
            </div>
            <div class="rtcl-marketplace-balance-single">
				<?php $earning = MarketplaceFunction::get_user_total_earning(); ?>
                <span class="payout-title"><?php _e( 'Total Earning', 'rtcl-marketplace' ); ?></span>
                <span class="payout-info"><?php echo wc_price( $earning ); ?></span>
            </div>
            <div class="rtcl-marketplace-balance-single">
				<?php $withdraw = MarketplaceFunction::get_user_total_withdraw(); ?>
                <span class="payout-title"><?php _e( 'Total Withdraw', 'rtcl-marketplace' ); ?></span>
                <span class="payout-info"><?php echo wc_price( $withdraw ); ?></span>
            </div>
        </div>
        <div class="rtcl-table-scroll-x rtcl-table-responsive-list rtcl-payout-history">
            <h3><?php esc_html_e( 'Withdraw History', 'rtcl-marketplace' ); ?></h3>
            <table class="rtcl-table-striped-border">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'Date', 'rtcl-marketplace' ); ?></th>
                    <th><?php esc_html_e( 'Amount', 'rtcl-marketplace' ); ?></th>
                    <th><?php esc_html_e( 'Payout Method', 'rtcl-marketplace' ); ?></th>
                    <th><?php esc_html_e( 'Paid Date', 'rtcl-marketplace' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'rtcl-marketplace' ); ?></th>
                </tr>
                </thead>

                <!-- the loop -->
				<?php foreach ( $withdraw_history as $withdraw ) { ?>
                    <tr>
                        <td data-heading="<?php esc_attr_e( 'Date:', 'rtcl-marketplace' ); ?>">
							<?php echo Functions::datetime( 'rtcl', $withdraw['date'] ); ?>
                        </td>
                        <td data-heading="<?php esc_attr_e( 'Amount:', 'rtcl-marketplace' ); ?>">
							<?php echo wc_price( $withdraw['amount'] ); ?>
                        </td>
                        <td data-heading="<?php esc_attr_e( 'Payout Method:', 'rtcl-marketplace' ); ?>">
							<?php echo esc_html( MarketplaceFunction::get_payout_option_text( $withdraw['method'] ) ); ?>
                        </td>
                        <td data-heading="<?php esc_attr_e( 'Paid Date:', 'rtcl-marketplace' ); ?>">
							<?php echo $withdraw['paid_date'] ? Functions::datetime( 'rtcl', $withdraw['paid_date'] ) : ''; ?>
                        </td>
                        <td data-heading="<?php esc_attr_e( 'Status:', 'rtcl-marketplace' ); ?>">
							<?php echo esc_html( MarketplaceFunction::get_payout_status_text( $withdraw['status'] ) ); ?>
                        </td>
                    </tr>
				<?php } ?>
            </table>
        </div>
    </div>
</div>