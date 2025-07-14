<?php

namespace RtclMarketplace\Admin;

use Rtcl\Helpers\Functions;
use RtclMarketplace\Helpers\Functions as MarketplaceFunctions;
use RtclMarketplace\Models\CommissionTable;
use RtclMarketplace\Models\PayoutTable;

class AdminHooks {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_filter( 'rtcl_register_settings_tabs', [ __CLASS__, 'add_marketplace_tab_item_at_settings_tabs_list' ] );
		add_filter( 'rtcl_settings_option_fields', [ __CLASS__, 'add_marketplace_tab_options' ], 10, 2 );
		add_action( 'rtcl_listing_details_meta_box', [ __CLASS__, 'marketplace_meta_boxes' ] );
		add_action( 'rtcl_listing_update_metas_at_admin', [ __CLASS__, 'save_marketplace_data' ], 10, 2 );
		add_filter( 'rtcl_licenses', [ __CLASS__, 'license' ], 20 );
		add_filter( 'admin_menu', [ __CLASS__, 'add_marketplace_menu' ], 60 );
	}

	public static function add_marketplace_menu() {

		if ( ! MarketplaceFunctions::is_enable_payout() ) {
			return;
		}

		// Add commission menu
		add_submenu_page(
			'rtcl-admin',
			__( 'Commission', 'rtcl-marketplace' ),
			__( 'Commission', 'rtcl-marketplace' ),
			'manage_rtcl_options',
			'rtcl-marketplace-commission',
			[ __CLASS__, 'display_commission' ]
		);

		// Add payout menu
		add_submenu_page(
			'rtcl-admin',
			__( 'Payouts', 'rtcl-marketplace' ),
			__( 'Payouts', 'rtcl-marketplace' ),
			'manage_rtcl_options',
			'rtcl-marketplace-payouts',
			[ __CLASS__, 'display_payouts' ]
		);
	}

	public static function display_commission() {
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		$table = new CommissionTable();
		?>
        <div class="wrap">
            <h3><?php esc_html_e( 'All Commission', 'rtcl-marketplace' ); ?></h3>
            <form method="post">
				<?php
				$table->prepare_items();
				$table->search_box( 'Search', 'search_id' );
				$table->display();
				?>
            </form>
        </div>
		<?php
	}

	public static function display_payouts() {
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		$table = new PayoutTable();
		?>
        <div class="wrap">
			<?php if ( isset( $_GET['payout_id'] ) ):
				$payment_id = absint( $_GET['payout_id'] );
				$payout_data = MarketplaceFunctions::get_payout_by_id( $payment_id );
				$seller_id = $payout_data['seller_id'] ?? 0;
				$seller = get_user_by( 'id', $seller_id );
				?>
                <h3><?php esc_html_e( 'Payout request from', 'rtcl-marketplace' ); ?>
                    <a href="<?php echo get_edit_profile_url( $seller->ID ); ?>"><?php echo esc_html( $seller->display_name ); ?></a>
                </h3>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="rtcl-marketplace-payout-status-wrap">
                                <div class="payout-status">
                                    <label for="payout-status-dropdown"><?php _e( 'Status', 'rtcl-marketplace' ); ?></label>
									<?php $statuses = MarketplaceFunctions::get_payout_status(); ?>
                                    <select id="payout-status-dropdown" name="payout-status-dropdown">
										<?php foreach ( $statuses as $key => $status ) { ?>
                                            <option <?php selected( $key, $payout_data['status'], true ); ?>
                                                    value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $status ); ?></option>
										<?php } ?>
                                    </select>
                                </div>
                                <div class="rtcl-marketplace-payout-status-btn">
                                    <button class="button button-primary" data-id="<?php echo esc_attr( $payment_id ); ?>">
										<?php _e( 'Update Status', 'rtcl-marketplace' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="postbox-container-2" class="postbox-container">
                            <div class="rtcl-marketplace-single-payout">
                                <div class="payout-date">
                                    <span><?php echo Functions::datetime( 'rtcl', $payout_data['date'] ); ?></span>
                                </div>
                                <div class="payout-amount">
                                    <span class="label"><?php _e( 'Amount: ', 'rtcl-marketplace' ); ?></span>
                                    <span><?php echo wc_price( $payout_data['amount'] ); ?></span>
                                </div>
                                <div class="payout-method">
                                    <span class="label"><?php _e( 'Method: ', 'rtcl-marketplace' ); ?></span>
                                    <span><?php echo MarketplaceFunctions::get_payout_option_text( $payout_data['method'] ); ?></span>
                                </div>
                                <div class="payout-method">
                                    <span class="label"><?php _e( 'Information: ', 'rtcl-marketplace' ); ?></span>
                                    <span><?php echo esc_html( $payout_data['details'] ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			<?php else: ?>
                <h3><?php esc_html_e( 'All Payout Request', 'rtcl-marketplace' ); ?></h3>
                <form method="post">
					<?php
					$table->prepare_items();
					$table->search_box( 'Search', 'search_id' );
					$table->display();
					?>
                </form>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
	 * Add marketplace metabox
	 *
	 * @return void
	 */
	public static function marketplace_meta_boxes() {
		add_meta_box(
			'rtcl_marketplace',
			__( 'Marketplace', 'rtcl-marketplace' ),
			[ self::class, 'marketplace_fields' ],
			rtcl()->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Add marketplace fields.
	 *
	 * @param \WP_Post $post
	 *
	 * @return void
	 */
	public static function marketplace_fields( \WP_Post $post ) {

		if ( ! MarketplaceFunctions::is_enable_marketplace()
		     && ( ! MarketplaceFunctions::is_enable_stock_management()
		          || MarketplaceFunctions::is_enable_download_product() )
		) {
			return;
		}

		$post_id = $post->ID;

		$data = [
			'post_id'         => $post_id,
			'manage_stock'    => get_post_meta( $post_id, '_manage_stock', true ),
			'stock'           => get_post_meta( $post_id, '_stock', true ),
			'allow_format'    => MarketplaceFunctions::get_allow_file_format(),
			'stock_enable'    => MarketplaceFunctions::is_enable_stock_management(),
			'download_enable' => MarketplaceFunctions::is_enable_download_product(),
		];
		Functions::get_template( 'listing-form/marketplace', $data, '', rtcl_marketplace()->get_plugin_template_path() );
	}

	/**
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function save_marketplace_data( $post_id, $post ) {

		// The nonce security has checked by the main hook.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['_rtcl_manage_stock'] ) ) {
			update_post_meta( $post_id, '_manage_stock', 'yes' );
			if ( isset( $_POST['_rtcl_stock'] ) ) {
				update_post_meta( $post_id, '_stock', absint( $_POST['_rtcl_stock'] ) );
			}
		} else {
			delete_post_meta( $post_id, '_manage_stock' );
			delete_post_meta( $post_id, '_stock' );
		}

		update_post_meta( $post_id, '_rtcl_enable_download',
			sanitize_text_field( wp_unslash( $_POST['_rtcl_enable_download'] ) ) ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated

		$download_files = [];

		if ( ! empty( $_POST['_rtcl_file_urls'] ) ) {

			$titles
				= $_POST['_rtcl_file_names']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$urls
				= $_POST['_rtcl_file_urls']; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

			foreach ( $urls as $index => $url ) {
				if ( empty( $url ) ) {
					continue;
				}
				$download_files[] = [
					'title' => sanitize_text_field( $titles[ $index ] ?? '' ),
					'url'   => sanitize_url( $url ),
				];
			}
		}

		update_post_meta( $post_id, 'rtcl_download_files', $download_files );

		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * add membership tab item
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public static function add_marketplace_tab_item_at_settings_tabs_list( $tabs ) {
		$tabs['marketplace'] = esc_html__( 'Marketplace', 'rtcl-marketplace' );

		return $tabs;
	}

	/**
	 * Add marketplace tab options
	 *
	 * @param $fields
	 * @param $active_tab
	 *
	 * @return mixed|null
	 */
	public static function add_marketplace_tab_options( $fields, $active_tab ) {
		if ( 'marketplace' == $active_tab ) {
			$fields = [
				'marketplace_enable'               => [
					'title'       => esc_html__( 'Marketplace', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Enable', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
					'description' => esc_html__( 'Enable marketplace option.', 'rtcl-marketplace' ),
				],
				'marketplace_categories'           => [
					'title'       => esc_html__( 'Select Categories', 'rtcl-marketplace' ),
					'options'     => MarketplaceFunctions::get_first_level_category_array(),
					'type'        => 'multiselect',
					'description' => esc_html__( 'Enable marketplace only for specific categories.',
						'rtcl-marketplace' ),
					'class'       => 'rtcl-select2',
					'blank_text'  => esc_html__( "Select categories", 'rtcl-marketplace' ),
					'css'         => 'min-width:500px;',
					'dependency'  => [
						'rules' => [
							'#rtcl_marketplace_settings-marketplace_enable' => [
								'type'  => 'equal',
								'value' => 'yes',
							],
						],
					],
				],
				'listing_archive_section'          => [
					'title'       => esc_html__( 'Listing Archive options', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
				],
				'enable_in_listings_page'          => [
					'title'       => esc_html__( 'Add to cart button', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Enable', 'rtcl-marketplace' ),
					'description' => esc_html__( 'Enable add to cart button in listing archive page.', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
				],
				'enable_quantity_in_listings_page' => [
					'title'       => esc_html__( 'Quantity field', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Enable', 'rtcl-marketplace' ),
					'description' => esc_html__( 'Enable quantity field in listing archive page.', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
					'dependency'  => [
						'rules' => [
							'#rtcl_marketplace_settings-enable_in_listings_page' => [
								'type'  => 'equal',
								'value' => 'yes',
							],
						],
					],
				],
				'buy_button_text'                  => [
					'title'      => esc_html__( 'Add to cart button text', 'rtcl-marketplace' ),
					'type'       => 'text',
					'default'    => esc_html__( 'Buy Now', 'rtcl-marketplace' ),
					'dependency' => [
						'rules' => [
							'#rtcl_marketplace_settings-enable_in_listings_page' => [
								'type'  => 'equal',
								'value' => 'yes',
							],
						],
					],
				],
				'listing_single_section'           => [
					'title'       => esc_html__( 'Listing details options', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
				],
				'disable_in_listing_page'          => [
					'title'       => esc_html__( 'Add to cart button', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Disable', 'rtcl-marketplace' ),
					'description' => esc_html__( 'Disable add to cart button in details page.', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
				],
				'disable_quantity_in_listing_page' => [
					'title'       => esc_html__( 'Quantity field', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Disable', 'rtcl-marketplace' ),
					'description' => esc_html__( 'Disable quantity field in details page.', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
				],
				'details_buy_button_text'          => [
					'title'      => esc_html__( 'Add to cart button text', 'rtcl-marketplace' ),
					'type'       => 'text',
					'default'    => esc_html__( 'Buy Now', 'rtcl-marketplace' ),
					'dependency' => [
						'rules' => [
							'#rtcl_marketplace_settings-disable_in_listing_page' => [
								'type'  => 'notequal',
								'value' => 'yes',
							],
						],
					],
				],
				'commission_section'               => [
					'title'       => esc_html__( 'Commission & Payout', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
				],
				'enable_payout_commission'         => [
					'title' => esc_html__( 'Enable payout & commission', 'rtcl-marketplace' ),
					'label' => esc_html__( 'Enable', 'rtcl-marketplace' ),
					'type'  => 'checkbox',
				],
				'commission_rate'                  => [
					'title'       => esc_html__( 'Commission Rate', 'rtcl-marketplace' ),
					'type'        => 'number',
					'description' => esc_html__( 'Add commission rate in %', 'rtcl-marketplace' ),
				],
				'minimum_payout_amount'            => [
					'title'       => esc_html__( 'Minimum payout amount', 'rtcl-marketplace' ),
					'type'        => 'number',
					'description' => esc_html__( 'User able to send payout request if earn the amount minimum', 'rtcl-marketplace' ),
				],
				'payout_method'                    => array(
					'title'   => esc_html__( 'Allow payout method for seller amount withdraw', 'rtcl-marketplace' ),
					'type'    => 'multi_checkbox',
					'default' => array( 'paypal' ),
					'options' => MarketplaceFunctions::get_payout_options(),
				),
				'payout_method_dbt_section'        => [
					'title'       => esc_html__( 'Direct Bank Transfer', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
					'dependency'  => [
						'rules' => [
							'#rtcl_marketplace_settings-payout_method-direct_bank_transfer' => [
								'type'  => 'equal',
								'value' => 'direct_bank_transfer',
							],
						],
					],
				],
				'direct_bank_transfer_title'       => [
					'title'   => esc_html__( 'Title', 'rtcl-marketplace' ),
					'default' => esc_html__( 'Direct bank transfer', 'rtcl-marketplace' ),
					'type'    => 'text',
				],
				'direct_bank_transfer_desc'        => [
					'title' => esc_html__( 'Description', 'rtcl-marketplace' ),
					'type'  => 'textarea',
				],
				'payout_method_paypal_section'     => [
					'title'       => esc_html__( 'Paypal', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
					'dependency'  => [
						'rules' => [
							'#rtcl_marketplace_settings-payout_method-paypal' => [
								'type'  => 'equal',
								'value' => 'paypal',
							],
						],
					],
				],
				'paypal_title'                     => [
					'title'   => esc_html__( 'Title', 'rtcl-marketplace' ),
					'default' => esc_html__( 'Paypal', 'rtcl-marketplace' ),
					'type'    => 'text',
				],
				'paypal_desc'                      => [
					'title' => esc_html__( 'Description', 'rtcl-marketplace' ),
					'type'  => 'textarea',
				],
				'payout_method_offline_section'    => [
					'title'       => esc_html__( 'Offline', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
					'dependency'  => [
						'rules' => [
							'#rtcl_marketplace_settings-payout_method-offline' => [
								'type'  => 'equal',
								'value' => 'offline',
							],
						],
					],
				],
				'offline_title'                    => [
					'title'   => esc_html__( 'Title', 'rtcl-marketplace' ),
					'default' => esc_html__( 'Offline', 'rtcl-marketplace' ),
					'type'    => 'text',
				],
				'offline_desc'                     => [
					'title' => esc_html__( 'Description', 'rtcl-marketplace' ),
					'type'  => 'textarea',
				],
				'others_section'                   => [
					'title'       => esc_html__( 'Other options', 'rtcl-marketplace' ),
					'type'        => 'title',
					'description' => '',
				],
				'stock_enable'                     => [
					'title'       => esc_html__( 'Stock management', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Enable', 'rtcl-marketplace' ),
					'description' => esc_html__( 'Manage stock for listings.', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
				],
				'download_enable'                  => [
					'title'       => esc_html__( 'Downloadable Product', 'rtcl-marketplace' ),
					'label'       => esc_html__( 'Enable', 'rtcl-marketplace' ),
					'description' => esc_html__( 'Manage stock for listings.', 'rtcl-marketplace' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
				],
				'myaccount_mydownload_endpoint'    => [
					'title'   => esc_html__( 'My Download Endpoint', 'rtcl-marketplace' ),
					'type'    => 'text',
					'default' => 'my-download',
				],
				'myaccount_orders_endpoint'        => [
					'title'   => esc_html__( 'Orders Endpoint', 'rtcl-marketplace' ),
					'type'    => 'text',
					'default' => 'my-orders',
				],
				'myaccount_payout_endpoint'        => [
					'title'   => esc_html__( 'Payout Endpoint', 'rtcl-marketplace' ),
					'type'    => 'text',
					'default' => 'payout',
				],
				'maximum_download_size'            => [
					'title'       => esc_html__( 'Maximum Download File Size', 'rtcl-marketplace' ),
					'type'        => 'number',
					'default'     => '1024',
					'description' => esc_html__( 'Enter maximum file size by KB (1024 KB equal 1MB)', 'rtcl-marketplace' ),
				],
				'download_allow_file_format'       => array(
					'title'   => esc_html__( 'Allow File format for downloadable porduct', 'rtcl-marketplace' ),
					'type'    => 'multi_checkbox',
					'default' => array( 'application/pdf', 'image/jpeg', 'image/jpg', 'image/png' ),
					'options' => array(
						'application/pdf' => esc_html__( 'PDF (.pdf)', 'rtcl-marketplace' ),
						'text/plain'      => esc_html__( 'Text (.txt)', 'rtcl-marketplace' ),
						'image/jpeg'      => esc_html__( 'Image (.jpeg)', 'rtcl-marketplace' ),
						'image/jpg'       => esc_html__( 'Image (.jpg)', 'rtcl-marketplace' ),
						'image/png'       => esc_html__( 'Image (.png)', 'rtcl-marketplace' ),
						'video/*'         => esc_html__( 'Video (.mp4)', 'rtcl-marketplace' ),
						'audio/*'         => esc_html__( 'Audio (.mp3)', 'rtcl-marketplace' ),
					),
				),

			];

			$fields = apply_filters( 'rtcl_marketplace_settings_options', $fields );
		}

		return $fields;
	}

	/**
	 * @param $licenses
	 *
	 * @return mixed
	 */
	public static function license( $licenses ) {
		$licenses[] = [
			'plugin_file' => RTCL_MARKETPLACE_PLUGIN_FILE,
			'api_data'    => [
				'key_name'    => 'marketplace_license_key',
				'status_name' => 'marketplace_license_status',
				'action_name' => 'rtcl_manage_marketplace_licensing',
				'product_id'  => 250462,
				'version'     => RTCL_MARKETPLACE_VERSION,
			],
			'settings'    => [
				'title' => esc_html__( 'Marketplace license key', 'rtcl-marketplace' ),
			],
		];

		return $licenses;
	}
}
