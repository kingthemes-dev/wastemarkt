<?php

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

class RtclSellerDocumentEmail extends RtclEmail {

	protected $data = [];

	function __construct() {
		$this->id            = 'seller_document_email';
		$this->template_html = 'emails/seller-document-email';

		// Call parent constructor.
		parent::__construct();
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return __( '[{site_title}] Seller Documents', 'rtcl-seller-verification' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Seller Documents', 'rtcl-seller-verification' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param          $user_id
	 * @param array $data
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function trigger( $user_id, $data = [] ) {
		$return = false;

		if ( ! $user_id ) {
			return false;
		}

		$user          = get_user_by( 'id', $user_id );
		$data['name']  = $user->display_name;
		$data['email'] = $user->user_email;

		$this->data = $data;
		$this->setup_locale();
		$this->object = $user;

		$this->set_recipient( Functions::get_admin_email_id_s() );

		if ( $this->get_recipient() ) {
			if ( ! empty( $this->data['name'] ) && ! empty( $this->data['email'] ) ) {
				$this->set_replay_to_name( $this->data['name'] );
				$this->set_replay_to_email_address( $this->data['email'] );
			}
			$return = $this->send();
		}

		$this->restore_locale();

		return $return;
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return Functions::get_template_html(
			$this->template_html, [
			'data'  => $this->data,
			'email' => $this,
		], '', rtclSellerVerification()->get_plugin_template_path()
		);
	}

}
