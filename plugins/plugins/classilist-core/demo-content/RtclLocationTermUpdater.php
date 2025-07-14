<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.15
 */

class RtclLocationTermUpdater {

	public function __construct() {
		$this->update_rtcl_location_terms();
	}

	public function update_rtcl_location_terms(){
		$this->create_terms();
	}

	public function create_terms() {
		if ( is_admin() && is_user_logged_in() ) {
			$taxonomy = 'rtcl_location';

			// Define terms and their hierarchy
			$terms = [
				[
					'name'     => 'California',
					'id'       => 33,
					'children' => [
						[ 'name' => 'Bakersfield', 'id' => 37 ],
						[ 'name' => 'Claremont', 'id' => 41 ],
						[ 'name' => 'Downey', 'id' => 46 ],
					],
				],
				[
					'name'     => 'Kansas',
					'id'       => 36,
					'children' => [
						[ 'name' => 'Abilene', 'id' => 38 ],
						[ 'name' => 'Emporia', 'id' => 40 ],
						[ 'name' => 'Hutchinson', 'id' => 45 ],
					],
				],
				[
					'name'     => 'Louisiana',
					'id'       => 43,
					'children' => [
						[ 'name' => 'Bogalusa', 'id' => 48 ],
						[ 'name' => 'Monroe', 'id' => 52 ],
						[ 'name' => 'New Orleans', 'id' => 54 ],
					],
				],
				[
					'name'     => 'New Jersey',
					'id'       => 31,
					'children' => [
						[ 'name' => 'Bloomfield', 'id' => 32 ],
						[ 'name' => 'Cape May', 'id' => 34 ],
						[ 'name' => 'Englewood', 'id' => 39 ],
					],
				],
				[
					'name'     => 'New York',
					'id'       => 44,
					'children' => [
						[ 'name' => 'Brooklyn', 'id' => 57 ],
						[ 'name' => 'Mineola', 'id' => 55 ],
						[ 'name' => 'Port Chester', 'id' => 50 ],
					],
				],
			];

			$this->rtResetTaxonomyTerms( $taxonomy, $terms );
		}
	}

	// Main function for terms delete and create
	private function rtResetTaxonomyTerms( $taxonomy, $terms ) {
		// Ensure taxonomy exists
		if ( !taxonomy_exists( $taxonomy ) ) {
			return;
		}

		// Step 1: Remove all terms in the taxonomy
		$all_terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		] );

		if ( !is_wp_error( $all_terms ) ) {
			foreach ( $all_terms as $term ) {
				wp_delete_term( $term->term_id, $taxonomy );
			}
		} else {
			return;
		}

		$this->rtInsertTaxonomyTerms( $taxonomy, $terms );
	}

	// Terms insert function
	private function rtInsertTaxonomyTerms( $taxonomy, $terms ) {
		global $wpdb;
		if ( !empty( $terms ) ) {
			foreach ( $terms as $term_data ) {
				$term_id = $term_data['id'];
				$term_name = $term_data['name'];
				$term_slug = $term_data['slug'] ?? sanitize_title( $term_name );

				// Insert term directly into database
				$wpdb->insert(
					$wpdb->terms,
					[
						'term_id' => $term_id,
						'name'    => $term_name,
						'slug'    => $term_slug,
					],
					[ '%d', '%s', '%s' ]
				);

				$wpdb->insert(
					$wpdb->term_taxonomy,
					[
						'term_id'     => $term_id,
						'taxonomy'    => $taxonomy,
						'description' => '',
						'parent'      => $parent_id,
						'count'       => 0,
					],
					[ '%d', '%s', '%s', '%d', '%d' ]
				);

				if ( !empty( $term_data['children'] ) ) {
					$this->rtInsertTaxonomyTerms( $taxonomy, $term_data['children'] , $term_id );
				}
			}
		}
	}
}