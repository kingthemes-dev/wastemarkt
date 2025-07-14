<?php

namespace RtclJobManager\Helpers;

use Rtcl\Helpers\Breadcrumb;
use RtclJobManager\Helpers\Functions as JobFunction;

class JobBreadcrumb extends Breadcrumb {
	/**
	 * Generate breadcrumb trail.
	 *
	 * @return array of breadcrumbs
	 */
	public function generate() {

		$this->add_crumbs_job_archive();

		$this->search_trail();

		return $this->get_breadcrumb();

	}


	protected function add_crumbs_job_archive() {

		$job_archive_page = JobFunction::job_archive_page();

		if ( intval( get_option( 'page_on_front' ) ) === $job_archive_page ) {
			return;
		}

		$_name = $job_archive_page ? get_the_title( $job_archive_page ) : '';

		if ( ! $_name ) {
			$product_post_type = get_post_type_object( rtcl()->post_type );
			$_name             = $product_post_type->labels->name;
		}

		$this->add_crumb( $_name, get_post_type_archive_link( rtcl()->post_type ) );
	}
}