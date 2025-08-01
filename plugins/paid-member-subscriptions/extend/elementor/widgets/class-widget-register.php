<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Elementor widget for our pms-account shortcode
 */
class PMS_Elementor_Register_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 */
	public function get_name() {
		return 'pms-register';
	}

	/**
	 * Get widget title.
	 *
	 */
	public function get_title() {
		return __( 'Register', 'paid-member-subscriptions' );
	}

	/**
	 * Get widget icon.
	 *
	 */
	public function get_icon() {
		return 'eicon-price-list';
	}

	/**
	 * Get widget categories.
	 *
	 */
	public function get_categories() {
		return array( 'paid-member-subscriptions' );
	}

	/**
	 * Register widget controls
	 *
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'pms_content_section',
			array(
				'label' => __( 'Layout', 'paid-member-subscriptions' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

        $pms_subscription_plans_list = pms_get_subscription_plans_list();
        $pms_subscription_plans_list['none'] = 'None';

        $this->add_control(
            'pms_subscription_plans',
            array(
                'label'    => __( 'Subscription Plans', 'paid-member-subscriptions' ),
                'type'     => \Elementor\Controls_Manager::SELECT2,
                'options'  => $pms_subscription_plans_list,
                'multiple' => 'true',
            )
        );

        $this->add_control(
            'pms_selected_plan',
            array(
                'label'   => __( 'Selected Plan', 'paid-member-subscriptions' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => pms_get_subscription_plans_list(),
            )
        );

        $this->add_control(
            'pms_plans_position',
            array(
                'label'   => __( 'Plans Position', 'paid-member-subscriptions' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'top'    => __( 'Top', 'paid-member-subscriptions' ),
                    'bottom' => __( 'Bottom', 'paid-member-subscriptions' )
                ),
                'default' => 'bottom',
            )
        );

		$this->end_controls_section();

	}

	/**
	 * Render widget output in the front-end
	 *
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
        $pms_selected_plans = $settings['pms_subscription_plans'];

        if ( !empty( $pms_selected_plans ) ){
            if ( count($pms_selected_plans) === 1 && $pms_selected_plans[0] === 'none' ) {
                $plans = 'subscription_plans="__none__"';
            } else {
                $plans = 'subscription_plans="' . esc_attr( implode(',', $pms_selected_plans) ) . '"';
            }
        }
        else
            $plans = '';


        //check why the subscription plans parameter one isn't workking
		echo do_shortcode( '[pms-register '.$plans.' plans_position="'. esc_attr( $settings['pms_plans_position'] ).'" selected="'. esc_attr( $settings['pms_selected_plan'] ).'"]');

	}

	protected function is_dynamic_content(): bool {

		return true;

	}

}
