<?php

namespace RtclClaimListing\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

class ClaimRequestEmailToAdmin extends RtclEmail {

	protected $data;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->db            = true;
		$this->id            = 'claim_request';
		$this->template_html = 'emails/claim-request-email-to-admin';

		// Call parent constructor.
		parent::__construct();

	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] Claim request for "{listing_title}"', 'rtcl-claim-listing' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return esc_html__( 'An email is Received.', 'rtcl-claim-listing' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param $listing_id
	 * @param $data
	 *
	 * @throws \Exception
	 */
	public function trigger( $listing_id, $data = [] ) {
		$this->setup_locale();

		if ( ! empty( $listing_id ) ) {
			$listing = rtcl()->factory->get_listing( $listing_id );
		}

		$this->data = $data;
		$this->set_recipient( Functions::get_admin_email_id_s() );

		if ( is_a( $listing, Listing::class ) ) {
			$this->object       = $listing;
			$this->placeholders = wp_parse_args( array(
				'{listing_title}' => $listing->get_the_title()
			), $this->placeholders );
		}

		if ( $this->get_recipient() ) {
			if (!empty($this->data['name']) && !empty($this->data['email'])) {
				$this->set_replay_to_name($this->data['name']);
				$this->set_replay_to_email_address($this->data['email']);
			}
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
				'listing'  => $this->object,
				'email'    => $this,
				'data' => $this->data
			),
			'',
			rtclClaimListing()->get_plugin_template_path()
		);
	}
}