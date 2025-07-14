<?php

namespace RtclJobManager\Helpers;

use Rtcl\Models\Form\Form;
use Rtcl\Services\FormBuilder\AvailableFields;
use Rtcl\Services\FormBuilder\FormPreDefined;
use Rtcl\Services\FormBuilder\SettingFields;

class Installer {
	/**
	 * @return void
	 */
	public static function activate() {
		if ( function_exists( 'rtcl' ) ) {
			$types_key = rtcl()->get_listing_types_option_id();
			self::create_job_submit_from();
		} else {
			$types_key = 'rtcl_listing_types';
		}
		$ad_types = get_option( $types_key );

		if ( is_array( $ad_types ) && ! in_array( rtcl_job_manager()->job_type_id(), array_keys( $ad_types ) ) ) {
			$ad_types[ rtcl_job_manager()->job_type_id() ] = 'Job';
			update_option( $types_key, $ad_types );
		}

		do_action( 'rtcl_flush_rewrite_rules' );

		self::create_job_application_table();
		self::add_job_page();
	}

	public static function create_job_application_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rtcl_job_applications';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					first_name VARCHAR(20),
					last_name VARCHAR(20),
					application_data longtext NOT NULL,
					user_id int(10) UNSIGNED DEFAULT NULL,
					listing_id int(10) UNSIGNED DEFAULT NULL,
					listing_cat int(10) UNSIGNED DEFAULT NULL,
					status VARCHAR(20) NOT NULL DEFAULT 'pending',
					created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	            PRIMARY KEY  (id)
	        ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
		/*
			  else {
					// This code for make a new column


					$column_exists = $wpdb->get_results( "SHOW COLUMNS FROM `$table_name` LIKE 'listing_cat'" );
					if ( empty( $column_exists ) ) {
						$sql = "ALTER TABLE $table_name ADD COLUMN listing_cat VARCHAR(30);";
						$wpdb->query( $sql );
					}
				}*/
	}

	public static function create_job_submit_from() {
		global $wpdb;
		$form_table = $wpdb->prefix . 'rtcl_forms';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$form_table'" ) ) {
			$row = $wpdb->get_var( "SELECT COUNT(*) from $form_table WHERE title = 'Job Submit' AND slug='job-submit'" );
			if ( ! $row ) {
				$formData = self::job_form_sample();
				Form::query()->insert( $formData );
			}
		}
	}

	public static function add_job_page() {

		$pSettings = get_option( 'rtcl_job_manager_settings', [] );

		if ( empty( $pSettings['job_archive_page'] ) ) {
			$id = wp_insert_post(
				[
					'post_title'     => 'Job Archive',
					'post_content'   => '',
					'post_status'    => 'publish',
					'post_author'    => 1,
					'post_type'      => 'page',
					'comment_status' => 'closed',
				]
			);

			if ( $id > 0 ) {
				$pSettings['job_archive_page'] = $id;
				update_option( 'rtcl_job_manager_settings', $pSettings );
			}
		}
	}

	/**
	 * @return void
	 */
	public static function deactivate() {
		do_action( 'rtcl_flush_rewrite_rules' );
	}

	public static function job_form_sample(): array {
		$availableFields = AvailableFields::get();

		$title         = $availableFields['title'];
		$title['uuid'] = uniqid();

		$listingType                    = $availableFields['listing_type'];
		$listingType['uuid']            = uniqid();
		$listingType['default_value']   = 'job';
		$listingType['container_class'] = 'hide-front-end';

		$category         = $availableFields['category'];
		$category['uuid'] = uniqid();

		$tag         = $availableFields['tag']; // Skills.
		$tag['uuid'] = uniqid();

		$pricing                     = $availableFields['pricing']; // Salary
		$pricing['uuid']             = uniqid();
		$pricing['label']            = __( 'Salary', 'classified-listing' );
		$pricing['options']          = [ 'pricing_type', 'price_type', 'price_unit' ];
		$pricing['price_type_label'] = __( 'Salary Type', 'classified-listing' );
		$pricing['price_unit_label'] = __( 'Salary Unit', 'classified-listing' );
		$pricing['price_label']      = __( 'Salary [$]', 'classified-listing' );

		$job_type                    = $availableFields['select'];
		$job_type['uuid']            = uniqid();
		$job_type['name']            = 'job-type';
		$job_type['container_class'] = 'col-md-4';
		$job_type['label']           = 'Job Type';
		$job_type['icon']            = [
			'type'  => 'class',
			'class' => 'rtcl-icon-filter',
		];
		$job_type['order']           = 3;
		$job_type['options']         = [
			[
				'label' => __( 'Permanent', 'classified-listing' ),
				'value' => __( 'Permanent', 'classified-listing' ),
			],
			[
				'label' => __( 'Temporary', 'classified-listing' ),
				'value' => __( 'Temporary', 'classified-listing' ),
			],
			[
				'label' => __( 'Freelance', 'classified-listing' ),
				'value' => __( 'Freelance', 'classified-listing' ),
			],
			[
				'label' => __( 'Contract', 'classified-listing' ),
				'value' => __( 'Contract', 'classified-listing' ),
			],
			[
				'label' => __( 'Part-time', 'classified-listing' ),
				'value' => __( 'Part-time', 'classified-listing' ),
			],
			[
				'label' => __( 'Full-time', 'classified-listing' ),
				'value' => __( 'Full-time', 'classified-listing' ),
			],
			[
				'label' => __( 'Internship', 'classified-listing' ),
				'value' => __( 'Internship', 'classified-listing' ),
			],
			[
				'label' => __( 'Seasonal', 'classified-listing' ),
				'value' => __( 'Seasonal', 'classified-listing' ),
			],
			[
				'label' => __( 'Remote', 'classified-listing' ),
				'value' => __( 'Remote', 'classified-listing' ),
			],
		];

		$experience                    = $availableFields['select'];
		$experience['uuid']            = uniqid();
		$experience['name']            = 'job-experience';
		$experience['label']           = 'Experience';
		$experience['container_class'] = 'col-md-4';
		$experience['order']           = 2;
		$experience['filterable']      = true;
		$experience['icon']            = [
			'type'  => 'class',
			'class' => 'rtcl-icon-signal',
		];
		$experience['options']         = [
			[
				'label' => __( 'Junior', 'classified-listing' ),
				'value' => __( 'Junior', 'classified-listing' ),
			],
			[
				'label' => __( 'Intermediate', 'classified-listing' ),
				'value' => __( 'Intermediate', 'classified-listing' ),
			],
			[
				'label' => __( 'Senior', 'classified-listing' ),
				'value' => __( 'Senior', 'classified-listing' ),
			],
		];

		$flexibility                    = $availableFields['select'];
		$flexibility['uuid']            = uniqid();
		$flexibility['name']            = 'job-flexibility';
		$flexibility['label']           = 'Flexibility';
		$flexibility['order']           = 1;
		$flexibility['filterable']      = true;
		$flexibility['container_class'] = 'col-md-4';
		$flexibility['icon']            = [
			'type'  => 'class',
			'class' => 'rtcl-icon-home',
		];
		$flexibility['options']         = [
			[
				'label' => __( 'Hybrid', 'classified-listing' ),
				'value' => __( 'Hybrid', 'classified-listing' ),
			],
			[
				'label' => __( 'Onsite', 'classified-listing' ),
				'value' => __( 'Onsite', 'classified-listing' ),
			],
			[
				'label' => __( 'Remote', 'classified-listing' ),
				'value' => __( 'Remote', 'classified-listing' ),
			],
		];

		$job_deadline                    = $availableFields['date'];
		$job_deadline['uuid']            = uniqid();
		$job_deadline['name']            = 'job_deadline';
		$job_deadline['label']           = 'Deadline';
		$job_deadline['container_class'] = 'col-md-4';
		$job_deadline['order']           = 7;
		$job_deadline['icon']            = [
			'type'  => 'class',
			'class' => 'rtcl-icon-calendar',
		];

		$vacancies                    = $availableFields['text'];
		$vacancies['uuid']            = uniqid();
		$vacancies['name']            = 'job-vacancies';
		$vacancies['label']           = 'Vacancies';
		$vacancies['container_class'] = 'col-md-4';
		$vacancies['order']           = 8;
		$vacancies['icon']            = [
			'type'  => 'class',
			'class' => 'rtcl-icon-users',
		];

		$office_time                    = $availableFields['text'];
		$office_time['uuid']            = uniqid();
		$office_time['name']            = 'job-office-time';
		$office_time['label']           = 'Office time';
		$office_time['container_class'] = 'col-md-4';
		$office_time['order']           = 8;
		$office_time['icon']            = [
			'type'  => 'class',
			'class' => 'rtcl-icon-clock',
		];

		$submission_form                  = $availableFields['switch'];
		$submission_form['uuid']          = uniqid();
		$submission_form['name']          = 'rtcl-job-submission-form';
		$submission_form['label']         = 'Enable Job Apply Form';
		$submission_form['help_message']  = "Uncheck to remove the job apply form from the job details page.";
		$submission_form['default_value'] = 'yes';
		$submission_form['order']         = 9;

		$external_link                 = $availableFields['url'];
		$external_link['uuid']         = uniqid();
		$external_link['name']         = 'rtcl-job-external-link';
		$external_link['label']        = 'External Link';
		$external_link['help_message'] = "Enter an external application link, if available.";
		$external_link['order']        = 9;
		$external_link['single_view']  = false;
		$external_link['archive_view'] = false;

		$apply_now_btn_text                 = $availableFields['text'];
		$apply_now_btn_text['uuid']         = uniqid();
		$apply_now_btn_text['name']         = 'rtcl-apply-btn-text';
		$apply_now_btn_text['label']        = 'Apply Now Text';
		$apply_now_btn_text['help_message'] = "Change 'Apply Now' Button text";
		$apply_now_btn_text['order']        = 9;
		$apply_now_btn_text['single_view']  = false;
		$apply_now_btn_text['archive_view'] = false;


		$description                = $availableFields['description'];
		$description['uuid']        = uniqid();
		$description['editor_type'] = 'wp_editor';

		// Contact Section.
		$company_logo                                              = $availableFields['file'];
		$company_logo['uuid']                                      = uniqid();
		$company_logo['name']                                      = 'rtcl-job-company-logo';
		$company_logo['label']                                     = 'Company Logo';
		$company_logo['validation']['allowed_file_types']['value'] = [ 'jpg|jpeg|gif|png|bmp' ];

		$company_name          = $availableFields['text'];
		$company_name['uuid']  = uniqid();
		$company_name['name']  = 'rtcl-job-company-name';
		$company_name['label'] = 'Company Name';
		$company_name['order'] = 1;

		$company_tagline          = $availableFields['text'];
		$company_tagline['uuid']  = uniqid();
		$company_tagline['name']  = 'rtcl-job-company-tagline';
		$company_tagline['label'] = 'Company Tagline';
		$company_tagline['order'] = 1;

		$location               = $availableFields['location'];
		$location['uuid']       = uniqid();
		$location['validation'] = [
			'required' => [
				'value'   => true,
				'message' => __( 'This field is required', 'classified-listing' ),
			],
		];

		$zipcode         = $availableFields['zipcode'];
		$zipcode['uuid'] = uniqid();

		$address         = $availableFields['address'];
		$address['uuid'] = uniqid();

		$phone               = $availableFields['phone'];
		$phone['uuid']       = uniqid();
		$phone['validation'] = [
			'required' => [
				'value'   => true,
				'message' => __( 'This field is required', 'classified-listing' ),
			],
		];

		$email                  = $availableFields['email'];
		$email['uuid']          = uniqid();
		$email['default_value'] = '{user.user_email}';

		$website         = $availableFields['website'];
		$website['uuid'] = uniqid();

		$social_profiles         = $availableFields['social_profiles'];
		$social_profiles['uuid'] = uniqid();

		// Gallery Section.
		$images         = $availableFields['images'];
		$images['uuid'] = uniqid();

		// Group section.
		$basicInfoSection = $contactDetailSection = $gallerySection = AvailableFields::getSectionField();

		$basicInfoSection['uuid']    = uniqid();
		$basicInfoSection['title']   = __( 'Basic Information', 'classified-listing' );
		$basicInfoSection['columns'] = [
			[
				'width'  => 100,
				'fields' => [
					$title['uuid'],
					$listingType['uuid'],
					$category['uuid'],
					$tag['uuid'],
					$pricing['uuid'],
					$job_type['uuid'],
					$experience['uuid'],
					$flexibility['uuid'],
					$job_deadline['uuid'],
					$vacancies['uuid'],
					$office_time['uuid'],
					$submission_form['uuid'],
					$external_link['uuid'],
					$apply_now_btn_text['uuid'],
					$description['uuid'],
				],
			],
		];

		$contactDetailSection['uuid']    = uniqid();
		$contactDetailSection['title']   = __( 'Contact Details', 'classified-listing' );
		$contactDetailSection['columns'] = [
			[
				'width'  => 100,
				'fields' => [
					$company_logo['uuid'],
					$company_name['uuid'],
					$company_tagline['uuid'],
					$location['uuid'],
					$zipcode['uuid'],
					$address['uuid'],
					$phone['uuid'],
					$email['uuid'],
					$website['uuid'],
					$social_profiles['uuid'],
				],
			],
		];

		$gallerySection['uuid']    = uniqid();
		$gallerySection['title']   = __( 'Gallery Images', 'classified-listing' );
		$gallerySection['columns'] = [
			[
				'width'  => 100,
				'fields' => [ $images['uuid'] ],
			],
		];

		$default = Form::query()->where( 'default', 1 )->one() ? 0 : 1;

		return [
			'title'      => __( 'Job Submit', 'classified-listing' ),
			'slug'       => 'job-submit',
			'status'     => 'publish',
			'default'    => $default,
			'created_by' => get_current_user_id(),
			'settings'   => SettingFields::get(),
			'fields'     => [
				$title['uuid']              => $title,
				$listingType['uuid']        => $listingType,
				$category['uuid']           => $category,
				$tag['uuid']                => $tag,
				$pricing['uuid']            => $pricing,
				$job_type['uuid']           => $job_type,
				$experience['uuid']         => $experience,
				$flexibility['uuid']        => $flexibility,
				$job_deadline['uuid']       => $job_deadline,
				$vacancies['uuid']          => $vacancies,
				$office_time['uuid']        => $office_time,
				$submission_form['uuid']    => $submission_form,
				$external_link['uuid']      => $external_link,
				$apply_now_btn_text['uuid'] => $apply_now_btn_text,
				$description['uuid']        => $description,
				$company_logo['uuid']       => $company_logo,
				$company_name['uuid']       => $company_name,
				$company_tagline['uuid']    => $company_tagline,
				$location['uuid']           => $location,
				$zipcode['uuid']            => $zipcode,
				$address['uuid']            => $address,
				$phone['uuid']              => $phone,
				$email['uuid']              => $email,
				$website['uuid']            => $website,
				$social_profiles['uuid']    => $social_profiles,
				$images['uuid']             => $images,
			],
			'sections'   => [
				$basicInfoSection,
				$contactDetailSection,
				$gallerySection,
			],
		];
	}
}
