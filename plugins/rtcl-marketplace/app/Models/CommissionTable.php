<?php

namespace RtclMarketplace\Models;
class CommissionTable extends \WP_List_Table {

	private $table_data;

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
			case 'order_id':
			case 'order_total':
			case 'seller_earning':
			case 'admin_earning':
			case 'date':
			case 'order_status':
			default:
				return $item[ $column_name ];
		}
	}

	public function get_columns() {
		return [
			'cb'             => '<input type="checkbox" />',
			'order_id'       => __( '# Order', 'rtcl-marketplace' ),
			'order_total'    => __( 'Total', 'rtcl-marketplace' ),
			'seller_earning' => __( 'Payable', 'rtcl-marketplace' ),
			'admin_earning'  => __( 'My Earning', 'rtcl-marketplace' ),
			'date'           => __( 'Date', 'rtcl-marketplace' ),
			'order_status'   => __( 'Order Status', 'rtcl-marketplace' )
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
		$primary               = 'order_id';
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

		$table_name = $wpdb->prefix . 'rtcl_marketplace_orders';

		$query = "SELECT * from {$table_name}";

		if ( ! empty( $search ) ) {
			$query .= " WHERE order_id Like '%{$search}%' OR order_status Like '%{$search}%'";
		}

		return $wpdb->get_results( $query, ARRAY_A );
	}

	protected function get_sortable_columns() {
		return [
			'order_id'    => [ 'Order ID', false ],
			'order_total' => [ 'Total', false ],
		];
	}

	public function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'order_id';

		$order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( $order === 'asc' ) ? $result : - $result;
	}

	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="element[]" value="%s" />', $item['id'] );
	}

	public static function column_order_id( $item ) {
		$id    = $item['order_id'] ?? 0;
		$order = wc_get_order( $id );

		return $order ? '<a href="' . esc_url( $order->get_edit_order_url() ) . '" target="_blank">' . $order->get_order_number() . '</a>' : $item['order_id'];
	}

	public static function column_order_total( $item ) {
		$total = $item['order_total'] ?? 0;

		return wc_price( $total );
	}

	public static function column_seller_earning( $item ) {
		$seller_amount = $item['seller_earning'] ?? 0;

		return wc_price( $seller_amount );
	}

	public static function column_admin_earning( $item ) {
		$admin_amount = $item['admin_earning'] ?? 0;

		return wc_price( $admin_amount );
	}

	public static function column_date( $item ) {
		$id    = $item['order_id'] ?? 0;
		$order = wc_get_order( $id );

		return $order ? wc_format_datetime( $order->get_date_created() ) : '';
	}

	public static function column_order_status( $item ) {
		$status = $item['order_status'] ?? 'wc-on-hold';

		return wc_get_order_status_name( $status );
	}

}