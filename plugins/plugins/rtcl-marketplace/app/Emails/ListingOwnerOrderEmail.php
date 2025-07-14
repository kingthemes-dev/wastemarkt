<?php

namespace RtclMarketplace\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

class ListingOwnerOrderEmail extends RtclEmail {

	protected $items;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->db            = true;
		$this->id            = 'listing_order_created';
		$this->template_html = 'emails/listing-order-email-to-owner';

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] New customer order ({order_number}) - {order_date}', 'rtcl-marketplace' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return esc_html__( 'New Customer Order: #{order_number}', 'rtcl-marketplace' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param $listing_id
	 * @param $listing
	 *
	 * @throws \Exception
	 */
	public function trigger( $order, $data = [] ) {
		$this->setup_locale();

		$this->object = $order;
		$this->items  = $data['vendor_items'];

		$vendor_id    = $data['vendor_id'];
		$vendor_email = get_userdata( $vendor_id )->user_email;

		$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
		$this->placeholders['{order_number}'] = $this->object->get_order_number();

		$this->set_recipient( $vendor_email );

		if ( $this->get_recipient() ) {
			$this->send();
		}

		$this->restore_locale();
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return Functions::get_template_html(
			$this->template_html,
			array(
				'order'      => $this->object,
				'email'      => $this,
				'items'      => $this->items,
				'show_image' => false,
				'image_size' => array( 32, 32 ),
			),
			'',
			rtcl_marketplace()->get_plugin_template_path()
		);
	}
}