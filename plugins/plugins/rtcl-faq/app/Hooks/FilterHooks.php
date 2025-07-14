<?php

namespace RtclFaq\Hooks;

use RtclFaq\Traits\SingletonTraits;

/**
 * FilterHooks Class
 */
class FilterHooks {

	use SingletonTraits;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'rtcl_moderation_settings_options', [ __CLASS__, 'rtcl_faq_sttings' ] );
	}

	/**
	 * FAQ Settings
	 *
	 * @param $options
	 *
	 * @return array
	 */
	public static function rtcl_faq_sttings( $options ) {
		$newOptions = [
			'lisgint_faq'                   => [
				'title'       => esc_html__( 'Listing FAQ', 'classified-listing' ),
				'type'        => 'title',
				'description' => '',
			],
			'listing_enable_faq'            => [
				'title'   => esc_html__( 'Enable Listing FAQ', 'classified-listing-pro' ),
				'type'    => 'checkbox',
				'default' => 'yes',
				'label'   => esc_html__( 'Enable or disable the FAQ.', 'classified-listing-pro' ),
			],
			'listing_faq_title'             => [
				'title'   => esc_html__( 'FAQ Title', 'classified-listing-pro' ),
				'type'    => 'text',
				'default' => esc_html__( 'Listing FAQ', 'classified-listing' ),
			],
			'listing_faq_position'          => [
				'title'   => esc_html__( 'FAQ Position', 'classified-listing-pro' ),
				'type'    => 'select',
				'default' => 'content',
				'options' => [
					'content' => esc_html__( 'After listing content', 'classified-listing-pro' ),
					'sidebar' => esc_html__( 'Listing Sidebar', 'classified-listing-pro' ),
				],
			],
			'listing_faq_limit'             => [
				'title'       => esc_html__( 'FAQ Limit', 'classified-listing-pro' ),
				'type'        => 'number',
				'description' => esc_html__( 'If you would like you can increase or decrease the FAQ limit. Keep empty for unlimited.', 'classified-listing-pro' ),
			],
			'listing_faq_active_first_item' => [
				'title'   => esc_html__( 'Active First Item', 'classified-listing-pro' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'label'   => esc_html__( 'Make active the first itme of the FAQ.', 'classified-listing-pro' ),
			],
			'listing_faq_close_others'      => [
				'title'   => esc_html__( 'FAQ collapse others on click', 'classified-listing-pro' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'label'   => esc_html__( 'Enable this feature if you wanna collapse others item on click.', 'classified-listing-pro' ),
			],

		];

		return array_merge( $options, $newOptions );
	}
}
