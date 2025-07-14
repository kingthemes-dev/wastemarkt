<?php

namespace RtclClaimListing\Api\V1;

use Cassandra\Date;
use RtclClaimListing\Helpers\Functions;
use WP_REST_Request;
use WP_REST_Server;

class ClaimListingsApi {

	public function register_routes() {
		register_rest_route( 'rtcl-claim/v1', 'listings', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_claim_listings' ],
				'permission_callback' => [ $this, 'permission_check' ],
				'args'                => [
					'per_page' => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'rtcl-claim-listing' ),
						'type'              => 'integer',
						'default'           => 10,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'     => [
						'description'       => esc_html__( 'Current page of the collection.', 'rtcl-claim-listing' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					],
					'title'    => [
						'description'       => esc_html__( 'Limit results to those matching a string.' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'status'   => [
						'description'       => esc_html__( 'Order by.', 'rtcl-claim-listing' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					]
				]
			]
		] );
		register_rest_route( 'rtcl-claim/v1', '/listings/(?P<claim_listing_id>[\d]+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_single_claim_listing_callback' ],
				'permission_callback' => [ $this, 'permission_check' ],
				'args'                => [
					'claim_listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Claim Listing id is required', 'rtcl-claim-listing' ),
					]
				]
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_claim_listing_callback' ],
				'permission_callback' => [ $this, 'permission_check' ],
				'args'                => [
					'claim_listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Claim Listing id is required', 'rtcl-claim-listing' ),
					],
					'email'            => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'Email is required.', 'rtcl-claim-listing' ),
					],
					'phone'            => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'Phone is required.', 'rtcl-claim-listing' ),
					],
					'message'          => [
						'type' => 'string',
					],
					'status'           => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'Status is required.', 'rtcl-claim-listing' ),
					]
				]
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_claim_listing_callback' ],
				'permission_callback' => [ $this, 'permission_check' ],
				'args'                => [
					'claim_listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Claim Listing id is required', 'rtcl-claim-listing' ),
					]
				]
			],

		] );
	}

	public function get_claim_listings( WP_REST_Request $request ) {
		global $wpdb;

		$claims_table = $wpdb->prefix . "rtcl_claims";

		$page     = (int) $request->get_param( 'page' );
		$per_page = (int) $request->get_param( 'per_page' );
		$offset   = ( $page - 1 ) * $per_page;

		$claim_status    = $request->get_param( 'status' );
		$title_in_search = $request->get_param( 'title' );

		$query = "SELECT * FROM {$claims_table}";

		if ( ! empty( $claim_status ) ) {
			$query .= " " . "WHERE status = '$claim_status'";
		}

		if ( ! empty( $title_in_search ) ) {
			if ( empty( $claim_status ) ) {
				$query .= " " . "WHERE title LIKE '%{$title_in_search}%'";
			} else {
				$query .= " " . "AND title LIKE '%{$title_in_search}%'";
			}
		}

		$count_row = Functions::count_rows( $query );

		$query .= " " . "ORDER BY created_at DESC";
		$query .= " " . "LIMIT {$offset},{$per_page}";

		$results = $wpdb->get_results( $query );

		$response['max_posts'] = $count_row;

		if ( ! empty( $results ) ) {
			foreach ( $results as $claim ) {
				$attachment_url = $claimer_name = $claimer_email = '';
				if ( $claim->user_id ) {
					$claimer = get_userdata( $claim->user_id );
					if ( $claimer ) {
						$claimer_name  = $claimer->data->display_name;
						$claimer_email = $claimer->data->user_email;
					}

				}
				$owner_name = '';
				if ( $claim->prev_owner_id ) {
					$owner = get_userdata( $claim->prev_owner_id );
					if ( $owner ) {
						$owner_name = $owner->data->display_name;
					}
				}

				if ( ! empty( $claim->info ) ) {
					$info = maybe_unserialize( $claim->info );
					if ( isset( $info['attachment_id'] ) ) {
						$attachment_url = wp_get_attachment_url( $info['attachment_id'] );
					}
				}

				$response['posts'][] = array(
					'id'            => $claim->id,
					'claim_title'   => $claim->title,
					'listing_id'    => $claim->listing_id,
					'listing_title' => rtcl()->factory->get_listing( $claim->listing_id )->get_the_title(),
					'user_id'       => $claim->user_id,
					'claimed_by'    => $claimer_name,
					'user_email'    => $claimer_email,
					'owner'         => $owner_name,
					'attachment'    => $attachment_url,
					'date'          => \date('F d, Y \a\t g:i a', strtotime($claim->created_at)),
					'status'        => $claim->status
				);
			}
		}

		return rest_ensure_response( $response );

	}

	public function get_single_claim_listing_callback( WP_REST_Request $request ) {

		if ( ! $request->get_param( 'claim_listing_id' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Claim Listing id not found.', "rtcl-claim-listing" )
			];
			wp_send_json( $response, 400 );
		}

		global $wpdb;

		$claim_listing_id = $request->get_param( 'claim_listing_id' );

		$claims_table = $wpdb->prefix . "rtcl_claims";

		$claim = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$claims_table} WHERE id=%d", [ $claim_listing_id ] ) );

		$response = array();

		if ( ! empty( $claim ) ) {
			$response = array(
				'id'            => $claim->id,
				'listing_id'    => $claim->listing_id,
				'listing_title' => rtcl()->factory->get_listing( $claim->listing_id )->get_the_title(),
				'date'          => $claim->created_at,
				'status'        => $claim->status
			);
			if ( ! empty( $claim->info ) ) {
				$info                   = maybe_unserialize( $claim->info );
				$response['claim_info'] = $info;
			}
			if ( $claim->user_id ) {
				$owner = get_userdata( $claim->user_id );
				if ( $owner ) {
					$response['claimer'] = array(
						'id'      => $owner->data->ID,
						'name'    => $owner->data->display_name,
						'email'   => $owner->data->user_email,
						'profile' => get_edit_user_link( $claim->user_id )
					);
				}
			}
			if ( $claim->prev_owner_id ) {
				$owner = get_userdata( $claim->prev_owner_id );
				if ( $owner ) {
					$response['owner'] = array(
						'id'      => $owner->data->ID,
						'name'    => $owner->data->display_name,
						'email'   => $owner->data->user_email,
						'profile' => get_edit_user_link( $claim->prev_owner_id )
					);
				}
			}
		}

		return rest_ensure_response( $response );
	}

	public function delete_claim_listing_callback( WP_REST_Request $request ) {

		global $wpdb;

		if ( ! $request->get_param( 'claim_listing_id' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Claim Listing id not found.', "rtcl-claim-listing" )
			];
			wp_send_json( $response, 400 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not permitted to delete."
			];
			wp_send_json( $response, 403 );
		}

		$success          = false;
		$message          = esc_html__( 'Something wrong!', 'rtcl-claim-listing' );
		$claim_listing_id = $request->get_param( 'claim_listing_id' );
		$claims_table     = $wpdb->prefix . "rtcl_claims";

		$deleted = $wpdb->delete( $claims_table, [ 'id' => $claim_listing_id ] );

		if ( $deleted ) {
			$success = true;
			$message = esc_html__( 'Deleted claim successfully', 'rtcl-claim-listing' );
		}

		$response = [
			'success' => $success,
			'msg'     => $message
		];

		return rest_ensure_response( $response );
	}

	public function update_claim_listing_callback( WP_REST_Request $request ) {

		global $wpdb;

		if ( ! $request->get_param( 'claim_listing_id' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Claim Listing id not found.', "rtcl-claim-listing" )
			];
			wp_send_json( $response, 400 );
		}

		if ( ! $request->get_param( 'status' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Claim Listing status not found.', "rtcl-claim-listing" )
			];
			wp_send_json( $response, 400 );
		}

		if ( ! $request->get_param( 'phone' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Phone not found.', "rtcl-claim-listing" )
			];
			wp_send_json( $response, 400 );
		}

		if ( ! $request->get_param( 'email' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => esc_html__( 'Email not found.', "rtcl-claim-listing" )
			];
			wp_send_json( $response, 400 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not permitted to update."
			];
			wp_send_json( $response, 403 );
		}

		$success = false;
		$message = esc_html__( 'Something wrong!', 'rtcl-claim-listing' );

		$claim_id     = absint( $request->get_param( 'claim_listing_id' ) );
		$claim_status = sanitize_text_field( $request->get_param( 'status' ) );
		$phone        = sanitize_text_field( $request->get_param( 'phone' ) );
		$email        = sanitize_email( $request->get_param( 'email' ) );

		$claims_table = $wpdb->prefix . "rtcl_claims";

		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$claims_table} WHERE id=%d", array( $claim_id ) ) );

		if ( ! empty( $result ) ) {
			$info = maybe_unserialize( $result->info );

			if ( empty( $info ) ) {
				$info = array();
			}

			$info['email'] = $email;
			$info['phone'] = $phone;

			if ( $request->get_param( 'message' ) ) {
				$info['message'] = sanitize_textarea_field( $request->get_param( 'message' ) );
			}

			$where = [
				'id' => $claim_id,
			];

			$data = [
				'info'       => maybe_serialize( $info ),
				'updated_at' => current_time( 'mysql' ),
				'status'     => $claim_status,
			];

			$row = $wpdb->update( $claims_table, $data, $where );

			if ( $row ) {
				$success = true;
				$message = esc_html__( 'Updated claim successfully', 'rtcl-claim-listing' );
			}

			if ( $success && 'approved' === $claim_status ) {
				$message = Functions::transfer_listing_ownership( $result->listing_id, $result->user_id );
			}

			if ( $success && 'cancelled' === $claim_status ) {
				rtcl()->mailer()->emails['Claim_Rejected_Email']->trigger( $result->listing_id, [ 'claimer_id' => $result->user_id ] );
			}

		}

		$response = [
			'success' => $success,
			'msg'     => $message
		];

		return rest_ensure_response( $response );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool
	 */
	public function permission_check( WP_REST_Request $request ) {
		return true;

		return current_user_can( 'manage_options' );
	}
}