<?php

namespace RtclJobManager\Admin;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Form\Form;
use Rtcl\Services\FormBuilder\FBField;
use Rtcl\Services\FormBuilder\FBHelper;

class Settings {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_filter( 'rtcl_register_settings_tabs', [ __CLASS__, 'add_job_manager_tab_item_at_settings_tabs_list' ] );
		add_filter( 'rtcl_settings_option_fields', [ __CLASS__, 'add_job_manager_tab_options' ], 10, 2 );
		add_action( 'admin_footer', [ __CLASS__, 'job_admin_footer' ] );
	}

	/**
	 * Job Admin CSS
	 *
	 * @return void
	 */
	public static function job_admin_footer() {
		if ( ! empty( $_GET['page'] ) && 'rtcl-settings' === $_GET['page'] ) { ?>
            <style>
              .rtcl-admin-main-settings .rtcl-settings-nav-wrap ul.nav-tab-wrapper li a.nav-job_manager:before {
                mask-image: url("data:image/svg+xml,%3Csvg width='19' height='19' viewBox='0 0 19 19' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M18.6426 11.5037V5.67033C18.6426 5.06283 18.4009 4.4795 17.9718 4.0495C17.5416 3.62016 16.9587 3.37892 16.3509 3.37866H3.01759C2.41009 3.37866 1.82676 3.62033 1.39676 4.0495C0.967418 4.47968 0.726177 5.06255 0.725922 5.67033V14.837C0.725922 15.4445 0.967588 16.0278 1.39676 16.4578C1.82694 16.8872 2.40981 17.1284 3.01759 17.1287H9.68426C9.85002 17.1287 10.009 17.0628 10.1262 16.9456C10.2434 16.8284 10.3093 16.6694 10.3093 16.5037C10.3093 16.3379 10.2434 16.1789 10.1262 16.0617C10.009 15.9445 9.85002 15.8787 9.68426 15.8787H3.01759C2.74146 15.8782 2.47676 15.7683 2.28151 15.5731C2.08625 15.3778 1.97636 15.1131 1.97592 14.837V5.67033C1.97636 5.3942 2.08625 5.1295 2.28151 4.93425C2.47676 4.73899 2.74146 4.6291 3.01759 4.62866H16.3509C16.6271 4.6291 16.8917 4.73899 17.087 4.93425C17.2823 5.1295 17.3921 5.3942 17.3926 5.67033V11.5037C17.3926 11.6694 17.4584 11.8284 17.5756 11.9456C17.6929 12.0628 17.8518 12.1287 18.0176 12.1287C18.1833 12.1287 18.3423 12.0628 18.4595 11.9456C18.5767 11.8284 18.6426 11.6694 18.6426 11.5037Z' fill='black'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M5.72592 4.00366V2.75366C5.72592 2.58783 5.79175 2.42866 5.90925 2.312C5.96715 2.25383 6.03598 2.20769 6.11178 2.17623C6.18758 2.14476 6.26885 2.1286 6.35092 2.12866H13.0176C13.1834 2.12866 13.3426 2.1945 13.4593 2.312C13.5768 2.42866 13.6426 2.58783 13.6426 2.75366V4.00366C13.6426 4.16942 13.7084 4.32839 13.8256 4.4456C13.9429 4.56281 14.1018 4.62866 14.2676 4.62866C14.4333 4.62866 14.5923 4.56281 14.7095 4.4456C14.8267 4.32839 14.8926 4.16942 14.8926 4.00366V2.75366C14.8926 2.25638 14.695 1.77947 14.3434 1.42784C13.9918 1.07621 13.5149 0.878662 13.0176 0.878662H6.35092C5.85364 0.878662 5.37673 1.07621 5.0251 1.42784C4.67347 1.77947 4.47592 2.25638 4.47592 2.75366V4.00366C4.47592 4.16942 4.54177 4.32839 4.65898 4.4456C4.77619 4.56281 4.93516 4.62866 5.10092 4.62866C5.26668 4.62866 5.42565 4.56281 5.54286 4.4456C5.66007 4.32839 5.72592 4.16942 5.72592 4.00366Z' fill='black'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M1.36508 5.06777L2.43591 7.76527C2.60519 8.19202 2.89875 8.55808 3.27853 8.81603C3.65831 9.07397 4.10682 9.2119 4.56591 9.21194H14.8026C15.2617 9.2119 15.7102 9.07397 16.09 8.81603C16.4697 8.55808 16.7633 8.19202 16.9326 7.76527L18.0034 5.06777C18.0646 4.91372 18.0622 4.74167 17.9965 4.58945C17.9309 4.43723 17.8075 4.31732 17.6534 4.2561C17.4994 4.19488 17.3273 4.19736 17.1751 4.263C17.0229 4.32864 16.903 4.45206 16.8417 4.6061L15.7709 7.30444C15.6134 7.7011 15.2292 7.96194 14.8026 7.96194H4.56591C4.13925 7.96194 3.75508 7.7011 3.59758 7.30444L2.52675 4.6061C2.46553 4.45206 2.34562 4.32864 2.1934 4.263C2.04118 4.19736 1.86913 4.19488 1.71508 4.2561C1.56103 4.31732 1.43762 4.43723 1.37198 4.58945C1.30634 4.74167 1.30386 4.91372 1.36508 5.06777Z' fill='black'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M12.3926 7.75366V9.42033C12.3926 9.58609 12.4584 9.74506 12.5756 9.86227C12.6929 9.97948 12.8518 10.0453 13.0176 10.0453C13.1833 10.0453 13.3423 9.97948 13.4595 9.86227C13.5767 9.74506 13.6426 9.58609 13.6426 9.42033V7.75366C13.6426 7.5879 13.5767 7.42893 13.4595 7.31172C13.3423 7.19451 13.1833 7.12866 13.0176 7.12866C12.8518 7.12866 12.6929 7.19451 12.5756 7.31172C12.4584 7.42893 12.3926 7.5879 12.3926 7.75366ZM5.72592 7.75366V9.42033C5.72592 9.58609 5.79177 9.74506 5.90898 9.86227C6.02619 9.97948 6.18516 10.0453 6.35092 10.0453C6.51668 10.0453 6.67565 9.97948 6.79286 9.86227C6.91007 9.74506 6.97592 9.58609 6.97592 9.42033V7.75366C6.97592 7.5879 6.91007 7.42893 6.79286 7.31172C6.67565 7.19451 6.51668 7.12866 6.35092 7.12866C6.18516 7.12866 6.02619 7.19451 5.90898 7.31172C5.79177 7.42893 5.72592 7.5879 5.72592 7.75366ZM10.7259 14.4203C10.7259 16.3753 12.3126 17.962 14.2676 17.962C16.2226 17.962 17.8093 16.3753 17.8093 14.4203C17.8093 12.4653 16.2226 10.8787 14.2676 10.8787C12.3126 10.8787 10.7259 12.4653 10.7259 14.4203ZM11.9759 14.4203C11.992 13.8232 12.2405 13.256 12.6685 12.8394C13.0966 12.4228 13.6703 12.1896 14.2676 12.1896C14.8649 12.1896 15.4386 12.4228 15.8666 12.8394C16.2946 13.256 16.5432 13.8232 16.5593 14.4203C16.5432 15.0174 16.2946 15.5846 15.8666 16.0013C15.4386 16.4179 14.8649 16.651 14.2676 16.651C13.6703 16.651 13.0966 16.4179 12.6685 16.0013C12.2405 15.5846 11.992 15.0174 11.9759 14.4203Z' fill='black'/%3E%3Cpath fill-rule='evenodd' clip-rule='evenodd' d='M15.9093 16.9454L17.5759 18.6121C17.6931 18.7292 17.8519 18.795 18.0176 18.795C18.1833 18.795 18.3421 18.7292 18.4593 18.6121C18.5764 18.495 18.6422 18.3361 18.6422 18.1704C18.6422 18.0048 18.5764 17.8459 18.4593 17.7288L16.7926 16.0621C16.7346 16.0041 16.6657 15.9581 16.59 15.9267C16.5142 15.8953 16.433 15.8792 16.3509 15.8792C16.2689 15.8792 16.1877 15.8953 16.1119 15.9267C16.0361 15.9581 15.9673 16.0041 15.9093 16.0621C15.8513 16.1201 15.8053 16.189 15.7739 16.2647C15.7425 16.3405 15.7263 16.4217 15.7263 16.5038C15.7263 16.5858 15.7425 16.667 15.7739 16.7428C15.8053 16.8186 15.8513 16.8874 15.9093 16.9454Z' fill='black'/%3E%3C/svg%3E%0A");
              }
            </style>

            <script>
                jQuery(document).ready(function () {
                    var jobSetting = jQuery('#rtcl_job_manager_settings-job_manager_enable')
                    var jobElement = jobSetting.closest('table.form-table').nextAll('.rtcl-settings-sub-title, table')

                    toggleRoot()
                    jobSetting.on('change', function () {
                        toggleRoot()
                    })

                    function toggleRoot () {
                        if (jobSetting.is(':checked')) {
                            jobElement.show()
                        } else {
                            jobElement.hide()
                        }
                    }
                })

            </script>
			<?php
		}
	}

	/**
	 * Add membership tab item
	 *
	 * @param array $tabs An array of existing tabs.
	 *
	 * @return array Modified array of tabs with the job manager tab added.
	 */
	public static function add_job_manager_tab_item_at_settings_tabs_list( $tabs ) {
		$tabs['job_manager'] = esc_html__( 'Job Manager', 'rtcl-job-manager' );

		return $tabs;
	}

	/**
	 * Add job_manager tab options
	 *
	 * @param array $fields An array.
	 * @param string $active_tab Active tab slug.
	 *
	 * @return mixed|null
	 */
	public static function add_job_manager_tab_options( $fields, $active_tab ) {
		if ( 'job_manager' == $active_tab ) {
			$fields = [
				'job_board_settings_title' => [
					'title'       => esc_html__( 'Job Manager Settings', 'rtcl-job-manager' ),
					'type'        => 'title',
					'description' => '',
				],
				'job_manager_enable'       => [
					'title'       => esc_html__( 'Job Manager', 'rtcl-job-manager' ),
					'label'       => esc_html__( 'Enable', 'rtcl-job-manager' ),
					'type'        => 'checkbox',
					'description' => esc_html__( 'Enable Job Manager.', 'rtcl-job-manager' ),
				],

				'job_settings' => [
					'title'       => esc_html__( 'General Settings', 'rtcl-job-manager' ),
					'type'        => 'title',
					'description' => '',
				],

				'job_separation' => [
					'title'       => esc_html__( 'Separate Job Archive', 'rtcl-job-manager' ),
					'label'       => esc_html__( 'Enable', 'rtcl-job-manager' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'description' => esc_html__( "If you're focusing exclusively on job listings, you can skip this field. However, if your site includes various types of listings, be sure to use this field to differentiate job postings from others.", 'rtcl-job-manager' ),
				],

				'show_archive_page_title' => [
					'title'   => esc_html__( 'Job Archive page title', 'rtcl-job-manager' ),
					'label'   => esc_html__( 'Enable Archive page title', 'rtcl-job-manager' ),
					'type'    => 'checkbox',
					'default' => 'yes',
				],

				'show_single_breadcrumb' => [
					'title'   => esc_html__( 'Breadcrumb?', 'rtcl-job-manager' ),
					'label'   => esc_html__( 'Enable Job Details Breadcrumb', 'rtcl-job-manager' ),
					'type'    => 'checkbox',
					'default' => 'yes',
				],

				'job_allow_register_users' => [
					'title' => esc_html__( 'User Restriction', 'rtcl-job-manager' ),
					'label' => esc_html__( 'Only allow registered users to apply', 'rtcl-job-manager' ),
					'type'  => 'checkbox',
				],

				'enable_top_job' => [
					'title' => esc_html__( 'Enable top job on the archive page', 'rtcl-job-manager' ),
					'label' => esc_html__( 'Enable top job', 'rtcl-job-manager' ),
					'type'  => 'checkbox',
				],

				'job_details_style' => [
					'title'       => esc_html__( 'Job Details Style', 'rtcl-job-manager' ),
					'options'     => [
						'1' => esc_html__( 'With Sidebar', 'rtcl-job-manager' ),
						'2' => esc_html__( 'No Sidebar', 'rtcl-job-manager' ),
					],
					'type'        => 'select',
					'description' => esc_html__( 'You may change job details page style.', 'rtcl-job-manager' ),
					'class'       => 'rtcl-select2',
					'blank_text'  => esc_html__( 'Choose Details Style', 'rtcl-job-manager' ),
					'css'         => 'min-width:150px;',
				],

				'job_archive_sidebar_pos' => [
					'title'       => esc_html__( 'Sidebar Position', 'rtcl-job-manager' ),
					'options'     => [
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'rtcl-job-manager' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'rtcl-job-manager' ),
					],
					'type'        => 'select',
					'description' => esc_html__( 'Select the position of the Job Archive Sidebar. Please choose a widget from the Widgets section to place in the sidebar.', 'rtcl-job-manager' ),
					'class'       => 'rtcl-select2',
					'blank_text'  => esc_html__( 'Choose Job Archive Sidebar', 'rtcl-job-manager' ),
					'css'         => 'min-width:150px;',
				],

				'job_archive_page' => [
					'title'       => esc_html__( 'Select Job Archive page', 'rtcl-job-manager' ),
					'options'     => Functions::get_pages(),
					'type'        => 'select',
					'description' => esc_html__( 'Choose a page as job archive page.', 'rtcl-job-manager' ),
					'class'       => 'rtcl-select2',
					'blank_text'  => esc_html__( 'Select a page', 'rtcl-job-manager' ),
					'css'         => 'min-width:300px;',
				],

				'job_meta_title' => [
					'title'       => esc_html__( 'Job Submission Form and Meta Settings', 'rtcl-job-manager' ),
					'type'        => 'title',
					'description' => esc_html__( "To manage job meta fields such as 'External Link' or 'Enable Job Submission' on the listing details page, ensure the relevant fields are configured here. If you modify the Job Form using the form builder, remember to update these field settings accordingly.", 'rtcl-job-manager' ),
				],

			];

			$form = Form::query()->select( 'id,title,fields' )->where( 'status', 'publish' )->get()->toArray();

			$_options = [];
			foreach ( $form as $f ) {
				$_options[ $f['id'] ] = $f['title'];
			}

			$fields['job_form_builder'] = [
				'title'       => esc_html__( 'Select Job Submission Form', 'rtcl-job-manager' ),
				'options'     => $_options,
				'type'        => 'select',
				'description' => esc_html__( 'Choose a Form first to select job fields', 'rtcl-job-manager' ),
				'blank_text'  => esc_html__( 'Select a Form', 'rtcl-job-manager' ),
				'css'         => 'min-width:300px;',
				'default'     => 'job-form',
			];

			foreach ( $form as $f ) {
				$_form   = Form::query()->find( $f['id'] );
				$_fields = $_form->getFieldAsGroup( FBField::CUSTOM );
				$_fields = FBHelper::reOrderCustomField( $_fields );

				$search_filter_options = $job_submission_switch_list = $external_link_options = $apply_now_btn_text = [];

				foreach ( $_fields as $field ) {
					$field     = new FBField( $field );
					$field_obj = $field->getField();

					$fieldType = $field_obj['element'] ?? '';

					$fieldID = $field_obj['name'] ?? '';
					$label   = $field_obj['label'] ?? '';

					if ( in_array( $fieldType, [ 'switch', 'radio' ] ) ) {
						$job_submission_switch_list[ $fieldID ] = $label;
					}

					if ( in_array( $fieldType, [ 'text', 'url' ] ) ) {
						$external_link_options[ $fieldID ] = $label;
					}

					if ( $fieldType == 'text' ) {
						$apply_now_btn_text[ $fieldID ] = $label;
					}

					if ( $fieldType == 'select' ) {
						$search_filter_options[ $fieldID ] = $label;
					}
				}

				$desc = esc_html__( 'These fields are dynamic and are generated by the form builder.', 'rtcl-job-manager' );

				if ( ! $search_filter_options ) {
					$desc = esc_html__( 'There is no dropdown/select fields in the form you choose from the above.', 'rtcl-job-manager' );
				}

				$fields[ 'job_search_fields_' . $f['id'] ] = [
					'title'       => esc_html__( 'Choose Fields for Search Filter (Archive Page)', 'rtcl-job-manager' ),
					'type'        => 'multi_checkbox',
					'description' => $desc,
					'options'     => $search_filter_options,
					'dependency'  => [
						'rules' => [
							'#rtcl_job_manager_settings-job_form_builder' => [
								'type'  => 'equal',
								'value' => (string) $f['id'],
							],
						],
					],
				];

				$fields[ 'job_submission_' . $f['id'] ] = [
					'title'       => esc_html__( 'Job Submission Switch', 'rtcl-job-manager' ),
					'type'        => 'select',
					'description' => esc_html__( 'What custom fields from the form-builder will control the Job Submission Form visibility? NB. switch and radio fields are available here.', 'rtcl-job-manager' ),
					'options'     => $job_submission_switch_list,
					'default'     => 'rtcl-job-submission-form',
					'dependency'  => [
						'rules' => [
							'#rtcl_job_manager_settings-job_form_builder' => [
								'type'  => 'equal',
								'value' => (string) $f['id'],
							],
						],
					],
				];

				$fields[ 'job_external_link_' . $f['id'] ] = [
					'title'       => esc_html__( 'External Link', 'rtcl-job-manager' ),
					'type'        => 'select',
					'description' => esc_html__( 'As we use a dynamic meta field for job so you need to specify a field as External Link. NB. URL field are available here.', 'rtcl-job-manager' ),
					'options'     => $external_link_options,
					'default'     => 'rtcl-job-external-link',
					'dependency'  => [
						'rules' => [
							'#rtcl_job_manager_settings-job_form_builder' => [
								'type'  => 'equal',
								'value' => (string) $f['id'],
							],
						],
					],
				];

				$fields[ 'job_apply_btn_text_' . $f['id'] ] = [
					'title'       => esc_html__( 'Apply Now button text', 'rtcl-job-manager' ),
					'type'        => 'select',
					'description' => esc_html__( 'Choose a option as "Apply Now" button change meta field for job submitter. NB. Text fields are available here.', 'rtcl-job-manager' ),
					'options'     => $apply_now_btn_text,
					'default'     => 'rtcl-apply-btn-text',
					'dependency'  => [
						'rules' => [
							'#rtcl_job_manager_settings-job_form_builder' => [
								'type'  => 'equal',
								'value' => (string) $f['id'],
							],
						],
					],
				];
			}

			$fields['job_submission_seetings_title'] = [
				'title' => esc_html__( 'Job Application Form Settings', 'rtcl-job-manager' ),
				'type'  => 'title',
			];

			$fields['enable_submission_form'] = [
				'title'   => esc_html__( 'Enable Job Application Form', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'You can enable job application from from the job details page.', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_birth_date'] = [
				'title'   => esc_html__( 'Date of Birth', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Date of Birth field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_whatsup'] = [
				'title'   => esc_html__( 'Whatsapp number', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Whatsapp number field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_phone'] = [
				'title'   => esc_html__( 'Phone', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Phone field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_website'] = [
				'title'   => esc_html__( 'Website', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Website field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_location'] = [
				'title'   => esc_html__( 'Location', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Location field. Included State, City, Zip', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_address'] = [
				'title'   => esc_html__( 'Address', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Address field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_social'] = [
				'title'   => esc_html__( 'Social Profile', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Social Profile field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_cv'] = [
				'title'   => esc_html__( 'Upload your CV', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Upload you CV field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields['enable_submission_cover_letter'] = [
				'title'   => esc_html__( 'Cover Letter', 'rtcl-job-manager' ),
				'label'   => esc_html__( 'Enable Cover Letter field', 'rtcl-job-manager' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			];

			$fields = apply_filters( 'rtcl_job_manager_settings_options', $fields );
		}

		return $fields;
	}

}
