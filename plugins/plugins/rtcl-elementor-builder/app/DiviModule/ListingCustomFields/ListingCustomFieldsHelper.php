<?php

namespace  RtclElb\DiviModule\ListingCustomFields;


use Rtcl\Helpers\Functions;

Class ListingCustomFieldsHelper {
	public $settings;
	public function __construct($settings) {
		$this->settings = $settings;
	}
	/**
	 * Get custom field group id
	 *
	 * @param [array] $group_id Goup id list.
	 * @return array
	 */
	public function custom_field_group_ids( $group_id ) {
		$settings = $this->settings;
		if ( ! empty( $settings['custom_field_group_list'] ) && is_array( $settings['custom_field_group_list'] ) ) {
			$ids = array_filter( $settings['custom_field_group_list'] );
			if ( count( $ids ) ) {
				$group_id = $ids;
			}
		}
		return $group_id;
	}

	public static function custom_field_group_list() {
		$group_ids = Functions::get_cfg_ids();

		$list = [
			'0' => esc_html__( 'All Group', 'rtcl-elementor-builder' ),
		];
		foreach ( $group_ids as $id ) {
			$list[ $id ] = get_the_title( $id );
		}

		return $list;
	}
}