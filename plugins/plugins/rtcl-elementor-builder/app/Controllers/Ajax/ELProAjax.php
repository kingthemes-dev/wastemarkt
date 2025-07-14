<?php

/**
 * ELProAjax Class
 *
 * Ajax functionality.
 *
 * @package  RTCL_Elementor_Builder
 * @since    2.0.10
 */

namespace RtclElb\Controllers\Ajax;

use Rtcl\Helpers\Functions;
use RtclPro\Helpers\Fns;
use RtclElb\Helpers\Fns as ElFns;
use RtclElb\Traits\ELTempleateBuilderTraits;


/**
 * Elementor builder ajax functionality
 */
class ELProAjax {


	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Initialize ajax hooks
	 *
	 * @return void
	 */
	public static function init() {
		// Elementor hooks.
		add_action( 'wp_ajax_rtcl_el_templeate_builder', [ __CLASS__, 'rtcl_el_templeate_builder' ] );
		add_action( 'wp_ajax_rtcl_el_create_templeate', [ __CLASS__, 'rtcl_el_create_templeate' ] );
		add_action( 'wp_ajax_rtcl_el_default_template', [ __CLASS__, 'rtcl_el_default_template' ] );
		// Elementor hooks end.
	}
	/**
	 * Elementor template builder
	 *
	 * @return void
	 */
	public static function rtcl_el_templeate_builder() {
		$title = '<h2>' . esc_html__( 'Template Settings', 'rtcl-elementor-builder' ) . '</h2>';
		if ( ! Functions::verify_nonce() ) {
			$return = [
				'success' => false,
				'title'   => $title,
				'content' => esc_html__( 'Session Expired...', 'rtcl-elementor-builder' ),
			];
			wp_send_json( $return );
		}
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : null;

		$template_type    = null;
		$template_default = null;
		$url              = null;
		$tmp_title        = '';
		$edit_with        = did_action( 'elementor/loaded' ) ? 'elementor' : 'gutenberg';
		$editor_btn_text  = '';
		$fb_form_id       = '';
		if ( $post_id ) {
			$tmp_title        = get_the_title( $post_id );
			$template_type    = get_post_meta( $post_id, self::template_type_meta_key(), true );
			$template_default = absint( self::builder_page_id( $template_type, $post_id ) );
			$edit_with        = self::page_edit_with( $post_id );
			$editor_btn_text  = self::page_edit_btn_text( $edit_with );
			$url              = add_query_arg(
				[
					'post'   => $post_id,
					'action' => $edit_with == 'elementor' ? 'elementor' : 'edit',
				],
				admin_url( 'post.php' )
			);
			// _fb_form_id
			$fb_form_id = get_post_meta( $post_id, ElFns::template_fb_form_id_key(), true );
		}
		ob_start();
		?>

		<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=rtcl_builder' ) ); ?>" autocomplete="off">
			<div class="rtcl-tb-modal-wrapper ">
				<div class="rtcl-template-name rtcl-tb-field-wraper">
					<label for="rtcl_tb_template_name"> <?php esc_html_e( 'Template name', 'rtcl-elementor-builder' ); ?></label>
					<input required class="rtcl-field" type="text" id="rtcl_tb_template_name" name="rtcl_tb_template_name" placeholder="<?php esc_html_e( 'Template name', 'rtcl-elementor-builder' ); ?>" value="<?php echo esc_attr( $tmp_title ); ?>" autocomplete="off">
					<span class="message" style="display: none; color:red"><?php esc_html_e( 'This field is required', 'rtcl-elementor-builder' ); ?></span>
				</div>
				<div class="rtcl-template-type rtcl-tb-field-wraper">
					<label for="rtcl_tb_template_type"><?php esc_html_e( 'Template Type', 'rtcl-elementor-builder' ); ?></label>
					<select class="rtcl-field" id="rtcl_tb_template_type" name="rtcl_tb_template_type">
						<?php
						$builder_page_types = self::builder_page_types();
						foreach ( $builder_page_types as $key => $value ) {
							?>
							<option <?php echo $key === $template_type ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $value ); ?> </option>
						<?php } ?>
					</select>
				</div>
				<?php
					$fbFormList = [
						'' => esc_html__( '--Select Form--', 'rtcl-elementor-builder' ),
					] + ElFns::get_all_fb_form_as_list();
					?>
					<div class="rtcl-template-listing-form rtcl-tb-field-wraper">
						<label for="rtcl_tb_template_edit_with"><?php esc_html_e( 'Select Listing Form', 'rtcl-elementor-builder' ); ?></label>
						<select class="rtcl-field" id="rtcl_tb_template_select_listing_form" name="rtcl_tb_template_select_listing_form" required>
							<?php foreach ( $fbFormList as $key => $value ) { ?>
								<option <?php echo absint( $key ) === absint( $fb_form_id ) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $value ); ?> </option>
							<?php } ?>
						</select>
					</div>
				
				<div class="rtcl-template-edit-with rtcl-tb-field-wraper">
					<label for="rtcl_tb_template_edit_with"><?php esc_html_e( 'Editor Type', 'rtcl-elementor-builder' ); ?></label>
					<select class="rtcl-field" id="rtcl_tb_template_edit_with" name="rtcl_tb_template_edit_with" required>
						<?php if ( did_action( 'elementor/loaded' ) ) : ?>
							<option value="elementor" <?php echo 'elementor' === $edit_with ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Elementor', 'rtcl-elementor-builder' ); ?></option>
						<?php endif; ?>
						<option value="gutenberg" <?php echo 'gutenberg' === $edit_with ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Gutenberg', 'rtcl-elementor-builder' ); ?></option>
					</select>
				</div>

				<div class="rtcl-template-setdefaults">
					<input type="checkbox" id="default_template" class="rtcl-field" name="default_template" value="default_template" <?php echo ( $post_id && absint( $post_id ) === absint( $template_default ) ) ? 'checked' : ''; ?>>
					<label for="default_template"> <?php esc_html_e( 'Set Default Template', 'rtcl-elementor-builder' ); ?></label><br>
				</div>
				<input type="hidden" id="page_id" name="page_id" value="<?php echo esc_attr( $post_id ); ?>">

				<div class="rtcl-template-footer">
					<div class="rtcl-tb-button-wrapper save-button">
						<button <?php echo $post_id ? esc_attr( 'disabled' ) : ''; ?> type="submit" id="rtcl_tb_button"> <?php esc_html_e( 'Save', 'rtcl-elementor-builder' ); ?></button>
					</div>

					<div class="rtcl-tb-button-wrapper rtcl-tb-edit-button-wrapper">
						<a href="<?php echo esc_url( $url ); ?>" class="btn"> <?php esc_html_e( $editor_btn_text, 'rtcl-elementor-builder' ); ?> </a>
					</div>

				</div>
			</div>
		</form>

		<?php
		$content = ob_get_clean();
		$return  = [
			'success' => true,
			'title'   => $title,
			'content' => $content,
		];
		wp_send_json( $return );
		wp_die();
	}

	/**
	 * Elementor Create Templeate
	 *
	 * @return void
	 */
	public static function rtcl_el_create_templeate() {
		$page_type        = isset( $_POST['page_type'] ) ? sanitize_text_field( wp_unslash( $_POST['page_type'] ) ) : null;
		$page_id          = isset( $_POST['page_id'] ) ? absint( wp_unslash( $_POST['page_id'] ) ) : null;
		$page_name        = isset( $_POST['page_name'] ) ? sanitize_text_field( wp_unslash( $_POST['page_name'] ) ) : null;
		$edit_with        = isset( $_POST['template_edit_with'] ) ? sanitize_text_field( wp_unslash( $_POST['template_edit_with'] ) ) : null;
		$default_template = isset( $_POST['default_template'] ) ? sanitize_text_field( wp_unslash( $_POST['default_template'] ) ) : null;
		$fb_form_id       = isset( $_POST['listing_form'] ) ? sanitize_text_field( wp_unslash( $_POST['listing_form'] ) ) : null;
		$url              = '#';
		$editor_btn_text  = self::page_edit_btn_text( $edit_with );
		if ( ! Functions::verify_nonce() || ! $page_type ) {
			$return = [
				'success' => false,
				'post_id' => $page_id,
			];
			wp_send_json( $return );
		}
		$option_name = self::option_name( $page_type );
		$post_data   = [
			'ID'         => $page_id,
			'post_title' => $page_name,
			'meta_input' => [
				self::template_type_meta_key() => $page_type,
			],
		];
		// for gutenberg.
		if ( 'elementor' == $edit_with ) {
			$post_data['meta_input']['_elementor_edit_mode'] = 'builder';
		} elseif ( 'gutenberg' == $edit_with ) {
			$post_data['meta_input']['_elementor_edit_mode'] = '';
		}

		if ( $page_id ) {
			$page_id  = wp_update_post( $post_data );
			$new_page = false;
		} else {
			unset( $post_data['ID'] );
			$post_data['post_type']   = self::$post_type_tb;
			$post_data['post_status'] = 'publish';
			$page_id                  = wp_insert_post( $post_data );
			$new_page                 = true;
			if ( 'elementor' == $edit_with ) {
				update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
			}
		}
		if ( 'single' === $page_type ) {
			update_post_meta( $page_id, ElFns::template_fb_form_id_key(), $fb_form_id );
			if ( $fb_form_id ) {
				$option_name = self::option_name( $page_type, $fb_form_id );
				$cache_key = 'rtcl_last_post_id' . ( $fb_form_id ? '_' . $fb_form_id : null );
				delete_transient( $cache_key );
			}
		}

		if ( $page_id ) {
			if ( 'default_template' === $default_template ) {
				update_option( $option_name, $page_id );
			} else {
				if ( ! get_option( $option_name ) ) {  // Ensures the existing default builder remains unchanged.
					update_option( $option_name, '' );
				}
			}

			$url = add_query_arg(
				[
					'post'   => $page_id,
					'action' => $edit_with == 'elementor' ? 'elementor' : 'edit',
				],
				admin_url( 'post.php' )
			);
		}

		$return = [
			'success'         => true,
			'post_id'         => $page_id,
			'post_edit_url'   => $url,
			'editor_btn_text' => $editor_btn_text,
			'new_page'        => $new_page,
		];
		wp_send_json( $return );
		wp_die();
	}
	/**
	 * Elementor Create Templeate
	 *
	 * @return void
	 */
	public static function rtcl_el_default_template() {
		$page_type = isset( $_POST['template_type'] ) ? sanitize_text_field( wp_unslash( $_POST['template_type'] ) ) : null;
		$page_id   = isset( $_POST['page_id'] ) ? absint( wp_unslash( $_POST['page_id'] ) ) : null;
		$fb_id     = isset( $_POST['fb_id'] ) ? absint( wp_unslash( $_POST['fb_id'] ) ) : null;
		if ( ! Functions::verify_nonce() || ! $page_type ) {
			$return = [
				'success'   => false,
				'post_id'   => $page_id,
				'page_type' => $page_type,
			];
			wp_send_json( $return );
		}
		$option_name = self::option_name( $page_type );
		if ( 'single' === $page_type && $fb_id ) {
			$option_name = self::option_name( $page_type, $fb_id );
		}

		update_option( $option_name, $page_id );

		$return = [
			'success'   => true,
			'post_id'   => $page_id,
			'page_type' => $page_type,
		];
		wp_send_json( $return );
		wp_die();
	}
}
