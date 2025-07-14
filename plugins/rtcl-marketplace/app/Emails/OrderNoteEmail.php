<?php

namespace RtclMarketplace\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

class OrderNoteEmail extends RtclEmail {

	protected $order;
	protected $seller_name;
	protected $customer_note;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->db            = true;
		$this->id            = 'vendor_note_email';
		$this->template_html = 'emails/vendor-note-email-to-admin';

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] A note has been added to order', 'rtcl-marketplace' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return esc_html__( 'New note from: {seller_name}', 'rtcl-marketplace' );
	}

	/**
	 * @param $payout_id
	 * @param $data
	 *
	 * @return string|void
	 * @throws \Exception
	 */
	public function trigger( $order_id, $data = [] ) {
		$this->setup_locale();

		$this->order = $data['order'];

		$seller_id           = $data['seller_id'];
		$this->seller_name   = get_userdata( $seller_id )->display_name;
		$this->customer_note = $data['customer_note'];

		$this->placeholders['{seller_name}'] = $this->seller_name;

		$this->set_recipient( Functions::get_admin_email_id_s() );

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
				'email'         => $this,
				'order'         => $this->order,
				'customer_note' => $this->customer_note,
				'seller_name'   => $this->seller_name,
			),
			'',
			rtcl_marketplace()->get_plugin_template_path()
		);
	}
}