<?php

namespace RtclVerification\Services;

use RtclVerification\Helpers\Functions;

abstract class SmsGateway {


	/**
	 *
	 *
	 * @var string
	 */
	protected $to;


	/**
	 *
	 *
	 * @var string
	 */
	protected $id;

	/**
	 *
	 *
	 * @var integer
	 */
	protected $otp_code;

	/**
	 *
	 *
	 * @var array
	 */
	protected $data;

	/**
	 *
	 *
	 * @var array
	 */
	protected $settings;


	public function __construct( $to, $data = [], $otp_code = null ) {
		$this->to       = $to;
		$this->otp_code = $otp_code ?: Functions::generate_otp();
		$this->data     = $data;

		/*$this->getSettings();*/
	}

	abstract protected function send_otp();

	/*protected function getSettings(){
		$this->settings = get_option('rtcl_v_'.$this->id);
	}*/

}