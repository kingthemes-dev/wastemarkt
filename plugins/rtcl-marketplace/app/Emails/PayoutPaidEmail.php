<?php

namespace RtclMarketplace\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;
use RtclMarketplace\Helpers\Functions as MarketplaceFunctions;

class PayoutPaidEmail extends RtclEmail {

	protected $user;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->db            = true;
		$this->id            = 'payout_paid';
		$this->template_html = 'emails/payout-paid-email-to-seller';

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] Payment sent', 'rtcl-marketplace' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return esc_html__( 'Your payment has been cleared', 'rtcl-marketplace' );
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

		$payout_data = MarketplaceFunctions::get_payout_by_id( $payout_id );

		$this->object = $payout_data;

		$vendor_id    = $payout_data['seller_id'] ?? 0;
		$this->user   = get_userdata( $vendor_id );
		$vendor_email = $this->user->user_email;

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
				'payout' => $this->object,
				'user'   => $this->user,
				'email'  => $this,
			),
			'',
			rtcl_marketplace()->get_plugin_template_path()
		);
	}
}