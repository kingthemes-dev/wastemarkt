<?php

namespace RtclMarketplace\Models;

use Rtcl\Helpers\Functions;
use RtclMarketplace\Helpers\Functions as MarketplaceFunction;

class PayoutTable extends \WP_List_Table {
	private $table_data;

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'seller':
			case 'amount':
			case 'date':
			case 'paid_date':
			case 'method':
			case 'status':
			default:
				return $item[ $column_name ];
		}
	}

	public function get_columns() {
		return [
			'cb'        => '',
			'seller'    => __( 'Seller', 'rtcl-marketplace' ),
			'amount'    => __( 'Amount', 'rtcl-marketplace' ),
			'date'      => __( 'Request Date', 'rtcl-marketplace' ),
			'paid_date' => __( 'Paid Date', 'rtcl-marketplace' ),
			'method'    => __( 'Payout Method', 'rtcl-marketplace' ),
			'status'    => __( 'Payout Status', 'rtcl-marketplace' )
		];
	}

	public function prepare_items() {

		if ( isset( $_POST['s'] ) ) {
			$this->table_data = $this->get_table_data( $_POST['s'] );
		} else {
			$this->table_data = $this->get_table_data();
		}

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$primary               = 'id';
		$this->_column_headers = [ $columns, $hidden, $sortable, $primary ];

		usort( $this->table_data, [ &$this, 'usort_reorder' ] );

		/* pagination */
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->table_data );

		$this->table_data = array_slice( $this->table_data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$this->items = $this->table_data;
	}

	private function get_table_data( $search = '' ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_marketplace_withdraw';

		$query = "SELECT * from {$table_name}";

		if ( ! empty( $search ) ) {
			$query .= " WHERE method Like '%{$search}%' OR status Like '%{$search}%'";
		}

		return $wpdb->get_results( $query, ARRAY_A );
	}

	protected function get_sortable_columns() {
		return [
			'date'      => [ 'Request Date', false ],
			'paid_date' => [ 'Paid Date', false ],
		];
	}

	public function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';

		$order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( $order === 'asc' ) ? $result : - $result;
	}

	public static function column_seller( $item ) {
		$seller_id = $item['seller_id'] ?? 0;
		$user      = get_user_by( 'id', $seller_id );

		$admin_url        = admin_url();
		$current_page_url = add_query_arg( [ 'page' => 'rtcl-marketplace-payouts', 'payout_id' => $item['id'] ], $admin_url . 'admin.php' );

		return '<a href="' . esc_url( $current_page_url ) . '">' . esc_attr( $user->display_name ) . '</a>';
	}

	public static function column_amount( $item ) {
		$amount = $item['amount'] ?? 0;

		return wc_price( $amount );
	}

	public static function column_date( $item ) {
		return Functions::datetime( 'rtcl', $item['date'] );
	}

	public static function column_paid_date( $item ) {
		return $item['paid_date'] ? Functions::datetime( 'rtcl', $item['paid_date'] ) : '';
	}

	public static function column_method( $item ) {
		return MarketplaceFunction::get_payout_option_text( $item['method'] );
	}

	public static function column_status( $item ) {
		return MarketplaceFunction::get_payout_status_text( $item['status'] );
	}

}