<?php

namespace RtclClaimListing\Hooks;

use Rtcl\Helpers\Functions;
use RtclClaimListing\Emails\ClaimApprovedEmailToUser;
use RtclClaimListing\Emails\ClaimRejectedEmailToUser;
use RtclClaimListing\Emails\ClaimRequestEmailToAdmin;
use RtclClaimListing\Helpers\Functions as ClaimFunctions;

class FilterHooks {

	public static function init() {
		add_filter( 'rtcl_email_services', [ __CLASS__, 'add_claim_email_services' ], 99 );
	}

	public static function add_claim_email_services( $services ) {
		$services['Claim_Request_Email']  = new ClaimRequestEmailToAdmin();
		$services['Claim_Approved_Email']  = new ClaimApprovedEmailToUser();
		$services['Claim_Rejected_Email']  = new ClaimRejectedEmailToUser();

		return $services;
	}

}