<?php
/**
 * Main Elementor ListingCustomFields Class
 *
 * ListingCustomFields main class
 *
 * @author  RadiusTheme
 * @since   2.0.10
 * @package  RTCL_Elementor_Builder
 * @version 1.2
 */

namespace RtclElb\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RtclElb\Helpers\Fns;
use Rtcl\Helpers\Functions;
use RtclElb\Widgets\WidgetSettings\FBDataSettings;

/**
 * ListingCustomFields class
 */
class FBData extends FBDataSettings {

	/**
	 * Construct function
	 *
	 * @param array  $data Some data.
	 * @param [type] $args some arg.
	 */
	public function __construct( $data = [], $args = null ) {
		$this->rtcl_name = __( 'Custom Fields - Form Builder Data', 'rtcl-elementor-builder' );
		$this->rtcl_base = 'rt-listing-form-builder-data';
		parent::__construct( $data, $args );
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render_repeater() {
		?>

		<?php
	}
	/**
	 * Display Output.
	 *
	 * @return mixed
	 */
	protected function render() {
		$settings        = $this->get_settings();
		$customFieldData = $settings['custom_form_field_data'] ?? [];
		if ( empty( $customFieldData ) || ! is_array( $customFieldData ) ) {
			return;
		}

		// if ( empty( $this->listing) ) {
		// 	return;
		// }

		$field = Fns::formBuilderData( $this->listing );
		?>
		<div class="form-builder-data-wrapper el-single-addon">
			<?php
			foreach ( $customFieldData as $key => $value ) {
				if ( 'yes' !== $value['show_in_frontend'] ) {
					continue;
				}
				$fieldName = $value['select_form_data_fields_name'] ?? '';
				$label = $value['select_form_data_fields_label'] ?? '';
				$template  = 'single/FB/';
				if ( empty( $field[ $fieldName ]['element'] ) ) {
					continue;
				}
				switch ( $field[ $fieldName ]['element'] ) {
					case 'file':
					case 'checkbox':
					case 'repeater':
						$template .= $field[ $fieldName ]['element'];
						break;
					case 'url':
						$fields_for = $value['select_form_data_fields_for'] ?? 'text';
						if ( 'text' === $fields_for ) {
							$template .= 'default';
						} else {
							$template                         .= $field[ $fieldName ]['element'];
							$field[ $fieldName ]['fields_for'] = $value['select_form_data_fields_for'] ?? 'text';
						}
						break;
					case 'text':
						if ( ! is_array( $field[ $fieldName ]['value'] ) ) {
							$template .= 'default';
						} else {
							$template .= 'array-' . $field[ $fieldName ]['element'];
						}
						break;
					default:
						$template .= 'default';
				}

				if ( 'yes' == $value['show_in_frontend_label'] ) {
					if ( ! empty( $label ) ) {
						$field[ $fieldName ]['label'] = $label;
					}
				} else {
					$field[ $fieldName ]['label'] = '';
				}

				$data = [
					'field'                 => $field[ $fieldName ],
					'sectionTitle'          => $label,
					'instance'              => $settings,
					'show_icon'             => 'yes' === ( $settings['show_icon'] ?? '' ),
					'default_template_path' => rtclElb()->get_plugin_template_path(),
				];
				$data = apply_filters( 'rtcl_listing_fb_data', $data );
				Functions::get_template( $template, $data, '', $data['default_template_path'] );
			}
			?>
		</div>
		<?php
	}
}