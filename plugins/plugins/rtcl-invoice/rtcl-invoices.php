<?php

/*
 * Plugin Name:       Classified Listing – PDF Invoices
 * Plugin URI:        https://www.radiustheme.com/downloads/classified-listing-rtcl-invoices/
 * Description:       Download PDF invoice for Classified Listing payments
 * Version:           1.0.2
 * Requires at least: 5.5
 * Requires PHP:      7.4
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       rtcl-invoices
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || die( 'Keep Silent' );

define( 'RTCL_INVOICE_VERSION', '1.0.2' );
define( 'RTCL_INVOICE_PLUGIN_FILE', __FILE__ );

require_once 'app/RtclInvoice.php';