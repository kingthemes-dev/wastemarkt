<?php

namespace RtclMarketplace\Models;

use Rtcl\Models\Listing;

class ListingProduct extends Listing {

	/**
	 * @param $listing
	 *
	 * @throws \Exception
	 */
	function __construct( $listing = 0 ) {
		parent::__construct( $listing );
	}

	/**
	 * @return mixed|null
	 */
	public function manage_stock() {
		$manage_stock = get_post_meta( $this->id, '_manage_stock', true );

		return apply_filters( 'rtcl_marketplace_manage_listing_stock', $manage_stock === 'yes' );
	}

	/**
	 * @return mixed|null
	 */
	public function get_stock_quantity() {
		$quantity = get_post_meta( $this->id, '_stock', true );

		return apply_filters( 'rtcl_marketplace_listing_stock_quantity', absint( $quantity ) );
	}

	/**
	 * @param $item_quantity
	 *
	 * @return void
	 */
	public function reduce_stock( $item_quantity = 0 ) {
		$stock_quantity = $this->get_stock_quantity();
		$new_stock      = $stock_quantity >= $item_quantity ? $stock_quantity - $item_quantity : 0;
		update_post_meta( $this->id, '_stock', $new_stock );
	}

	/**
	 * @param $item_quantity
	 *
	 * @return void
	 */
	public function add_stock( $item_quantity = 0 ) {
		$stock_quantity = $this->get_stock_quantity();
		update_post_meta( $this->id, '_stock', $stock_quantity + $item_quantity );
	}
}