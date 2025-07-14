<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.15
 */

class RtclCategoriesTermUpdater {

	private $taxonomy = 'rtcl_category';

	public function __construct() {
		$this->update_rtcl_category_terms();
	}

	public function update_rtcl_category_terms(){
		$this->create_terms();
	}

	// Main function for terms delete and create
	// মূল ফাংশন যা টার্ম ডিলেট এবং তৈরি করে
	public function create_terms() {
		$this->delete_existing_terms(); // পুরাতন টার্ম ডিলেট
		$terms = $this->get_terms_data(); // নতুন টার্ম ডেটা

		$parent_term_ids = []; // প্যারেন্ট টার্মের আইডি ট্র্যাক করার জন্য

		// প্রথমে প্যারেন্ট টার্ম তৈরি
		foreach ($terms as $parent) {
			$parent_id = $this->create_or_update_term($parent['name'], $parent['slug'], $parent['id'], 0);

			if ($parent_id) {
				$parent_term_ids[$parent['id']] = $parent_id; // প্যারেন্ট আইডি সেভ করা
			} else {
				error_log("Error creating parent term: {$parent['name']}");
			}
		}

		// তারপর চাইল্ড টার্ম তৈরি
		foreach ($terms as $parent) {
			if (!empty($parent['children'])) {
				$parent_id = $parent_term_ids[$parent['id']] ?? null;

				if ($parent_id) {
					foreach ($parent['children'] as $child) {
						$this->create_or_update_term($child['name'], $child['slug'], $child['id'], $parent_id);
					}
				} else {
					error_log("Parent term ID for {$parent['name']} not found. Skipping child terms.");
				}
			}
		}
	}

	// পুরাতন টার্মগুলো মুছে ফেলার ফাংশন
	private function delete_existing_terms() {
		$terms = get_terms([
			'taxonomy' => $this->taxonomy,
			'hide_empty' => false,
		]);

		if (!empty($terms) && !is_wp_error($terms)) {
			foreach ($terms as $term) {
				wp_delete_term($term->term_id, $this->taxonomy);
				error_log("Deleted term: {$term->name} (ID: {$term->term_id})");
			}
		}
	}

	// টার্ম তৈরি বা আপডেটের ফাংশন
	private function create_or_update_term($name, $slug, $id, $parent_id) {
		$args = [
			'slug' => $slug,
			'parent' => $parent_id,
		];

		$existing_term = get_term_by('slug', $slug, $this->taxonomy);

		if (!$existing_term) {
			$result = wp_insert_term($name, $this->taxonomy, $args);

			if (!is_wp_error($result)) {
				$term_id = $result['term_id'];

				// কাস্টম টার্ম আইডি সেট করা
				global $wpdb;
				$wpdb->update(
					$wpdb->terms,
					['term_id' => $id],
					['term_id' => $term_id]
				);

				error_log("Created term: {$name} (ID: {$id})");
				return $id;
			} else {
				error_log("Error creating term {$name}: {$result->get_error_message()}");
				return false;
			}
		} else {
			error_log("Term {$name} already exists with ID {$existing_term->term_id}");
			return $existing_term->term_id;
		}
	}

	// New terms definition
	private function get_terms_data() {
		return [
			[
				'name' => 'Business & Industry',
				'slug' => 'business-industry',
				'id' => 61,
				'children' => [
					['name' => 'Industry Machinery & Tools', 'slug' => 'industry-machinery-tools', 'id' => 73],
					['name' => 'Licences, Titles & Tenders', 'slug' => 'licences-titles-tenders', 'id' => 79],
					['name' => 'Medical Equipment & Supplies', 'slug' => 'medical-equipment-supplies', 'id' => 82],
					['name' => 'Office Supplies & Stationary', 'slug' => 'office-supplies-stationary', 'id' => 91],
				],
			],
			[
				'name' => 'Cars & Vehicles',
				'slug' => 'cars-vehicles',
				'id' => 62,
				'children' => [
					['name' => 'Auto Parts & Accessories', 'slug' => 'auto-parts-accessories', 'id' => 137],
					['name' => 'Auto Services', 'slug' => 'auto-services', 'id' => 138],
					['name' => 'Boats & Water Transport', 'slug' => 'boats-water-transport', 'id' => 142],
					['name' => 'Bicycles & Three Wheelers', 'slug' => 'bicycles-and-three-wheelers', 'id' => 141],
					['name' => 'Boats & Water Transport', 'slug' => 'boats-water-transport', 'id' => 142],
					['name' => 'Cars', 'slug' => 'cars', 'id' => 146],
					['name' => 'Motorbikes & Scooters', 'slug' => 'motorbikes-scooters', 'id' => 86],
					['name' => 'Tractors & Heavy-Duty', 'slug' => 'tractors-heavy-duty', 'id' => 114],
					['name' => 'Trucks, Vans & Buses', 'slug' => 'trucks-vans-buses', 'id' => 116],
				],
			],
			[
				'name' => 'Education',
				'slug' => 'dducation',
				'id' => 64,
				'children' => [
					['name' => 'Textbooks', 'slug' => 'textbooks', 'id' => 112],
					['name' => 'Tuition', 'slug' => 'tuition', 'id' => 117],
				],
			],
		];
	}
}



