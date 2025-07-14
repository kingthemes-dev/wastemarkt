<?php

namespace  RtclElb\DiviModule\ListingActionSocialShare;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingActionSocialShare extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_action_social_share';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Action and Social Share', 'rtcl-elementor-builder' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->folder_name = 'et_pb_classified_single_page_modules';
		$this->settings_modal_toggles = [
			'general'  => [
				'toggles' => [
					'general'    => esc_html__( 'General', 'rtcl-elementor-builder' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'action'       => esc_html__( 'Action', 'rtcl-elementor-builder' ),
					'social'       => esc_html__( 'Social Share', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_show_favourites'       => [
				'label'          =>  __('Show Favourites', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'        => 'on',
				'description'    => esc_html__( 'Select list style.', 'rtcl-elementor-builder' ),
				'mobile_options' => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'general',
			],
			'rtcl_show_social_share' => [
				'label'      		=> esc_html__('Show Social Share', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'option_category' 	=> 'basic_option',
				'default'         	=> 'on',
				'tab_slug'    		=> 'general',
				'toggle_slug' 		=> 'general',
			],
			'rtcl_report_abuse'        => [
				'label'       => esc_html__('Show Report Abuse', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_inline_style'        => [
				'label'       => __('Inline Style', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'off',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			// computed.
			'__listing_action_social_share'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingActionSocialShare\ListingActionSocialShare', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_inline_style',
					'rtcl_report_abuse',
					'rtcl_show_social_share',
					'rtcl_show_favourites',
				)
			),
			// visibility
			'rtcl_action_color'       => [
				'label'       => esc_html__( 'Action Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'action',
				'hover'       => 'tabs',
			],
			'rtcl_social_color'       => [
				'label'       => esc_html__( 'Social Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'social',
				'hover'       => 'tabs',
			],
			'rtcl_social_bg_color'       => [
				'label'       => esc_html__( 'Social Background Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'social',
				'hover'       => 'tabs',
			]
		];
		return $fields;
	}

	public function get_advanced_fields_config() {

		$advanced_fields                = [];
		$advanced_fields['text']        = [];
		$advanced_fields['text_shadow'] = [];

		$advanced_fields['fonts'] = [
			'action'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon .list-group-item:not(.rtcl-sidebar-social) a',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'action',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
			'social'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon .rtcl-sidebar-social .rtcl-icon',
				),
				'important'        => 'all',
				'hide_text_color'  => true,
				'hide_text_shadow' => true,
				'hide_text_align'  => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'divider',
				'line_height'      => array(
					'range_settings' => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '.1',
					),
					'default'        => '1.2em',
				),
				'font_size'        => array(
					'default' => '18px',
				),
				'font'             => [
					'default' => '|700|||||||',
				],
			],
		];
		$advanced_fields['margin_padding'] = [
			'css'         => [
				'main'      => '%%order_class%% .el-single-addon .list-group-item:not(.rtcl-sidebar-social) a',
				'important' => 'all',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'general',
		];

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		add_filter('rtcl_listing_is_social_share_for_single', function () {
			return true;
		}, 20);

		// Store the callback in a variable
		$actions_filter_callback = function ( $the_actions ) use ( $settings ) {
			if ( ! $settings['rtcl_show_social_share'] || $settings['rtcl_show_social_share'] === 'off' ) {
				$the_actions['social'] = false;
			} else {
				$the_actions['social'] = $the_actions['social'];
			}
			if( !get_the_ID() && $settings['rtcl_show_social_share'] === 'on'){
				$the_actions['social'] = '<a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u=http://dev.local/listing/unlocking-potential-transforming-undefined-spaces-into-opportunities/" target="_blank" rel="nofollow"><span class="rtcl-icon rtcl-icon-facebook"></span></a>

											<a class="twitter" href="https://twitter.com/intent/tweet?text=Unlocking%20Potential:%20Transforming%20Undefined%20Spaces%20into%20Opportunities&amp;url=http://dev.local/listing/unlocking-potential-transforming-undefined-spaces-into-opportunities/" target="_blank" rel="nofollow"><span class="rtcl-icon fa-brands fa-x-twitter"></span></a>
										
											<a class="linkedin" href="https://www.linkedin.com/shareArticle?url=http://dev.local/listing/unlocking-potential-transforming-undefined-spaces-into-opportunities/&amp;title=Unlocking%20Potential:%20Transforming%20Undefined%20Spaces%20into%20Opportunities" target="_blank" rel="nofollow"><span class="rtcl-icon rtcl-icon-linkedin"></span></a>
										
										
											<a class="whatsapp" href="https://wa.me/?text=Unlocking%20Potential:%20Transforming%20Undefined%20Spaces%20into%20Opportunities http%3A%2F%2Fdev.local%2Flisting%2Funlocking-potential-transforming-undefined-spaces-into-opportunities%2F" data-action="share/whatsapp/share" target="_blank" rel="nofollow"><i class="rtcl-icon rtcl-icon-whatsapp"></i></a>';
			}
			$favourites = !empty($settings['rtcl_show_favourites']) && $settings['rtcl_show_favourites'] === 'on' ? true : false;
			$report_abuse = !empty($settings['rtcl_report_abuse']) && $settings['rtcl_report_abuse'] === 'on' ? true : false;
			$the_actions['can_add_favourites'] =  $favourites ;
			$the_actions['can_report_abuse']   =  $report_abuse ;
			return $the_actions;
		};

		// Add the filter
		add_filter( 'rtcl_listing_the_actions', $actions_filter_callback, 20, 1 );

		$template_style = 'divi/listing-action-social-share/actions';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'instance'      => $settings,
			'listing'       => rtcl()->factory->get_listing(self::listing_id()),
			'template_path' => Fns::get_plugin_template_path(),
		];

		// Get the rendered template content
		$output = Functions::get_template_html( $data['template'], $data, '', $data['template_path'] );

		// Remove the filter after rendering
		remove_filter( 'rtcl_listing_the_actions', $actions_filter_callback, 20 );

		return $output;
	}

	public static function listing_id(): int {
		$_id = self::get_prepared_listing_id();
		return absint($_id);
	}

	/**
	 * Widget result.
	 *
	 * @param [array] $data array of query.
	 *
	 * @return array
	 */

	public function render( $unprocessed_props, $content, $render_slug ) {
		$settings = $this->props;
		$this->render_css( $render_slug );
		return self::get_content( $settings );
	}

	protected function render_css( $render_slug ) {
		$wrapper = '%%order_class%% .el-single-addon ';

		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'list-group-item:not(.rtcl-sidebar-social) a',
				'text_color' => 'rtcl_action_color',
			],
			[
				'class' => 'rtcl-sidebar-social .rtcl-icon',
				'text_color' => 'rtcl_social_color',
				'bg_color'   => 'rtcl_social_bg_color',

			],
		];

		foreach ( $badge_styles as $badge ) {
			$selector = "$wrapper .{$badge['class']}";
			$text_color = $this->props[ $badge['text_color'] ] ?? '';
			$bg_color   = $this->props[ $badge['bg_color'] ?? '' ] ?? '';

			if ( ! empty( $text_color ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'color: %1$s !important;', $text_color ),
					]
				);
			}

			if ( ! empty( $bg_color ) ) {
				\ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => $selector,
						'declaration' => sprintf( 'background-color: %1$s !important;', $bg_color ),
					]
				);
			}
		}
	}



}