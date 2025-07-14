<?php
/**
 * @author     RadiusTheme
 *
 * @version    1.0.0
 *
 * @var object  $field
 * @var string $show_icon
 * @var string $sectionTitle
 * @var array $instance;
 */

use Rtcl\Helpers\Functions;

?>
<?php
if ( ! is_array( $field['value'] ) || empty( $field['value'] ) ) {
	return;
}
?>
<div class="rtcl-fb-element rtcl-fb-<?php echo esc_attr( $field['element'] ); ?>">
	<?php if ( ! empty( $sectionTitle ) ) { ?>
		<div class="rtcl-icon-label-wrapper rtcl-repeater-main-heading-icon-label">
			<?php if ( $show_icon && ! empty( $instance['repeater_main_title_show_icon'] ) && ! empty( $field['icon'] ) ) { ?>
				<div class="rtcl-field-icon"><i class="<?php echo esc_attr( $field['icon']['class'] ); ?>"></i></div>
			<?php } ?>
			<h3 class="rtcl-repeater-main-heading"><?php echo esc_html( $sectionTitle ); ?></h3>
		</div>
	<?php } ?>
	<div class="rtcl-fb-repeater-fields-content">
		<?php
		foreach ( $field['value'] as $key => $rfield ) {

			if ( 'list' === ( $instance['repeater_show_list_item'] ?? 'all' ) ) {
				if ( 'checkbox' !== $rfield['element'] ) {
					continue;
				}
			}
			if ( 'file' === ( $instance['repeater_show_list_item'] ?? 'all' ) ) {
				if ( 'file' !== $rfield['element'] ) {
					continue;
				}
			}
			if ( 'others' === ( $instance['repeater_show_list_item'] ?? 'all' ) ) {
				if ( in_array( $rfield['element'], [ 'file', 'checkbox' ], true ) ) {
					continue;
				}
			}

			$template = 'single/FB/';
			switch ( $rfield['element'] ) {
				case 'file':
				case 'checkbox':
					$template .= $rfield['element'];
					break;
				default:
					$template .= 'default';
			}
			$data = [
				'field'                 => $rfield,
				'instance'              => $instance,
				'show_icon'             => 'yes' === ( $instance['show_icon'] ?? '' ),
				'default_template_path' => rtclElb()->get_plugin_template_path(),
			];
			$data = apply_filters( 'rtcl_listing_fb_repeater_data', $data );
			Functions::get_template( $template, $data, '', $data['default_template_path'] );
		}
		?>
	</div>
</div>

