<?php

namespace RtclJobManager\Widgets;

use Rtcl\Helpers\Functions;
use Rtcl\Models\WidgetFields;
use RtclJobManager\Helpers\Functions as JobFunction;
use WP_Term;
use WP_Widget;
use Rtcl\Models\Form\Form;

/**
 * Class Filter
 *
 * @package Rtcl\Widgets
 */
class JobFilter extends WP_Widget {

	protected $widget_slug;
	protected $instance;

	public function __construct() {

		$this->widget_slug = 'rtcl-job-widget-filter';

		parent::__construct(
			$this->widget_slug,
			esc_html__( 'Classified Listing Job Filter', 'rtcl-job-manager' ),
			[
				'classname'   => 'rtcl ' . $this->widget_slug,
				'description' => esc_html__( 'Classified listing job filter widget.', 'rtcl-job-manager' ),
			]
		);
	}


	/**
	 * Front-end markup
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		if ( empty( $instance ) ) {
			$instance = $this->getDefaultValues();
		}
		$this->instance = $instance;
		global $wp;
		$queried_object = get_queried_object();
		foreach ( [ rtcl()->location, rtcl()->category, rtcl()->tag ] as $taxonomy ) {
			if ( is_a( $queried_object, WP_Term::class ) && $queried_object->taxonomy === $taxonomy ) {
				$queried_object = clone $queried_object;
				unset( $queried_object->description );
				$this->instance['current_taxonomy'][ $taxonomy ] = clone $queried_object;
			} else {
				$q_term = $term = '';
				if ( isset( $wp->query_vars[ $taxonomy ] ) ) {
					$q_term = explode( '/', $wp->query_vars[ $taxonomy ] );
					$q_term = end( $q_term );
				}
				if ( $q_term && $term = get_term_by( 'slug', $q_term, $taxonomy ) ) {
					$term = clone $term;
					unset( $term->description );
				}
				$this->instance['current_taxonomy'][ $taxonomy ] = $term;
			}
		}
		?>
        <div class="rtcl-widget-filter-wrapper <?php echo esc_attr( $instance['filter_style'] ?? '' ); ?>">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			$job_archive_page = JobFunction::job_archive_page();
			$url              = get_permalink( $job_archive_page );
			?>
            <div class="panel-block">
                <form class="rtcl-job-filter-form" action="<?php echo esc_url( $url ); ?>">
                    <div class="job-search-filter">
						<?php
						$this->get_category_filter();
						$this->get_location_filter();
						$this->get_others_filter();
						Functions::print_html( $this->get_price_filter(), true );
						?>
                    </div>
                    <div class="form-footer">
                        <input type="submit" value="<?php echo esc_attr__( 'Apply Filter', 'rtcl-job-manager' ); ?>">
                        <a class="btn reset-btn" href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr__( 'Reset', 'rtcl-job-manager' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g>
                                    <path d="M21 12a9 9 0 1 1-3.84-7.36l-.11-.32A1 1 0 0 1 19 3.68l1 3a1 1 0 0 1-.14.9A1 1 0 0 1 19 8h-3a1 1 0 0 1-1-1 1 1 0 0 1 .71-.94A7 7 0 1 0 19 12a1 1 0 0 1 2 0z" data-name="Layer 114" fill="#000000" opacity="1" data-original="#000000"></path>
                                </g></svg>
                        </a>
                    </div>
                </form>
            </div>
			<?php

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['after_widget'];
			?>
        </div>
		<?php
	}

	public function get_instance() {
		return $this->instance;
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']              = ! empty( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['search_by_category'] = ! empty( $new_instance['search_by_category'] ) ? 1 : 0;
		$instance['search_by_location'] = ! empty( $new_instance['search_by_location'] ) ? 1 : 0;
		$instance['search_by_price']    = ! empty( $new_instance['search_by_price'] ) ? 1 : 0;
		$instance['search_by_cf']       = ! empty( $new_instance['search_by_cf'] ) ? 1 : 0;

		return apply_filters( 'rtcl_widget_filter_update_values', $instance, $new_instance, $old_instance, $this );
	}

	/**
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance ) {
		// Parse incoming $instance into an array and merge it with $defaults
		$instance     = $this->getDefaultValues( $instance );
		$fields       = self::widget_filter_fields();
		$widgetFields = new WidgetFields( $fields, $instance, $this );
		$widgetFields->render();
	}


	static function widget_filter_fields() {
		$fields = [
			'title'              => [
				'label' => esc_html__( 'Title', 'rtcl-job-manager' ),
				'type'  => 'text',
			],
			'search_by_category' => [
				'label' => esc_html__( 'Category?', 'rtcl-job-manager' ),
				'type'  => 'checkbox',
			],
			'search_by_location' => [
				'label' => esc_html__( 'Location?', 'rtcl-job-manager' ),
				'type'  => 'checkbox',
			],
			'search_by_price'    => [
				'label' => esc_html__( 'Salary?', 'rtcl-job-manager' ),
				'type'  => 'checkbox',
			],
			'search_by_cf'       => [
				'label' => esc_html__( 'Custom Fields? - Choose available custom fields from Job Settings.', 'rtcl-job-manager' ),
				'type'  => 'checkbox',
			],
		];

		return apply_filters( 'rtcl_job_widget_filter_fields', $fields );
	}

	public function getDefaultValues( $instance = [] ) {
		// Define the array of defaults
		$defaults = [
			'title'              => esc_html__( 'Filter', 'rtcl-job-manager' ),
			'search_by_category' => 1,
			'search_by_location' => 1,
			'search_by_price'    => 1,
			'search_by_cf'       => 1,
		];

		// Parse incoming $instance into an array and merge it with $defaults
		return wp_parse_args( (array) $instance, $defaults );
	}

	public function get_category_filter() {
		if ( ! empty( $this->instance['search_by_category'] ) ) {
			$job_category = ! empty( $_REQUEST['category'] ) ? sanitize_text_field( $_REQUEST['category'] ) : '';
			?>
            <div class="input-group">
                <label for="rtcl-job-category"><?php echo esc_html__( 'Category', 'rtcl-job-manager' ); ?></label>
                <select id="rtcl-job-category" name="category" class="rtcl-select2 rtcl-select rtcl-form-control">
                    <option value="">--<?php esc_html_e( 'Select Category', 'rtcl-job-manager' ); ?>--</option>
					<?php
					$categories = Functions::get_one_level_categories( 0, 'job' );

					if ( ! empty( $categories ) ) {
						foreach ( $categories as $category ) {

							$slt         = '';
							$category_id = $category->term_id;
							if ( $job_category && $category->term_id == $job_category ) {
								$category_id = $job_category;
								$slt         = ' selected';
							}
							printf(
								"<option  value='%s' %s>%s</option>",
								esc_attr( $category_id ),
								$slt,
								$category->name
							);
						}
					}
					?>
                </select>
            </div>

			<?php
		}
	}

	/**
	 * @return null|string
	 */
	public function get_location_filter() {

		if ( ! empty( $this->instance['search_by_location'] ) ) {
			$job_location     = ! empty( $_REQUEST['location'] ) ? sanitize_text_field( $_REQUEST['location'] ) : '';
			$job_sub_location = ! empty( $_REQUEST['sub_location'] ) ? sanitize_text_field( $_REQUEST['sub_location'] ) : '';
			$location         = Functions::get_one_level_locations();
			$sub_location     = $job_location ? Functions::get_one_level_locations( $job_location ) : '';
			?>

            <div class="rtcl-job-location-fields input-group">
                <label for="rtcl-job-location"><?php echo esc_html__( 'Location', 'rtcl-job-manager' ); ?></label>
                <select id="rtcl-job-location" name="location" class="rtcl-select2 rtcl-select rtcl-form-control">
                    <option value="">--<?php esc_html_e( 'Select location', 'rtcl-job-manager' ); ?>--</option>
					<?php
					if ( ! empty( $location ) ) {
						foreach ( $location as $location ) {
							$slt         = '';
							$location_id = $location->term_id;
							if ( $job_location && $location->term_id == $job_location ) {
								$location_id = $job_location;
								$slt         = ' selected';
							}
							printf(
								"<option  value='%s' %s>%s</option>",
								esc_attr( $location_id ),
								$slt,
								$location->name
							);
						}
					}
					?>
                </select>
                <div class="sub-location">
					<?php
					if ( $sub_location ) :
						?>
                        <select id="rtcl-job-location" name="sub_location" class="rtcl-select2 rtcl-select rtcl-form-control">
                            <option value="">--<?php esc_html_e( 'Select sub location', 'rtcl-job-manager' ); ?>--</option>
							<?php
							if ( ! empty( $sub_location ) ) {
								foreach ( $sub_location as $location ) {

									$slt         = '';
									$location_id = $location->term_id;
									if ( $job_sub_location && $location->term_id == $job_sub_location ) {
										$location_id = $job_sub_location;
										$slt         = ' selected';
									}
									printf(
										"<option  value='%s' %s>%s</option>",
										esc_attr( $location_id ),
										$slt,
										$location->name
									);
								}
							}
							?>
                        </select>
					<?php endif; ?>
                </div>
            </div>

			<?php
		}
	}

	public function get_others_filter() {

		$job_archive_page = JobFunction::job_form_builder();

		if ( ! $job_archive_page ) {
			return;
		}
		$fg_id      = "job_search_fields_{$job_archive_page}";
		$fieldGroup = Functions::get_option_item( 'rtcl_job_manager_settings', $fg_id, '' );
		$form       = Form::query()->find( $job_archive_page );
		if ( $form && ! empty( $this->instance['search_by_cf'] ) ) {

			if ( is_array( $fieldGroup ) && count( $fieldGroup ) ) {
				foreach ( $fieldGroup as $f_item ) {
					$field_info = $form->getFieldByName( $f_item );

					if ( 'select' != $field_info['element'] ) {
						continue;
					}
					$id         = $f_item;
					$flabel     = $field_info['label'];
					$select_opt = $field_info['options'] ?? [];
					$_selected  = ! empty( $_REQUEST[ $id ] ) ? sanitize_text_field( $_REQUEST[ $id ] ) : '';
					?>
                    <div class="input-group">
                        <label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $flabel ); ?></label>
                        <select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" class="rtcl-select2 rtcl-select rtcl-form-control">
                            <option value="">--<?php echo esc_html( $flabel ); ?>--</option>
							<?php
							foreach ( $select_opt as $opt ) {
								$slt = '';
								if ( $_selected == $opt['label'] ) {
									$slt = ' selected';
								}
								printf(
									"<option value='%s' %s>%s</option>",
									esc_attr( $opt['label'] ),
									$slt,
									esc_attr( $opt['value'] ),
								);
							}
							?>
                        </select>
                    </div>
					<?php

				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_price_filter() {
		if ( ! empty( $this->instance['search_by_price'] ) ) {
			$fMinValue = ! empty( $_GET['min_salary'] ) ? esc_attr( $_GET['min_salary'] ) : '';
			?>
            <div class="input-group">
                <label for="job_min_salary"><?php echo esc_html__( 'Search by Salary', 'rtcl-job-manager' ); ?></label>
                <input id="job_min_salary" type="number" name="min_salary" class="rtcl-form-control" placeholder="<?php echo esc_attr__( 'Minimum salary', 'rtcl-job-manager' ); ?>" value="<?php echo esc_attr( $fMinValue ); ?>">
            </div>
			<?php
		}
	}
}
