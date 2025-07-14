<?php

namespace  RtclElb\DiviModule\ListingReview;
use Rtcl\Helpers\Functions;
use Rtcl\Traits\Addons\ListingItem;
use RtclElb\Helpers\Fns;

Class ListingReview extends \ET_Builder_Module {
	use ListingItem;
	public $slug = 'rtcl_listing_review';
	public $vb_support = 'on';
	public $icon_path;
	protected $module_credits
		= [
			'author'     => 'RadiusTheme',
			'author_uri' => 'https://radiustheme.com',
		];
	public function init() {
		$this->name      = esc_html__( 'Listing Review', 'rtcl-elementor-builder' );
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
					'general'       => esc_html__( 'General Style', 'rtcl-elementor-builder' ),
					'comment_header'       => esc_html__( 'Comment List Header Section', 'rtcl-elementor-builder' ),
					'header_button' => esc_html__( 'Header Button', 'rtcl-elementor-builder' ),
					'leave_review' => esc_html__( 'Leave Review', 'rtcl-elementor-builder' ),
					'comment_list' => esc_html__( 'Comment List', 'rtcl-elementor-builder' ),
					'form' => esc_html__( 'Form', 'rtcl-elementor-builder' ),
					'form_button' => esc_html__( 'Form Button', 'rtcl-elementor-builder' ),
				],
			],
		];
	}

	public function get_fields() {
		$fields =  [
			'rtcl_show_comment_list'       => [
				'label'          =>  __('Comment List', 'rtcl-elementor-builder'),
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
			'comment_list_style' => [
				'label'      		=> esc_html__('Comment List Style', 'rtcl-elementor-builder'),
				'type'           => 'select',
				'options'        => [
					'inline' => __( 'Inline', 'rtcl-elementor-builder' ),
					'newline' => __( 'Newline', 'rtcl-elementor-builder' ),
				],
				'option_category' 	=> 'basic_option',
				'default'         	=> 'inline',
				'tab_slug'    		=> 'general',
				'toggle_slug' 		=> 'general',
				'show_if'    => [
					'rtcl_show_comment_list' => 'on',
				],
			],
			'rtcl_show_review_title'        => [
				'label'       => esc_html__('Review Section Title', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
				'show_if'    => [
					'rtcl_show_comment_list' => 'on',
				],
			],
			'rtcl_review_title_text'        => [
				'label'       => __('Review Title Text', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => 'Reviews',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
				'show_if'    => [
					'rtcl_show_review_title' => 'on',
				],
			],
			'rtcl_show_leave_review_button'        => [
				'label'       => __('Leave Review Button', 'rtcl-elementor-builder'),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'Yes', 'rtcl-elementor-builder' ),
					'off' => esc_html__( 'No', 'rtcl-elementor-builder' ),
				],
				'default'     => 'on',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
				'show_if'    => [
					'rtcl_show_comment_list' => 'on',
				],
			],
			'rtcl_leave_review_button_text'        => [
				'label'       => __('Leave Review Button Text', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => 'Leave Review',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'show_if'    => [
					'rtcl_show_leave_review_button' => 'on',
				],
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_review_meta'        => [
				'label'       => __('Review Meta', 'rtcl-elementor-builder'),
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
			'rtcl_show_comment_form'        => [
				'label'       => __('Review Form', 'rtcl-elementor-builder'),
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
			'rtcl_comment_form_title_text'        => [
				'label'       => __('Review form Title Text', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => 'Leave Review',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			'rtcl_comment_form_button_text'        => [
				'label'       => __('Submit Button Text', 'rtcl-elementor-builder'),
				'type'        => 'text',
				'default'     => 'Submit Review',
				'description' => __( 'Show / Hide new badge.', 'rtcl-elementor-builder' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general',
			],
			// computed.
			'__listing_review'           => array(
				'type'                => 'computed',
				'computed_callback'   => array('RtclElb\DiviModule\ListingReview\ListingReview', 'get_content' ),
				'computed_depends_on' => array(
					'rtcl_comment_form_button_text',
					'rtcl_comment_form_title_text',
					'rtcl_show_comment_form',
					'rtcl_review_meta',
					'rtcl_leave_review_button_text',
					'rtcl_show_leave_review_button',
					'rtcl_review_title_text',
					'rtcl_show_review_title',
					'comment_list_style',
					'rtcl_show_comment_list'
				)
			),
			// visibility
			'rtcl_price_color'       => [
				'label'       => esc_html__( 'Price Color', 'rtcl-elementor-builder' ),
				'description' => esc_html__( 'Here you can define a custom color for category name.', 'rtcl-elementor-builder' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'price',
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
			'comment_header'       => [
				'css'              => array(
					'main' => '%%order_class%% .el-single-addon.rtcl-Reviews.rtcl #comments .rtcl-reviews-meta .rtcl-single-listing-section-title',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'comment_header',
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
			'comment_list'       => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-Reviews.rtcl #comments ol.comment-list li',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'comment_list',
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
			'leave_review'       => [
				'css'              => array(
					'main' => '%%order_class%% .rtcl-Reviews.rtcl #reply-title',
				),
				'important'        => 'all',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'leave_review',
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
		
			]
		];
		$advanced_fields['button'] = array(
			'form_button'        => array(
				'label'           => __( 'Submit Button', 'rtcl-elementor-builder' ),
				'css'             => array(
					'main' => '%%order_class%% #review-form #respond .form-submit .btn.btn-primary',
				),
				'use_alignment'   => false,
				'border_width'    => array(
					'default' => '2px',
				),
				'box_shadow'      => array(
					'css' => array(
						'main' => '%%order_class%% ##review-form #respond .form-submit .btn.btn-primary',
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'important' => 'all',
					),
				),
				'toggle_priority' => 80,
				'toggle_slug'     => 'form_button',
			),
			'header_button' => array(
				'label'           => __( 'Leave Review', 'rtcl-elementor-builder' ),
				'css'             => array(
					'main' => '%%order_class%% #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a',
				),
				'use_alignment'   => false,
				'border_width'    => array(
					'default' => '2px',
				),
				'box_shadow'      => array(
					'css' => array(
						'main' => '%%order_class%% #comments .rtcl-reviews-meta .rtcl-reviews-meta-action a',
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'important' => 'all',
					),
				),
				'toggle_slug'     => 'header_button',
				'toggle_priority' => 80,
			)
		);
		$advanced_fields['margin_padding'] = array(
			'use_margin'   => true,
			'use_padding'  => true,
			'css'          => array(
				'main'      => "%%order_class%% .el-single-addon.rtcl-Reviews.rtcl",
				'important' => 'all',
			),
			'label_prefix' => __( 'Heading', 'rtcl-elementor-builder' ),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'general',
		);
		$advanced_fields['form_field']     = array(
			'form_field'  => array(
				'label'           => __( 'Fields', 'rtcl-elementor-builder' ),
				'css'             => array(
					'main'         => implode(
						',',
						[
							' %%order_class%% form .rtcl-form-group  #title',
							' %%order_class%% form .rtcl-form-group #comment',
						]
					),
					'border_radii' => implode(
						',',
						array(
							' %%order_class%% form .rtcl-form-group  #title',
							' %%order_class%% form .rtcl-form-group #comment',
						)
					),

					// Required to override default WooCommerce styles.
					'important'    => array( 'all' ),
				),
				'box_shadow'      => array(
					'css' => array(
						'main'         => implode(
							',',
							array(
								' %%order_class%% form .rtcl-form-group  #title',
								' %%order_class%% form .rtcl-form-group #comment',
							)
						),
						'border_radii' => implode(
							',',
							array(
								' %%order_class%% form .rtcl-form-group  #title',
								' %%order_class%% form .rtcl-form-group #comment',
							)
						),
						'important'    => array( 'all' ),
					),
				),
				'border_styles'   => array(
					'form_field'       => array(
						'label_prefix' => __( 'Fields', 'rtcl-elementor-builder' ),
						'css'          => array(
							'main'      => array(
								'border_styles' => implode(
									',',
									array(
										' %%order_class%% form .rtcl-form-group  #title',
										' %%order_class%% form .rtcl-form-group #comment',
									)
								),
								'border_radii'  => implode(
									',',
									array(
										' %%order_class%% form .rtcl-form-group  #title',
										' %%order_class%% form .rtcl-form-group #comment',
									)
								),
							),
							'important' => 'all',
						),
						'defaults'     => array(
							'border_radii'  => 'on|0px|0px|0px|0px',
							'border_styles' => array(
								'width' => '0px',
								'style' => 'solid',
							),
						),
					),
					'form_field_focus' => array(
						'label_prefix' => __( 'Fields Focus', 'rtcl-elementor-builder' ),
						'css'          => array(
							'main'      => array(
								'border_styles' => implode(
									',',
									array(
										' %%order_class%% form .rtcl-form-group  #title:focus',
										' %%order_class%% form .rtcl-form-group #comment:focus',
									)
								),
								'border_radii'  => implode(
									',',
									array(
										' %%order_class%% form .rtcl-form-group  #title:focus',
										' %%order_class%% form .rtcl-form-group #comment:focus',
									)
								),
							),
							'important' => 'all',
						),

						'defaults' => array(
							'border_radii'  => 'on|0px|0px|0px|0px',
							'border_styles' => array(
								'width' => '0px',
								'style' => 'solid',
							),
						),
					),
				),
				'font_field'      => array(
					'css'         => array(
						'main'      => implode(
							',',
							[
								' %%order_class%% form .rtcl-form-group input.rtcl-form-control',
								' %%order_class%% form .rtcl-form-group textarea.rtcl-form-control',
							]
						),

						// Required to override default WooCommerce styles.
						'important' => array( 'line-height', 'size', 'font', 'border-radius' ),
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
				),
				'margin_padding'  => array(
					'css' => array(
						'main'      => ' %%order_class%% form .rtcl-form-group .rtcl-form-control',
						'padding'   => ' %%order_class%% form .rtcl-form-group .rtcl-form-control',
						'margin'    => ' %%order_class%% form .rtcl-form-group .rtcl-form-control',
						'important' => 'all',
					),
				),
				'width'           => array(),
				'toggle_priority' => 55,
				'toggle_slug'     => 'form',
			)
		);
		

		return $advanced_fields;
	}

	public static function get_content( $settings ) {
		global $listing, $post, $comments;
		$_post    = $post;
		$_listing = $listing;
		$listing  = rtcl()->factory->get_listing(self::listing_id());
		if ( is_singular( 'rtcl_builder' ) ) {
			$post = get_post( $listing->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
		add_filter( 'rtcl_review_gravatar_image', function ($gravatar){
			return '<div class="gravatar-img">' . $gravatar . '</div>';
		} );
		$template_style = 'divi/listing-review/review';
		$data = [
			'template'      => $template_style,
			'settings'      => $settings,
			'instance'      => $settings,
			'post'			=> $_post,
			'listing'       => rtcl()->factory->get_listing(self::listing_id()),
			'template_path' => Fns::get_plugin_template_path(),
		];

		return Functions::get_template_html( $data['template'], $data, '', $data['template_path'] );
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
		$wrapper = '%%order_class%% .el-single-addon.item-price ';
		
		// ✅ Badge Styles (color + bg-color)
		$badge_styles = [
			[
				'class' => 'rtcl-price',
				'text_color' => 'rtcl_price_color',
				'bg_color'   => 'rtcl_price_bg_color',
			],
			[
				'class' => 'price-unit',
				'text_color' => 'rtcl_unit_color',
			],
			[
				'class' => 'price-type',
				'text_color' => 'rtcl_price_type_color',
			],
			[
				'class' => 'divider-after-price',
				'text_color' => 'rtcl_divider_color',
			]
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