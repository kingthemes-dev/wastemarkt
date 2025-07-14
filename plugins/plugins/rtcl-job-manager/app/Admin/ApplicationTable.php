<?php

namespace RtclJobManager\Admin;

use Rtcl\Resources\Options;
use RtclJobManager\Helpers\Functions;
use Rtcl\Helpers\Functions as RtclFns;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * ApplicationTable Class
 */
class ApplicationTable extends \WP_List_Table {

	/**
	 * Class Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * If data not exist
	 *
	 * @return void
	 */
	public function no_items() {
		_e( 'No job applications found.' );
	}

	/**
	 * Row bulk action
	 *
	 * @return array|mixed
	 */
	public function get_bulk_actions() {
		$actions = [];
		foreach ( Functions::job_status() as $id => $label ) {
			$actions[ $id ] = "Mark as " . $label;
		}
		$actions['delete'] = __( 'Delete', 'rtcl-job-manager' );

		return $actions;
	}

	/**
	 * Extra tab nav
	 *
	 * @param $which
	 *
	 * @return void
	 */
	protected function extra_tablenav( $which ) {
		if ( $which == 'top' ) {
			?>

            <input type="hidden" name="page" value="job-applications"/>
            <div class="alignleft actions">

                <select name="post_per_page">
                    <option value=""><?php echo esc_html( 'Display Per Page' ); ?></option>
                    <option value="15"><?php echo esc_html( '15' ); ?></option>
                    <option value="30"><?php echo esc_html( '30' ); ?></option>
                    <option value="60"><?php echo esc_html( '60' ); ?></option>
                    <option value="100"><?php echo esc_html( '100' ); ?></option>
                    <option value="200"><?php echo esc_html( '200' ); ?></option>
                    <option value="500"><?php echo esc_html( '500' ); ?></option>
                    <option value="1000"><?php echo esc_html( '1000' ); ?></option>
                </select>

                <select name="filter_job_status" id="filter_job_status">
                    <option value=""><?php _e( 'All Status', 'textdomain' ); ?></option>
					<?php
					$status = Functions::job_status();
					foreach ( $status as $id => $val ) {
						?>
                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $_REQUEST['filter_job_status'] ?? '', $id ); ?>><?php echo esc_html( $val ); ?></option>
						<?php
					}
					?>
                </select>

				<?php $this->categories_dropdown(); ?>
				<?php //$this->job_dropdown(); ?>
				<?php $this->filter_by_jobs(); ?>

                <input type="text" name="s" placeholder="<?php esc_attr_e( 'Search by Name', 'textdomain' ); ?>"
                       value="<?php echo esc_attr( $_REQUEST['s'] ?? '' ); ?>"/>
				<?php
				submit_button( __( 'Submit' ), '', 'search_action', false );
				?>
            </div>
			<?php
		}
	}


	public function filter_by_jobs() {
		$job_name = '';
		$job_id   = '';

		if ( ! empty( $_REQUEST['_rtcl_job'] ) ) {
			$job_id   = absint( $_REQUEST['_rtcl_job'] );
			$category = get_post( $job_id );
			$job_name = $category->post_title;
		}
		?>
        <select class="rtcl-ajax-select" name="_rtcl_job"
                data-type="listing"
                data-placeholder="<?php esc_attr_e( 'Filter by Job', 'classified-listing' ); ?>"
                data-action="rtcl_inline_job_search_autocomplete"
                data-allow_clear="true">
            <option value="<?php echo esc_attr( $job_id ); ?>" selected="selected">
				<?php RtclFns::print_html( $job_name, true ); ?>
            <option>
        </select>
		<?php
	}


	protected function job_dropdown() {
		$args = [
			'post_type'      => 'rtcl_listing',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => 'ad_type',
					'value'   => 'job',
					'compare' => '==',
				],
			],
		];

		$query        = get_posts( $args );
		$selected_job = ! empty( $_REQUEST['rtcl_job'] ) ? sanitize_text_field( $_REQUEST['rtcl_job'] ) : '';
		echo "<select name='rtcl_job'>";
		echo '<option>All Jobs</option>';
		foreach ( $query as $post ) {
			printf( "<option value='%s' %s>%s - #%s</option>", $post, selected( $selected_job, $post ), get_the_title( $post ), $post );
		}
		echo '</select>';
	}

	protected function categories_dropdown() {

		$term_ids = get_terms(
			[
				'taxonomy'   => 'rtcl_category',
				'meta_key'   => '_rtcl_types',
				'meta_value' => 'job',
				'fields'     => 'ids',
				'hide_empty' => false,
				'parent'     => 0,
			]
		);

		$dropdown_options = [
			'show_option_all' => get_taxonomy( 'rtcl_category' )->labels->all_items,
			'hide_empty'      => 0,
			'hierarchical'    => 1,
			'show_count'      => 0,
			'orderby'         => 'name',
			'selected'        => $_REQUEST['filter_cat'] ?? '0',
			'taxonomy'        => 'rtcl_category',
			'name'            => 'filter_cat',
			'include'         => $term_ids, // Include only the filtered term IDs
		];

		wp_dropdown_categories( $dropdown_options );
	}

	/**
	 * Column List
	 *
	 * @return array
	 */
	public function get_columns() {

		if ( ! empty( $_REQUEST['order'] ) && 'asc' == $_REQUEST['order'] ) {
			$order = 'desc';
		} else {
			$order = 'asc';
		}
		$columns = [
			'cb'          => '<input type="checkbox" />',
			'id'          => __( 'ID', 'rtcl-job-manager' ),
			'name'        => '<div style="display: flex;gap:8px"><a style="display: flex" href="?page=job-applications&orderby=first_name&order=' . $order . '"><span>First Name</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span></a> <a style="display: flex;" href="?page=job-applications&orderby=last_name&order=' . $order . '"><span>Last Name</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span></a></div>',
			'listing_id'  => __( 'Job Title', 'rtcl-job-manager' ),
			'listing_cat' => __( 'Category', 'rtcl-job-manager' ),
			'address'     => __( 'Address', 'rtcl-job-manager' ),
			'status'      => __( 'Status', 'rtcl-job-manager' ),
			'created_at'  => __( 'Application Date', 'rtcl-job-manager' ),
		];

		return $columns;
	}

	/**
	 * Make shortable column
	 *
	 * @return array[]
	 */
	public function get_sortable_columns() {
		$sortable_columns = [
			'id'          => [ 'id', true ], // Default short item.
			'listing_id'  => [ 'listing_id', false ],
			'listing_cat' => [ 'listing_cat', false ],
			'status'      => [ 'status', false ],
			'created_at'  => [ 'created_at', false ],
		];

		return $sortable_columns;
	}

	/**
	 * Table Column Info
	 *
	 * @param $item
	 * @param $column_name
	 *
	 * @return string|void
	 */
	public function column_default( $item, $column_name ) {

		$data                   = json_decode( $item['application_data'], true );
		$data['user_id']        = $item['user_id'];
		$data['job_id']         = $item['listing_id'];
		$data['status']         = $item['status'];
		$data['application_id'] = $item['id'];
		$data['first_name']     = $item['first_name'];
		$data['last_name']      = $item['last_name'];

		$listing = rtcl()->factory->get_listing( $item['listing_id'] );

		switch ( $column_name ) {
			case 'id':
				return sprintf(
					"<strong><a class='rtcl-application-title app-id' data-info='%s' href='#' data-id='%s'>%s</a></strong>",
					htmlspecialchars( wp_json_encode( $data ) ),
					$item['id'],
					$item[ $column_name ],
				);
			case 'name':
				$_action = [
					'archived',
					'Mark as Archive',
				];
				$termId  = $item['id'];
				if ( ! empty( $_REQUEST['filter_job_status'] ) && 'archived' == $_REQUEST['filter_job_status'] ) {
					$_action = [
						'delete',
						'Delete',
					];
					$termId  .= '&filter_job_status=archived';
				}
				$uid   = $item['user_id'] ?? '';
				$jobid = $item['listing_id'] ?? '';

				$actions = [
					'delete' => sprintf( '<a href="?page=%s&row_action=%s&item_id=%s&uid=%s&jobid=%s">%s</a>', $_REQUEST['page'], $_action[0], $termId, $uid, $jobid, $_action[1] ),
					'view'   => sprintf( '<a class="rtcl-application-title" href="#" data-info="%s" data-id="%s" > View</a>', htmlspecialchars( wp_json_encode( $data ) ), $item['id'] ),
				];

				$name = trim( sprintf( ' % s % s', $item['first_name'] ?? '', $item['last_name'] ?? '' ) );

				return sprintf(
					"<strong><a class='rtcl-application-title row-title' data-info='%s' href='#' data-id='%s'>%s</a></strong> %s",
					htmlspecialchars( wp_json_encode( $data ) ),
					$item['id'],
					$name,
					$this->row_actions( $actions )
				);
			case 'address':
				return esc_html( $data['address'] ?? '...' );
			case 'listing_id':
				if ( $listing ) {
					return sprintf( "<a href='%s'>%s </a> [#%s]", $listing->get_the_permalink(), $listing->get_the_title(), $listing->get_id() );
				} else {
					return __( 'This job is no longer available.', 'rtcl-job-manager' );
				}
			case 'listing_cat':
				if ( $listing ) {
					$listing_cat = $listing->get_parent_category();

					return sprintf( "<a href='%s'>%s</a>", get_term_link( $listing_cat->term_id ), $listing_cat->name );
				} else {
					return '-';
				}
			case 'user_id':
				if ( $item[ $column_name ] != 0 ) {
					$user = get_userdata( $item[ $column_name ] );

					return sprintf( "<a href='%s'>%s %s</a>", get_edit_profile_url( $item[ $column_name ] ), $user->first_name ?? '', $user->last_name ?? '' );
				} else {
					return '-';
				}
			case 'status':
				return sprintf( "<span class='rtclJobStatusLabel %s' data-id='%s'>%s</span>", esc_attr( $item[ $column_name ] ), $item['id'], esc_html( $item[ $column_name ] ) );
			default:
				return esc_html( $item[ $column_name ] );
		}
	}

	/**
	 * Column Checkbox
	 *
	 * @param $item
	 *
	 * @return string|void
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="job_application[]" value="%s" />',
			$item['id']
		);
	}

	public function prepare_items() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rtcl_job_applications';

		if ( ! empty( $_REQUEST['post_per_page'] ) ) {
			update_user_meta( get_current_user_id(), 'app_per_page', sanitize_text_field( $_REQUEST['post_per_page'] ) );
		}

		// Bulk Action.
		$this->process_bulk_delete();
		$this->process_bulk_status();

		$ppp = get_user_meta( get_current_user_id(), 'app_per_page', true );

		$per_page     = (int) $ppp ?: 15;
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$post_table  = $wpdb->prefix . 'posts';
		$terms_table = $wpdb->prefix . 'terms';
		// Fetch data.
		// $query = "SELECT * FROM $table_name WHERE 1=1"; // Use a base condition

		$query = "SELECT app.*, post.post_title, term.name
        FROM $table_name as app
        LEFT JOIN $post_table as post ON app.listing_id = post.ID
        LEFT JOIN $terms_table as term ON app.listing_cat = term.term_id
        WHERE 1=1
        ";

		if ( ! empty( $_REQUEST['filter_job_status'] ) ) {
			$query .= " AND app.status = '" . esc_sql( $_REQUEST['filter_job_status'] ) . "'";
		} else {
			$query .= " AND app.status != 'archived'";
		}

		if ( ! empty( $_REQUEST['filter_cat'] ) ) {
			$query .= " AND app.listing_cat = '" . esc_sql( $_REQUEST['filter_cat'] ) . "'";
		}

		if ( ! empty( $_REQUEST['_rtcl_job'] ) ) {
			$query .= " AND app.listing_id = '" . esc_sql( $_REQUEST['_rtcl_job'] ) . "'";
		}
		// Handle searching
		if ( ! empty( $_REQUEST['s'] ) ) {
			// $search = esc_sql( $_REQUEST['s'] );
			// $query .= " AND (app.first_name LIKE '%$search%' OR app.last_name LIKE '%$search%')";

			$search       = esc_sql( $_REQUEST['s'] );
			$search_terms = explode( ' ', $search ); // Split the search into individual words

			$search_query = [];
			foreach ( $search_terms as $term ) {
				$term = trim( $term );
				if ( ! empty( $term ) ) {
					// Add a condition for each word to search in both first_name and last_name
					$search_query[] = "(app.first_name LIKE '%$term%' OR app.last_name LIKE '%$term%')";
				}
			}

			if ( ! empty( $search_query ) ) {
				// Join the conditions with 'AND' to require all words to be matched
				$query .= ' AND (' . implode( ' OR ', $search_query ) . ')';
			}
		}

		$orderby = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
		$order   = ! empty( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], [
			'asc',
			'desc'
		] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'desc';

		if ( 'listing_id' === $orderby ) {
			$query .= ' ORDER BY post.post_title ' . esc_sql( $order );
		} elseif ( 'listing_cat' === $orderby ) {
			$query .= ' ORDER BY term.name ' . esc_sql( $order );
		} else {
			$query .= ' ORDER BY app.' . esc_sql( $orderby ) . ' ' . esc_sql( $order );
		}

		$query .= ' LIMIT %d OFFSET %d';

		$query_prepare = $wpdb->prepare( $query, $per_page, $offset );

		$data = $wpdb->get_results( $query_prepare, ARRAY_A );

		// Count total records.
		if ( ! empty( $_REQUEST['filter_job_status'] ) ) {
			$job_status  = $_REQUEST['filter_job_status'];
			$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = '$job_status'" );
		} else {
			$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status != 'archived'" );
		}

		// Prepare table.
		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];
		$this->items           = $data;

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			]
		);
	}

	public function process_bulk_delete() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'rtcl_job_applications';
		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_REQUEST['job_application'] ) ? $_REQUEST['job_application'] : [];
			if ( ! empty( $ids ) && is_array( $ids ) ) {
				foreach ( $ids as $item_id ) {
					$this->remove_job_from_users( $item_id );
				}
				$ids = implode( ',', array_map( 'absint', $ids ) );
				$wpdb->query( "DELETE FROM $table_name WHERE id IN($ids)" );
			}
		}

		$row_action = ! empty( $_REQUEST['row_action'] ) ? $_REQUEST['row_action'] : '';
		$item_id    = isset( $_REQUEST['item_id'] ) ? $_REQUEST['item_id'] : '';
		if ( 'archived' == $row_action && $item_id ) {
			$wpdb->update(
				$table_name,
				[ 'status' => 'archived' ],
				[ 'id' => $item_id ],
				'%s',
				'%d'
			);
		}
		if ( 'delete' == $row_action && $item_id ) {
			$this->remove_job_from_users( $item_id );
			$wpdb->query( "DELETE FROM $table_name WHERE id = '$item_id'" );
		}
	}

	public function remove_job_from_users( $item_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rtcl_job_applications';
		$query      = $wpdb->prepare( "SELECT user_id, listing_id FROM $table_name WHERE id = %d", $item_id );
		$result     = $wpdb->get_row( $query, ARRAY_A );

		if ( ! empty( $result['user_id'] ) ) {
			$user_id      = $result['user_id'];
			$jobid        = $result['listing_id'] ?? '';
			$existing_job = get_user_meta( $user_id, 'rtcl_applied_job', true );
			unset( $existing_job[ $jobid ] );
			update_user_meta( $user_id, 'rtcl_applied_job', $existing_job );
		}
	}

	public function process_bulk_status() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rtcl_job_applications';

		$status = array_keys( Functions::job_status() );

		if ( in_array( $this->current_action(), $status ) ) {
			$job_status = $this->current_action();

			$ids = isset( $_REQUEST['job_application'] ) ? $_REQUEST['job_application'] : [];
			if ( ! empty( $ids ) && is_array( $ids ) ) {
				foreach ( $ids as $id ) {
					$result = $wpdb->update(
						$table_name,
						[ 'status' => $job_status ],
						[ 'id' => $id ],
						[ '%s' ],
						[ '%d' ]
					);
				}
			}
		}
	}
}
