<?php defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' ); ?>
<?php if ( ! isset( $view ) ) {
	return;
} ?>
<?php if ( HMWP_Classes_Tools::getOption( 'hmwp_mode' ) <> 'default' && HMWP_Classes_Tools::isCachePlugin() ) { ?>
    <div class="card col-sm-12 m-0 mb-2 p-0 rounded-0">
        <div class="card-body f-gray-dark text-center">
            <h4 class="card-title"><?php echo esc_html__( 'Change Paths in Cached Files', 'hide-my-wp' ); ?></h4>
            <div class="border-top mt-2 pt-2"></div>
            <div class="col-sm-12 row mb-1 ml-1 p-2">

                <div class="col-sm-12 my-2 p-0 text-center">
                    <form id="hmwp_savecachepath" <?php if ( HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache' ) ) { ?>class="ajax_submit"<?php } ?> method="POST">
						<?php wp_nonce_field( 'hmwp_savecachepath', 'hmwp_nonce' ) ?>
                        <input type="hidden" name="action" value="hmwp_savecachepath"/>
                        <div class="checker text-center">
                            <div class="col-sm-12 p-0 py-2 switch switch-sm">

                                <input type="hidden" name="hmwp_change_in_cache" value="0"/>
                                <input type="checkbox" id="hmwp_change_in_cache" name="hmwp_change_in_cache" onchange="jQuery('form#hmwp_savecachepath').submit()" class="switch nopopup" <?php echo( HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache' ) ? 'checked="checked"' : '' ) ?> value="1"/>
                                <label for="hmwp_change_in_cache">
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-paths-in-cached-files/' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                </label>
                            </div>
                        </div>
						<?php if ( HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache' ) ) { ?>

                            <div id="hmwp_change_in_cache_directory" class="col-sm-12 p-0 py-2 input-group input-group-sm" <?php echo( ! HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache_directory' ) ? 'style="display:none"' : '' ) ?>>
                                <div class="col-sm-12 text-black-50 m-0 p-2"><?php echo esc_html__( 'Custom Cache Directory', 'hide-my-wp' ); ?>:</div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><?php echo esc_html('/' . HMWP_Classes_Tools::getOption( 'hmwp_wp-content_url' ) . '/') ?></span>
                                </div>
                                <input type="text" name="hmwp_change_in_cache_directory" class="form-control nopopup" value="<?php echo esc_attr( HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache_directory' ) <> '' ? HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache_directory' ) : '' ) ?>" placeholder="<?php echo 'cache' ?>"/>
                            </div>
							<?php if ( ! HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache_directory' ) ) { ?>
                                <button type="button" class="btn btn-sm btn-link" onclick="jQuery('#hmwp_change_in_cache_directory').show();jQuery(this).hide();"><?php echo esc_html__( 'Set Custom Cache Directory', 'hide-my-wp' ); ?></button>
							<?php } ?>
						<?php } ?>
                    </form>

                    <div class="text-black-50 mt-2"><?php echo esc_html__( 'Change the WordPress common paths in the cached files.', 'hide-my-wp' ); ?></div>
                    <div class="my-3 text-info"><?php echo esc_html__( 'Note! The plugin will use WP cron to change the paths in background once the cache files are created.', 'hide-my-wp' ) ?> </div>
					<?php if ( HMWP_Classes_Tools::getOption( 'hmwp_change_in_cache' ) ) { ?>
                        <form method="POST">
							<?php wp_nonce_field( 'hmwp_changepathsincache', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_changepathsincache"/>
                            <button type="submit" class="btn btn-sidebar d-inline-block"><?php echo esc_html__( 'Change Paths Now', 'hide-my-wp' ); ?></button>
                        </form>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div id="hmwp_changepathsincache" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><?php echo esc_html__( 'Ghost Mode', 'hide-my-wp' ) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="my-3"><?php echo esc_html__( 'Click to run the process to change the paths in the cache files.', 'hide-my-wp' ) ?> </div>
                <div class="my-3 text-info"><?php echo esc_html__( 'Note! The plugin will use WP cron to change the paths in background once the cache files are created.', 'hide-my-wp' ) ?> </div>
                <form method="POST">
					<?php wp_nonce_field( 'hmwp_changepathsincache', 'hmwp_nonce' ) ?>
                    <input type="hidden" name="action" value="hmwp_changepathsincache"/>
                    <button type="submit" class="btn btn-success d-inline-block ml-2"><?php echo esc_html__( 'Change Paths', 'hide-my-wp' ); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
