<?php

namespace RtclPro\Api\V1;

use Rtcl\Controllers\Hooks\Filters;
use Rtcl\Helpers\Functions;
use Rtcl\Models\VStore;
use Rtcl\Resources\Options;
use RtclPro\Controllers\ChatController;
use RtclPro\Helpers\Api;
use RtclPro\Helpers\Fns;
use RtclPro\Models\Conversation;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class V1_MyApi {
	public function register_routes() {
		register_rest_route( 'rtcl/v1', 'my', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'my_info_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ]
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_my_account_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'first_name' => [
						'description' => esc_html__( 'First name is required field.', 'classified-listing-pro' ),
						'type'        => 'string',
						'required'    => true,
					],
					'last_name'  => [
						'description' => esc_html__( 'Last name is required field.', 'classified-listing-pro' ),
						'type'        => 'string',
						'required'    => true,
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/profile-image', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'upload_my_profile_image_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'image' => [
						'type'              => 'file',
						'validate_callback' => function ( $value, $request, $param ) {
							$files = $request->get_file_params();
							if ( empty( $files['image'] ) ) {
								return new WP_Error( 'rest_invalid_param', esc_html__( 'parameter image file field is required.', 'classified-listing-pro' ), [ 'status' => 400 ] );
							}

							return true;
						},
						'description'       => 'Image file is required field.',
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/listings', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_my_listings' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'search'   => [
						'description'       => esc_html__( 'Limit results to those matching a string.' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'per_page' => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 20,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'     => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					],
					'order_by' => [
						'description' => esc_html__( 'Order by.', 'classified-listing-pro' ),
						'type'        => 'string'
					],
				]
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_my_listing_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing id is required', 'classified-listing-pro' ),
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/listing/renew', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'renew_listing' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'listing_id' => [
					'required'    => true,
					'type'        => 'integer',
					'description' => esc_html__( 'Listing id is required (listing_id)', 'classified-listing-pro' ),
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/favourites', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_my_favourite_listings' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'per_page' => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 20,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'     => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					]
				]
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'toggle_my_favourite_listing' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing is required', 'classified-listing-pro' ),
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/mark-as-sold', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'toggle_mark_as_sold' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'listing_id' => [
					'required'    => true,
					'type'        => 'integer',
					'description' => esc_html__( 'Listing id required', 'classified-listing-pro' ),
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/chat', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_my_chat_list' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'per_page' => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 20,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'     => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/chat/check', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'check_has_conversation' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing is required', 'classified-listing-pro' ),
					],
					'per_page'   => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 50,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'       => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/chat/conversation', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_conversation_messages' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'con_id'   => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Conversation is required', 'classified-listing-pro' ),
					],
					'limit'    => [
						'required'    => false,
						'type'        => 'integer',
						'description' => esc_html__( 'Message limit', 'classified-listing-pro' ),
					],
					'per_page' => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 50,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'     => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					]
				]
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'start_new_conversation' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing is required', 'classified-listing-pro' ),
					],
					'text'       => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'Message text is required', 'classified-listing-pro' ),
					],
					'files'      => [
						'required'    => false,
						'type'        => 'array',
						'description' => esc_html__( 'Image or files', 'classified-listing-pro' ),
					],
					'temp_id'    => [
						'type'        => 'number',
						'description' => esc_html__( 'Message temp id', 'classified-listing-pro' ),
					]
				]
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_chat_conversation' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'con_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Conversation is required', 'classified-listing-pro' ),
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/chat/message', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'send_message' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'con_id'     => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Conversation ID is required', 'classified-listing-pro' ),
					],
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing ID is required', 'classified-listing-pro' ),
					],
					'text'       => [
						'required'    => true,
						'type'        => 'string',
						'description' => esc_html__( 'Message text is required', 'classified-listing-pro' ),
					],
					'temp_id'    => [
						'type'        => 'number',
						'description' => esc_html__( 'Message temp id', 'classified-listing-pro' ),
					],
					'files'      => [
						'required'    => false,
						'type'        => 'array',
						'description' => esc_html__( 'Image or files', 'classified-listing-pro' ),
					]
				]
			],
			[
				'methods'             => 'PUT',
				'callback'            => [ $this, 'set_message_read' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'con_id'     => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Conversation ID is required', 'classified-listing-pro' ),
					],
					'message_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Message ID is required', 'classified-listing-pro' ),
					]
				]
			]
		] );

		register_rest_route( 'rtcl/v1', 'my/manage/listings', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_my_manage_listings_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'per_page' => [
						'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 20,
						'minimum'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
					],
					'page'     => [
						'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => 'rest_validate_request_arg',
						'minimum'           => 1,
					]
				]
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_my_manage_listing_callback' ],
				'permission_callback' => [ Api::class, 'permission_check' ],
				'args'                => [
					'listing_id' => [
						'required'    => true,
						'type'        => 'integer',
						'description' => esc_html__( 'Listing id is required', 'classified-listing-pro' ),
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/manage/listings/change-status', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_my_manage_listings_change_status_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'id'     => [
					'required'    => true,
					'type'        => 'integer',
					'description' => esc_html__( 'Listing id is required', 'classified-listing-pro' ),
				],
				'status' => [
					'required'    => true,
					'type'        => 'string',
					'description' => esc_html__( 'Listing status is required. (publish, pending, rtcl-reviewed, rtcl-expired, draft)', 'classified-listing-pro' ),
					'enum'        => [
						'publish',
						'pending',
						'rtcl-reviewed',
						'rtcl-expired',
						'draft',
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/manage/orders', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_my_manage_orders_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'search'   => [
					'description'       => esc_html__( 'Limit results to those matching a string.' ),
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => 'rest_validate_request_arg',
				],
				'per_page' => [
					'description'       => esc_html__( 'Maximum number of items to be returned in result set.', 'classified-listing-pro' ),
					'type'              => 'integer',
					'default'           => 20,
					'minimum'           => 1,
					'maximum'           => 100,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
				],
				'page'     => [
					'description'       => esc_html__( 'Current page of the collection.', 'classified-listing-pro' ),
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'rest_validate_request_arg',
					'minimum'           => 1,
				],
				'order_by' => [
					'description' => esc_html__( 'Order by', 'classified-listing-pro' ),
					'type'        => 'string'
				],
				'order'    => [
					'description' => esc_html__( 'Order', 'classified-listing-pro' ),
					'type'        => 'string'
				],
				'status'   => [
					'required'    => false,
					'type'        => 'string',
					'description' => esc_html__( 'Order status is required. (rtcl-pending, rtcl-processing, rtcl-on-hold, rtcl-completed, rtcl-cancelled, rtcl-refunded, rtcl-failed, rtcl-created)', 'classified-listing-pro' ),
					'enum'        => [
						'rtcl-pending',
						'rtcl-processing',
						'rtcl-on-hold',
						'rtcl-completed',
						'rtcl-cancelled',
						'rtcl-refunded',
						'rtcl-failed',
						'rtcl-created'
					]
				]
			]
		] );
		register_rest_route( 'rtcl/v1', 'my/manage/orders/change-status', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'get_my_manage_orders_change_status_callback' ],
			'permission_callback' => [ Api::class, 'permission_check' ],
			'args'                => [
				'id'     => [
					'required'    => true,
					'type'        => 'integer',
					'description' => esc_html__( 'Order id is required', 'classified-listing-pro' ),
				],
				'status' => [
					'required'    => true,
					'type'        => 'string',
					'description' => esc_html__( 'Order status is required. (publish, pending, rtcl-reviewed, rtcl-expired, draft)', 'classified-listing-pro' ),
					'enum'        => [
						'rtcl-pending',
						'rtcl-processing',
						'rtcl-on-hold',
						'rtcl-completed',
						'rtcl-cancelled',
						'rtcl-refunded',
						'rtcl-failed',
						'rtcl-created'
					]
				]
			]
		] );
	}


	public function check_has_conversation( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$visitor_id = get_current_user_id();
		$tempId = !empty( $request->get_param( 'temp_id' ) ) && is_numeric( $request->get_param( 'temp_id' ) ) ? absint( $request->get_param( 'temp_id' ) ) : '';
		if ( !$visitor_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}

		$listing_id = $request->get_param( 'listing_id' );
		if ( !( $listing = rtcl()->factory->get_listing( $listing_id ) ) || !$listing->exists() || $visitor_id === $listing->get_author_id() ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not permitted to access this conversation.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}
		$per_page = $request->get_param( 'per_page' );
		$page = $request->get_param( 'page' );
		$author_id = $listing->get_author_id();
		$response = false;
		$conversation = Fns::getConversationByVisitorIdAuthorIdListingId( $visitor_id, $author_id, $listing_id );
		if ( $conversation) {
			$response = $conversation->getData( );
			$messageData = $conversation->messages( [ 'per_page' => $per_page, 'page' => $page ] );
			$response->messages = $messageData['data'];
			$response->pagination = $messageData['pagination'];
		}

		return rest_ensure_response( $response );
	}

	public function start_new_conversation( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$visitor_id = get_current_user_id();
		$tempId = !empty( $request->get_param( 'temp_id' ) ) && is_numeric( $request->get_param( 'temp_id' ) ) ? absint( $request->get_param( 'temp_id' ) ) : '';
		if ( !$visitor_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}

		$listing_id = $request->get_param( 'listing_id' );
		$text = $request->get_param( 'text' );

		if ( !$listing_id || !( $listing = rtcl()->factory->get_listing( $listing_id ) ) || !$listing->exists() || $visitor_id === $listing->get_author_id() ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "No listing found to start chat.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}

		if ( $visitor_id === $listing->get_author_id() ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "As you are an author of this listing, so you not permitted to create chat.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}

		$author_id = $listing->get_author_id();
		$conversation = Fns::getConversationByVisitorIdAuthorIdListingId( $visitor_id, $author_id, $listing_id );
		if ( $conversation ) {
			$response = $conversation->getData();
		} else {
			$files = $request->get_file_params();
			$rawFiles = !empty( $files['files'] ) && is_array( $files['files'] ) ? $files['files'] : [];
			$messageData = [ 'text' => $text, 'files' => $rawFiles ];
			$msgResponse = ChatController::initiate_new_conversation_write_message( [
				'listing_id'   => $listing->get_id(),
				'sender_id'    => $visitor_id,
				'recipient_id' => $author_id
			], $messageData );
			if ( is_wp_error( $msgResponse ) ) {
				$msgResponse->add_data( [ 'status' => 400, 'temp_id' => $tempId, ] );

				return $msgResponse;
			}
			$msgResponse->temp_id = $tempId;
			$conversation = new Conversation( $msgResponse->con_id );
			$response = $conversation->getData();
			$response->newMessage = $msgResponse;
		}

		return rest_ensure_response( $response );
	}

	public function delete_chat_conversation( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			];
			wp_send_json( $response, 403 );
		}

		$con_id = $request->get_param( 'con_id' );
		if ( !ChatController::_is_valid_conversation( $con_id ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not permitted to access this conversation."
			];
			wp_send_json( $response, 403 );
		}

		return rest_ensure_response( ChatController::_delete_conversation( $con_id ) );
	}

	public function send_message( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		$tempId = !empty( $request->get_param( 'temp_id' ) ) && is_numeric( $request->get_param( 'temp_id' ) ) ? absint( $request->get_param( 'temp_id' ) ) : '';
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [
				'status'  => 400,
				'temp_id' => $tempId
			] );
		}


		$text = $request->get_param( 'text' );
		$listing_id = $request->get_param( 'listing_id' );
		if ( !$listing_id || !( $listing = rtcl()->factory->get_listing( $listing_id ) ) || !$listing->exists() ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "No listing found.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}
		$con_id = $request->get_param( 'con_id' );
		$conversation = new Conversation( $con_id );

		if ( !$conversation->exist() ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "Conversation not found.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}
		if ( $conversation->listing_id !== $listing->get_id() || ( $conversation->recipient_id !== $user_id && $conversation->sender_id !== $user_id ) ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not permitted to access this conversation.", "classified-listing-pro" ), [
				'status'  => 403,
				'temp_id' => $tempId
			] );
		}
		$files = $request->get_file_params();
		$rawFiles = !empty( $files['files'] ) && is_array( $files['files'] ) ? $files['files'] : [];
		$response = $conversation->sent_message( [ 'text' => $text, 'files' => $rawFiles ] );

		if ( is_wp_error( $response ) ) {
			$response->add_data( [ 'status' => 503, 'temp_id' => $tempId ] );

			return $response;
		}

		$response->temp_id = $tempId;

		return rest_ensure_response( $response );
	}

	public function set_message_read( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}

		$con_id = $request->get_param( 'con_id' );
		$message_id = $request->get_param( 'message_id' );
		if ( !ChatController::_is_valid_conversation( $con_id ) ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not permitted to access this conversation.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}

		return rest_ensure_response( ChatController::_set_message_read( $con_id, $message_id ) );
	}


	public function get_conversation_messages( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}
		$per_page = $request->get_param( 'per_page' );
		$page = $request->get_param( 'page' );
		$con_id = $request->get_param( 'con_id' );
		if ( !ChatController::_is_valid_conversation( $con_id ) ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not permitted to access this conversation.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}
		$conversation = new Conversation( $con_id );
		$response = $conversation->getData();
		$messageData = $conversation->messages( [ 'per_page' => $per_page, 'page' => $page ] );
		$response->messages = $messageData['data'];
		$response->pagination = $messageData['pagination'];

		Fns::update_chat_conversation_status( $con_id, $user_id );

		return rest_ensure_response( $response );
	}

	public function get_my_chat_list( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}
		$per_page = $request->get_param( 'per_page' );
		$page = $request->get_param( 'page' );

		return rest_ensure_response( ChatController::_fetch_conversations( $user_id, [
			'per_page' => $per_page,
			'page'     => $page
		] ) );
	}

	public function delete_my_listing_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}
		if ( !$request->get_param( 'listing_id' ) || ( !$listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) || $listing->get_post_type() !== rtcl()->post_type ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'Listing not found.'
			];
			wp_send_json( $response, 400 );
		}

		if ( $user_id !== $listing->get_author_id() ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not permitted to delete."
			];
			wp_send_json( $response, 403 );
		}
		$children = get_children( apply_filters( 'rtcl_before_delete_listing_attachment_query_args', [
			'post_parent'    => $listing->get_id(),
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'inherit',
		], $listing->get_id() ) );
		if ( !empty( $children ) ) {
			foreach ( $children as $child ) {
				wp_delete_attachment( $child->ID, true );
			}
		}

		do_action( 'rtcl_before_delete_listing', $listing->get_id() );
		$result = Functions::delete_post( $listing->get_id() );

		return rest_ensure_response( $result ? $listing->get_id() : false );
	}

	public function toggle_my_favourite_listing( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}

		if ( !$request->get_param( 'listing_id' ) || ( !$listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'Listing not found.'
			];
			wp_send_json( $response, 400 );
		}

		$favourites = get_user_meta( $user_id, 'rtcl_favourites', true );
		$favourites = !empty( $favourites ) && is_array( $favourites ) ? $favourites : [];

		if ( in_array( $listing->get_id(), $favourites ) ) {
			if ( ( $key = array_search( $listing->get_id(), $favourites ) ) !== false ) {
				unset( $favourites[$key] );
			}
		} else {
			$favourites[] = $listing->get_id();
		}

		$favourites = array_filter( $favourites );
		$favourites = array_values( $favourites );
		update_user_meta( $user_id, 'rtcl_favourites', $favourites );

		return rest_ensure_response( $favourites );
	}

	public function toggle_mark_as_sold( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}

		if ( !$request->get_param( 'listing_id' ) || ( !$listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => 'Listing not found.'
			];
			wp_send_json( $response, 400 );
		}
		$data = [
			'listing_id' => $listing->get_id()
		];
		if ( absint( get_post_meta( $listing->get_id(), '_rtcl_mark_as_sold', true ) ) ) {
			delete_post_meta( $listing->get_id(), '_rtcl_mark_as_sold' );
			$data['action'] = 'unsold';
		} else {
			update_post_meta( $listing->get_id(), '_rtcl_mark_as_sold', 1 );
			$data['action'] = 'sold';
		}

		return rest_ensure_response( $data );
	}

	public function get_my_listings( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}

		$per_page = (int)$request->get_param( "per_page" );
		$page = (int)$request->get_param( "page" );
		$search = $request->get_param( "search" );
		$order_by = $request->get_param( "order_by" );
		$args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'any',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'author'         => $user_id,
			'fields'         => 'ids',
			'query_type'     => 'my'
		];
		if ( $search ) {
			$args['s'] = $search;
		}

		$response = Api::get_query_listing_data( apply_filters( 'rtcl_rest_response_my_listings_args', $args ) );

		return rest_ensure_response( $response );
	}

	public function renew_listing( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( "rtcl_rest_authentication_error", __( 'You are not logged in.', 'classified-listing-pro' ), [ 'status' => 404 ] );
		}
		$listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) );
		if ( !$listing ) {
			return new WP_Error( "rtcl_rest_authentication_error", __( 'Listing not found.', 'classified-listing-pro' ), [ 'status' => 404 ] );
		}
		if ( !apply_filters( 'rtcl_enable_renew_button', Functions::is_enable_renew(), $listing ) ) {
			return new WP_Error( "rtcl_rest_authentication_error", __( "Unauthorized access.", 'classified-listing-pro' ), [ 'status' => 403 ] );
		}
		if ( $listing->get_owner_id() !== get_current_user_id() ) {
			return new WP_Error( "rtcl_rest_authentication_error", __( "You are not authorized to renew this listing.", 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( "rtcl-expired" !== $listing->get_status() ) {
			return new WP_Error( "rtcl_rest_authentication_error", __( "Listing is not expired.", 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		$wp_error = new WP_Error();
		$vStore = new VStore();

		do_action( 'rtcl_before_renew_listing', $listing, $vStore, $wp_error, $_REQUEST );

		if ( $wp_error->has_errors() ) {
			return new WP_Error( "rtcl_rest_renew_error", $wp_error->get_error_message(), $wp_error->get_error_data() );
		}

		$post_arg = [
			'ID'          => $listing->get_id(),
			'post_status' => 'publish'
		];
		$updatedListingId = wp_update_post( $post_arg );
		if ( is_wp_error( $updatedListingId ) ) {
			return $updatedListingId;
		}
		Functions::add_default_expiry_date( $listing->get_id() );
		$wp_error = new WP_Error();
		do_action( 'rtcl_after_renew_listing', $listing, $vStore, $wp_error, $_REQUEST );

		if ( $wp_error->has_errors() ) {
			$post_arg = [
				'ID'          => $listing->get_id(),
				'post_status' => 'rtcl-expired'
			];
			wp_update_post( $post_arg );

			return new WP_Error( "rtcl_rest_renew_error", $wp_error->get_error_message(), $wp_error->get_error_data() );
		}

		if ( get_post_meta( $listing->get_id(), 'never_expires', true ) ) {
			$expire_at = esc_html__( 'Never Expires', 'classified-listing' );
		} else if ( $expiry_date = get_post_meta( $listing->get_id(), 'expiry_date', true ) ) {
			$expire_at = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
				strtotime( $expiry_date ) );
		} else {
			$expire_at = 'N/A';
		}

		$data = [
			'expire_at' => $expire_at,
			'status'    => 'publish',
		];

		return rest_ensure_response( $data );
	}

	public function get_my_favourite_listings( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}
		$per_page = (int)$request->get_param( "per_page" );
		$page = (int)$request->get_param( "page" );
		$favourite_posts = get_user_meta( $user_id, 'rtcl_favourites', true );
		$favourite_post_ids = !empty( $favourite_posts ) && is_array( $favourite_posts ) ? $favourite_posts : [ 0 ];
		$args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'fields'         => 'ids',
			'post__in'       => $favourite_post_ids
		];
		$response = Api::get_query_listing_data( apply_filters( 'rtcl_rest_response_my_favourite_listings_args', $args ) );

		return rest_ensure_response( $response );
	}

	public function update_my_account_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', esc_html__( "You are not logged in.", "classified-listing-pro" ), [ 'status' => 403 ] );
		}
		// Validate password
		$password = '';
		if ( $request->get_param( 'change_password' ) === true ) {
			$password = sanitize_text_field( $request->get_param( 'pass1' ) );
			$error = null;
			if ( empty( $password ) ) {
				// Password is empty
				$error = esc_html__( 'The password field is empty.', 'classified-listing-pro' );
			}
			if ( $password !== $request->get_param( 'pass2' ) ) {
				// Passwords don't match
				$error = esc_html__( "The two passwords you entered don't match.", 'classified-listing-pro' );
			}
			if ( $error ) {
				$response = [
					'status'        => "error",
					'error'         => 'DADREQUEST',
					'code'          => '40!',
					'error_message' => $error
				];
				wp_send_json( $response, 403 );
			}
		}
		$first_name = sanitize_text_field( $request->get_param( 'first_name' ) );
		$last_name = sanitize_text_field( $request->get_param( 'last_name' ) );

		$user_data = [
			'ID'         => $user_id,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'nickname'   => $first_name
		];

		if ( !empty( $password ) ) {
			$user_data['user_pass'] = $password;
		}
		$user_id = wp_update_user( $user_data );
		$user_meta = [
			'_rtcl_phone'           => !empty( $request->get_param( 'phone' ) ) ? esc_attr( $request->get_param( 'phone' ) ) : null,
			'_rtcl_whatsapp_number' => !empty( $request->get_param( 'whatsapp_number' ) ) ? esc_attr( $request->get_param( 'whatsapp_number' ) ) : null,
			'_rtcl_website'         => !empty( $request->get_param( 'website' ) ) ? esc_url_raw( $request->get_param( 'website' ) ) : null,
			'_rtcl_zipcode'         => !empty( $request->get_param( 'zipcode' ) ) ? esc_attr( $request->get_param( 'zipcode' ) ) : null,
			'_rtcl_address'         => !empty( $request->get_param( 'address' ) ) ? esc_textarea( $request->get_param( 'address' ) ) : null,
		];
		if ( $request->has_param( 'latitude' ) ) {
			$user_meta['_rtcl_latitude'] = !empty( $request->get_param( 'latitude' ) ) ? esc_attr( $request->get_param( 'latitude' ) ) : null;
		}
		if ( $request->has_param( 'longitude' ) ) {
			$user_meta['_rtcl_longitude'] = !empty( $request->get_param( 'longitude' ) ) ? esc_attr( $request->get_param( 'longitude' ) ) : null;
		}
		if ( $request->has_param( 'locations' ) ) {
			$locations = $request->get_param( 'locations' );
			$user_meta['_rtcl_location'] = !empty( $locations ) && is_array( $locations ) ? array_filter( array_map( 'absint', $locations ) ) : [];
		}
		foreach ( $user_meta as $metaKey => $metaValue ) {
			update_user_meta( $user_id, $metaKey, $metaValue );
		}

		$user = get_user_by( "ID", $user_id );
		do_action( 'rtcl_rest_api_update_my_account_success', $user, $request );
		$user_data = Api::get_user_data( $user );

		return rest_ensure_response( $user_data );
	}

	public function my_info_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => esc_html__( "You are not logged in.", 'classified-listing-pro' )
			];
			wp_send_json( $response, 403 );
		}
		$user = get_user_by( 'ID', $user_id );
		$my_data = Api::get_user_data( $user );

		return rest_ensure_response( $my_data );
	}

	public function upload_my_profile_image_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			$response = [
				'status'        => "error",
				'error'         => 'FORBIDDEN',
				'code'          => '403',
				'error_message' => "You are not logged in."
			];
			wp_send_json( $response, 403 );
		}

		$files = $request->get_file_params();
		if ( empty( $files['image'] ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => "Image file field is required."
			];
			wp_send_json( $response, 400 );
		}
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		Filters::beforeUpload();
		$status = wp_handle_upload( $files['image'], [ 'test_form' => false ] );
		Filters::afterUpload();
		if ( $status && isset( $status['error'] ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => $status['error']
			];
			wp_send_json( $response, 400 );
		}
		$filename = $status['file'];
		// Check the type of tile. We'll use this as the 'post_mime_type'.
		$fileType = wp_check_filetype( basename( $filename ) );

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = [
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $fileType['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		];

		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $filename );
		if ( is_wp_error( $attach_id ) ) {
			$response = [
				'status'        => "error",
				'error'         => 'BADREQUEST',
				'code'          => '400',
				'error_message' => $attach_id->get_error_message()
			];
			wp_send_json( $response, 400 );
		}

		if ( $existing_pp = absint( get_user_meta( $user_id, '_rtcl_pp_id', true ) ) ) {
			wp_delete_attachment( $existing_pp, true );
		}
		update_user_meta( $user_id, '_rtcl_pp_id', $attach_id );
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
		$src = wp_get_attachment_image_src( $attach_id, [ 80, 80 ] );
		$data = [
			'thumbnail_id' => $attach_id,
			'src'          => $src[0],
			'user_id'      => $user_id
		];
		do_action( 'rtcl_user_pp_updated', $data, $user_id, $attach_id, $request->get_file_params() );

		return rest_ensure_response( $data );
	}


	public function get_my_manage_listings_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not logged in.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !current_user_can( 'administrator' ) && !current_user_can( 'rtcl_manager' ) ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not authorized to access.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		$per_page = (int)$request->get_param( "per_page" );
		$page = (int)$request->get_param( "page" );
		$locations = $request->get_param( "locations" );
		$categories = $request->get_param( "categories" );
		$search = $request->get_param( "search" );
		$listing_type = $request->get_param( "listing_type" );
		$order_by = $request->get_param( "order_by" );
		$post_status = $request->get_param( "status" );
		$post_status = in_array( $post_status, array_keys( Options::get_status_list() ) ) ? $post_status : 'any';

		$args = [
			'post_type'      => rtcl()->post_type,
			'post_status'    => $post_status,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'fields'         => 'ids',
		];
		if ( $search ) {
			$args['s'] = $search;
		}
		$ordering = Api::get_ordering_args( $order_by );
		$args['orderby'] = $ordering['orderby'];
		$args['order'] = $ordering['order'];
		if ( isset( $ordering['meta_key'] ) ) {
			$args['meta_key'] = $ordering['meta_key'];
		}
		$tax_queries = [];
		$meta_queries = [];
		if ( !empty( $categories ) ) {
			$tax_queries[] = [
				'taxonomy'         => rtcl()->category,
				'field'            => 'term_id',
				'terms'            => $categories,
				'include_children' => isset( $general_settings['include_results_from'] ) && in_array( 'child_categories',
						$general_settings['include_results_from'] ),
			];

		}
		if ( !empty( $locations ) && "local" === Functions::location_type() ) {
			$tax_queries[] = [
				'taxonomy'         => rtcl()->location,
				'field'            => 'term_id',
				'terms'            => $locations,
				'include_children' => isset( $general_settings['include_results_from'] ) && in_array( 'child_locations',
						$general_settings['include_results_from'] ),
			];
		}
		$count_tax_queries = count( $tax_queries );
		if ( $count_tax_queries ) {
			$args['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( [ 'relation' => 'AND' ], $tax_queries ) : $tax_queries;
		}


		// Promotions filter
		if ( !empty( $promotion_in ) && is_array( $promotion_in ) ) {
			$promotions = array_keys( Options::get_listing_promotions() );
			foreach ( $promotion_in as $promotion ) {
				if ( is_string( $promotion ) && in_array( $promotion, $promotions ) ) {
					$meta_queries[] = [
						'key'     => $promotion,
						'compare' => '=',
						'value'   => 1
					];
				}
			}
		}

		if ( !empty( $promotion_not_in ) && is_array( $promotion_not_in ) ) {
			$promotions = array_keys( Options::get_listing_promotions() );
			foreach ( $promotion_not_in as $promotion ) {
				if ( is_string( $promotion ) && in_array( $promotion, $promotions ) ) {
					$meta_queries[] = [
						'relation' => 'OR',
						[
							'key'     => $promotion,
							'compare' => '!=',
							'value'   => 1
						],
						[
							'key'     => $promotion,
							'compare' => 'NOT EXISTS',
						]
					];
				}
			}
		}

		// Listing type filter
		if ( $listing_type && in_array( $listing_type, array_keys( Functions::get_listing_types() ) ) && !Functions::is_ad_type_disabled() ) {
			$meta_queries[] = [
				'key'     => 'ad_type',
				'value'   => $listing_type,
				'compare' => '='
			];
		}

		$count_meta_queries = count( $meta_queries );
		if ( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( [ 'relation' => 'AND' ], $meta_queries ) : $meta_queries;
		}

		$response = Api::get_query_listing_data( apply_filters( 'rtcl_rest_response_my_manage_listings_args', $args ) );

		return rest_ensure_response( $response );
	}

	public function delete_my_manage_listing_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not logged in.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !current_user_can( 'administrator' ) && !current_user_can( 'rtcl_manager' ) ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not permitted to delete.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !$request->get_param( 'listing_id' ) || ( !$listing = rtcl()->factory->get_listing( $request->get_param( 'listing_id' ) ) ) || $listing->get_post_type() !== rtcl()->post_type ) {
			return new WP_Error( 'BADREQUEST', __( 'Listing not found.', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}

		$children = get_children( apply_filters( 'rtcl_before_delete_listing_attachment_query_args', [
			'post_parent'    => $listing->get_id(),
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'inherit',
		], $listing->get_id() ) );
		if ( !empty( $children ) ) {
			foreach ( $children as $child ) {
				wp_delete_attachment( $child->ID, true );
			}
		}

		do_action( 'rtcl_before_delete_listing', $listing->get_id() );
		$result = Functions::delete_post( $listing->get_id() );

		return rest_ensure_response( $result ? $listing->get_id() : false );
	}

	public function get_my_manage_listings_change_status_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not logged in.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !current_user_can( 'administrator' ) && !current_user_can( 'rtcl_manager' ) ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not permitted to update listing status.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		$status = $request->get_param( 'status' );
		if ( !in_array( $status, array_keys( Options::get_status_list() ) ) ) {
			return new WP_Error( 'BADREQUEST', __( 'Invalid listing status.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !$request->get_param( 'id' ) || ( !$listing = rtcl()->factory->get_listing( $request->get_param( 'id' ) ) ) ) {
			return new WP_Error( 'BADREQUEST', __( 'Listing not found.', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}

		if ( $status === $listing->get_status() ) {
			return new WP_Error( 'BADREQUEST', __( 'Status no change', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}


		$post = [ 'ID' => $listing->get_id(), 'post_status' => $status ];
		$updated = wp_update_post( $post );
		if ( !$updated || is_wp_error( $updated ) ) {
			return new WP_Error( 'BADREQUEST', $updated ? $updated->get_error_message() : __( 'Error while updating status', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}
		$listing = rtcl()->factory->get_listing( $listing->get_id() );

		return rest_ensure_response( Api::get_single_listing_data( $listing ) );
	}

	public function get_my_manage_orders_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not logged in.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !current_user_can( 'administrator' ) && !current_user_can( 'rtcl_manager' ) ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not authorized to access.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		$per_page = (int)$request->get_param( "per_page" );
		$page = (int)$request->get_param( "page" );
		$search = sanitize_text_field( $request->get_param( "search" ) );
		$order = sanitize_text_field( $request->get_param( "order" ) );
		$order_by = sanitize_text_field( $request->get_param( "order_by" ) );
		$post_status = sanitize_text_field( $request->get_param( "status" ) );
		$post_status = in_array( $post_status, array_keys( Options::get_payment_status_list() ) ) ? $post_status : 'any';

		$args = [
			'post_type'      => rtcl()->post_type_payment,
			'post_status'    => $post_status,
			'order'          => $order,
			'order_by'       => $order_by,
			'posts_per_page' => $per_page,
			'fields'         => 'ids',
			'paged'          => $page ?: 1,
		];

		if ( $search ) {
			$args['s'] = $search;
		}

		$response = Api::get_query_order_data( apply_filters( 'rtcl_rest_api_payments_args', $args ) );

		return rest_ensure_response( $response );
	}

	public function get_my_manage_orders_change_status_callback( WP_REST_Request $request ) {
		Api::is_valid_auth_request();
		$user_id = get_current_user_id();
		if ( !$user_id ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not logged in.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !current_user_can( 'administrator' ) && !current_user_can( 'rtcl_manager' ) ) {
			return new WP_Error( 'FORBIDDEN', __( 'You are not permitted to update order status.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		$status = $request->get_param( 'status' );
		if ( !in_array( $status, array_keys( Options::get_payment_status_list() ) ) ) {
			return new WP_Error( 'BADREQUEST', __( 'Invalid order status.', 'classified-listing-pro' ), [ 'status' => 403 ] );
		}

		if ( !$request->get_param( 'id' ) || ( !$order = rtcl()->factory->get_order( $request->get_param( 'id' ) ) ) ) {
			return new WP_Error( 'BADREQUEST', __( 'Order not found.', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}

		if ( $status === $order->get_status() ) {
			return new WP_Error( 'BADREQUEST', __( 'Status no change', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}

		if ( !$order->update_status( $status ) ) {
			return new WP_Error( 'BADREQUEST', __( 'Error while updating status', 'classified-listing-pro' ), [ 'status' => 400 ] );
		}
		$order = rtcl()->factory->get_order( $order->get_id() );

		return rest_ensure_response( Api::get_single_order_data( $order ) );
	}
}
