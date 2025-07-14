<?php

namespace RtclMarketplace\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

class PayoutRequestEmail extends RtclEmail {

	protected $amount;
	protected $seller_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->db            = true;
		$this->id            = 'payout_request';
		$this->template_html = 'emails/payout-request-email-to-admin';

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] Payout request from {seller_name}', 'rtcl-marketplace' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return esc_html__( 'Payout request from: {seller_name}', 'rtcl-marketplace' );
	}

	/**
	 * @param $payout_id
	 * @param $data
	 *
	 * @return string|void
	 * @throws \Exception
	 */
	public function trigger( $payout_id, $data = [] ) {
		$this->setup_locale();

		$this->amount = $data['amount'];

		$seller_id         = $data['seller_id'];
		$this->seller_name = get_userdata( $seller_id )->display_name;

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
				'email'       => $this,
				'amount'      => $this->amount,
				'seller_name' => $this->seller_name,
			),
			'',
			rtcl_marketplace()->get_plugin_template_path()
		);
	}
}