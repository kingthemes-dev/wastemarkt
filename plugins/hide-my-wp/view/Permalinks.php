<?php defined('ABSPATH') || die('Cheatin\' uh?'); ?>
<?php if(!isset($view)) return;?>
<div id="hmwp_wrap" class="d-flex flex-row p-0 my-3">
    <?php echo $view->getAdminTabs(HMWP_Classes_Tools::getValue('page', 'hmwp_permalinks')); ?>
    <style>#hmwp_wrap .hmwp_nav .hmwp_nav_item:nth-child(n+3){display: none}</style>
    <div class="hmwp_row d-flex flex-row p-0 m-0">
        <?php do_action('hmwp_notices'); ?>
        <div class="hmwp_col flex-grow-1 p-0 pr-2 mr-2 mb-3">
            <?php
            // Check the frontend new paths and login
            $view->show('blocks/FrontendLoginCheck');

            // Download the new paths once they are confirmed
            // Update: disable the download of custom login on confirmation
            if( false && HMWP_Classes_Tools::getOption('download_settings') ) { ?>
                <form id="hmwp_download_settings" class="ajax_submit" method="POST">
                    <?php wp_nonce_field('hmwp_download_settings', 'hmwp_nonce') ?>
                    <input type="hidden" name="action" value="hmwp_download_settings"/>
                </form>
                <script>setTimeout(function(){jQuery('#hmwp_download_settings').submit();},1000)</script>
            <?php }?>

            <form method="POST">
                <?php wp_nonce_field('hmwp_settings', 'hmwp_nonce'); ?>
                <input type="hidden" name="action" value="hmwp_settings"/>
                <input type="hidden" name="hmwp_mode" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_mode')) ?>"/>

                <?php do_action('hmwp_form_notices'); ?>

                <?php do_action('hmwp_change_paths_form_beginning') ?>

                <div id="level" class="col-sm-12 p-0 m-0 tab-panel tab-panel-first border-0">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Levels of security', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/wp-ghost-settings-best-practice/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body p-2 text-center">
                            <noscript>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="default_mode" name="hmwp_mode" value="default" class="custom-control-input" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'default') ? 'checked="checked"' : '') ?>>
                                    <label class="custom-control-label" for="default_mode"><?php echo esc_html__("Deactivated", 'hide-my-wp') ?></label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="lite_mode" name="hmwp_mode" value="lite" class="custom-control-input" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'lite') ? 'checked="checked"' : '') ?>>
                                    <label class="custom-control-label" for="lite_mode"><?php echo esc_html__("Safe mode", 'hide-my-wp') ?></label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="ninja_mode" name="hmwp_mode" value="ninja" class="custom-control-input" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'ninja') ? 'checked="checked"' : '') ?>>
                                    <label class="custom-control-label" for="ninja_mode"><?php echo esc_html__("Ghost mode", 'hide-my-wp') ?></label>
                                </div>
                                <style>.group_autoload{display: none !important;}</style>
                                <style>#hmwp_wrap .hmwp_nav .hmwp_nav_item:nth-child(n+3){display: block}#hmwp_wrap .tab-panel:not(.tab-panel-first){display: block}</style>
                            </noscript>
                            <div class="group_autoload d-flex justify-content-center btn-group btn-group-lg mt-3" role="group" >
                                <button type="button" class="btn btn-outline-info default_autoload m-1 py-4 px-4 <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'default') ? 'active' : '') ?>"><?php echo esc_html__("Deactivated", 'hide-my-wp') ?></button>
                                <button type="button" class="btn btn-outline-info lite_autoload m-1 py-4 px-4 hmwp_modal <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'lite') ? 'active' : '') ?>" onclick="jQuery('#hmwp_safe_mode_modal').modal('show')"><?php echo esc_html__("Safe mode", 'hide-my-wp') ?></button>
                                <button type="button" class="btn btn-outline-info ninja_autoload m-1 py-4 px-4 hmwp_modal <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'ninja') ? 'active' : '') ?>"  onclick="jQuery('#hmwp_ghost_mode_modal').modal('show')"><?php echo esc_html__("Ghost mode", 'hide-my-wp') ?></button>
                            </div>

                            <script>
                                (function ($) {

                                    $(document).ready(function () {

                                        $(".default_autoload").on('click', function () {
                                            $('input[name=hmwp_mode]').val('default');
                                            $('.group_autoload button').removeClass('active');
                                            <?php
                                            foreach (HMWP_Classes_Tools::$default as $name => $value) {
                                                if (is_string($value) && $value <> "0" && $value <> "1") {
                                                    echo '$("input[type=text][name=' . esc_attr($name) . ']").val("' . esc_attr(str_replace('"', '\\"', $value)) . '");' . "\n";
                                                } elseif ($value == "0" || $value == "1") {
                                                    echo '$("input[name=' . esc_attr($name) . ']").prop("checked", ' . (int)$value . '); $("input[name=' . esc_attr($name) . ']").trigger("change");';
	                                                echo '$("input[type=hidden][name=' . esc_attr($name) . ']").val("' . esc_attr(str_replace('"', '\\"', $value)) . '");' . "\n";
                                                }
                                            }
                                            ?>
                                            $('input[name=hmwp_admin_url]').trigger('keyup');
                                            $('.hmwp_nav_item').not(':first').hide();

                                            $('.hmwp_ghost_mode_modal').show();
                                            $('.hmwp_emulate_cms').hide();
                                            $('.hmwp_disable_url').hide();
                                            $('.hmwp_presets').show();
                                            $('select[name="hmwp_emulate_cms"] option[value="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_emulate_cms')) ?>"]').prop('selected', 'selected');
                                        });

                                        $(".safe_confirmation").on('click', function () {
                                            $('input[name=hmwp_mode]').val('lite');
                                            <?php
                                            $lite = @array_merge(HMWP_Classes_Tools::$default, HMWP_Classes_Tools::$lite);
                                            foreach ($lite as $name => $value) {
                                                if (is_string($value) && $value <> "0" && $value <> "1") {
                                                    echo '$("input[type=text][name=' . esc_attr($name) . ']").val("' . esc_attr(str_replace('"', '\\"', $value)) . '");' . "\n";
                                                } elseif ($value == "0" || $value == "1") {
                                                    echo '$("input[name=' . esc_attr($name) . ']").prop("checked", ' . (int)$value . '); $("input[name=' . esc_attr($name) . ']").trigger("change");';
                                                    echo '$("input[type=hidden][name=' . esc_attr($name) . ']").val("' . esc_attr(str_replace('"', '\\"', $value)) . '");' . "\n";
                                                }
                                            }
                                            ?>
                                            $('input[name=hmwp_admin_url]').trigger('keyup');
                                            $('.tab-panel_tutorial').hide();
                                            $('.hmwp_nav_item').show();

                                            $('.hmwp_emulate_cms').show();
                                            $('.hmwp_disable_url').hide();
                                            $('.hmwp_presets').hide();
                                            $('select[name="hmwp_emulate_cms"] option[value="<?php echo esc_attr($lite['hmwp_emulate_cms']) ?>"]').prop('selected', 'selected');
                                        });

                                        $(".ghost_confirmation").on('click', function () {
                                            $('input[name=hmwp_mode]').val('ninja');
                                            <?php
                                            $ninja = @array_merge(HMWP_Classes_Tools::$default, HMWP_Classes_Tools::$ninja);
                                            foreach ($ninja as $name => $value) {
                                                if (is_string($value) && $value <> "0" && $value <> "1") {
                                                    echo '$("input[type=text][name=' . esc_attr($name) . ']").val("' . esc_attr(str_replace('"', '\\"', $value)) . '");' . "\n";
                                                } elseif ($value == "0" || $value == "1") {
                                                    echo '$("input[name=' . esc_attr($name) . ']").prop("checked", ' . (int)$value . '); $("input[name=' . esc_attr($name) . ']").trigger("change");';
                                                    echo '$("input[type=hidden][name=' . esc_attr($name) . ']").val("' . esc_attr(str_replace('"', '\\"', $value)) . '");' . "\n";
                                                }
                                            }
                                            ?>
                                            $('input[name=hmwp_admin_url]').trigger('keyup');
                                            $('.tab-panel_tutorial').hide();
                                            $('.hmwp_nav_item').show();

                                            $('.hmwp_emulate_cms').show();
                                            $('.hmwp_disable_url').hide();
                                            $('.hmwp_presets').hide();
                                            $('select[name="hmwp_emulate_cms"] option[value="<?php echo esc_attr($ninja['hmwp_emulate_cms']) ?>"]').prop('selected', 'selected');
                                        });

                                        if ($('input[name=hmwp_mode]').val() === 'default'){
                                            $('.hmwp_nav_item').not(':first').hide();
                                        }else{
                                            $('.hmwp_nav_item').show();
                                        }

                                        //Listen the modal close
                                        $(document).on('hide.bs.modal','.modal', function () {
                                            $('.group_autoload button').removeClass('active');
                                            $('.group_autoload .'+$('input[name=hmwp_mode]').val()+'_autoload').addClass('active');
                                        });
                                    });
                                })(jQuery);
                            </script>
                            <div class="hmwp_disable_url col-sm-12 row border-bottom border-light py-2 m-0" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'default') ? 'style="display:none"' : '') ?>>
                                <?php if(!HMWP_Classes_Tools::getOption('logout')) { ?>
                                    <?php if (defined('HMWP_DEFAULT_LOGIN') && HMWP_DEFAULT_LOGIN ) {
		                                if ( stripos( HMWP_DEFAULT_LOGIN, site_url() ) !== false ) {
			                                $custom_login = HMWP_DEFAULT_LOGIN;
		                                } else {
			                                $custom_login = site_url( HMWP_DEFAULT_LOGIN );
		                                }
                                        ?>
                                        <div class="col-sm-12 pt-3">
                                            <strong><?php echo  esc_html__("Login URL", 'hide-my-wp') ?>:</strong>
                                            <?php echo '<a href="' . esc_url($custom_login) . '" target="_blank">' . esc_url($custom_login) . '</a>' ?>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="col-sm-12 pt-3">
                                            <strong><?php echo  esc_html__("Login URL", 'hide-my-wp') ?>:</strong>
                                            <?php echo '<a href="' . esc_url(site_url() . '/' . HMWP_Classes_Tools::getOption('hmwp_login_url')) . '" target="_blank">' . esc_url(site_url() . '/' . HMWP_Classes_Tools::getOption('hmwp_login_url')) . '</a>' ?>
                                        </div>
                                    <?php }?>
                                <?php }?>
                            </div>

                            <div class="hmwp_presets col-sm-12 p-0 pt-3 m-0" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') <> 'default') ? 'style="display:none"' : '') ?>>
                                <div class="card-title">
                                    <?php echo  esc_html__("More options", 'hide-my-wp') ?>: <a href="<?php echo esc_url( HMWP_Classes_Tools::getSettingsUrl('hmwp_backup') ); ?>" class="font-weight-bold" ><?php echo esc_html__('Load Security Presets', 'hide-my-wp'); ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="hmwp_emulate_cms col-sm-12 row justify-content-center py-4 shadow-none" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'default') ? 'style="display:none"' : '') ?>>
                            <div class="py-2 px-1 text-right">
                                <div class="font-weight-bold"><?php echo esc_html__('Simulate CMS', 'hide-my-wp'); ?>:</div>
                            </div>
                            <div class="col-sm-4 px-2 input-group">
                                <select name="hmwp_emulate_cms" class="selectpicker form-control mb-1">
                                    <option value="" <?php selected('', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?>><?php echo esc_html__("No CMS Simulation", 'hide-my-wp') ?></option>
                                    <option value="drupal" <?php selected('drupal', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Drupal 8", 'hide-my-wp') ?></option>
                                    <option value="drupal9" <?php selected('drupal9', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Drupal 9", 'hide-my-wp') ?></option>
                                    <option value="drupal10" <?php selected('drupal10', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Drupal 10", 'hide-my-wp') ?></option>
                                    <option value="drupal11" <?php selected('drupal11', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Drupal 11", 'hide-my-wp') ?></option>
                                    <option value="joomla3" <?php selected('joomla3', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Joomla 3", 'hide-my-wp') ?></option>
                                    <option value="joomla4" <?php selected('joomla4', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Joomla 4", 'hide-my-wp') ?></option>
                                    <option value="joomla5" <?php selected('joomla5', HMWP_Classes_Tools::getOption('hmwp_emulate_cms')) ?> ><?php echo esc_html__("Joomla 5", 'hide-my-wp') ?></option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="card col-sm-12 p-0 m-0 mt-3" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'default') ? 'style="display:none"' : '') ?>>
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Whitelist', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/whitelist-ips-paths/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-md-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Whitelist IPs', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('Add IPs that can pass plugin security', 'hide-my-wp') ?></div>
                                </div>
                                <div class="col-md-8 p-0 input-group input-group">
                                    <?php
                                    $whitelist_ip = array();
                                    if (HMWP_Classes_Tools::getOption('whitelist_ip')) {
                                        $whitelist_ip = json_decode(HMWP_Classes_Tools::getOption('whitelist_ip'), true);
                                    }
                                    ?>
                                    <textarea type="text" class="form-control" name="whitelist_ip" style="height: 100px"><?php echo esc_html(!empty($whitelist_ip) ? implode(PHP_EOL, $whitelist_ip) : '') ?></textarea>
                                    <div class="small text-black-50 col-md-12 pt-2 px-0"><?php echo sprintf(esc_html__('You can white-list a single IP address like 192.168.0.1 or a range of 245 IPs like 192.168.0.*. Find your IP with %s', 'hide-my-wp'), '<a href="https://whatismyipaddress.com/" target="_blank">https://whatismyipaddress.com/</a>') ?></div>
	                                <?php
	                                $domain = ( HMWP_Classes_Tools::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) ? get_home_url( BLOG_ID_CURRENT_SITE ) : site_url();
	                                if( $ip = @gethostbyname( wp_parse_url($domain, PHP_URL_HOST) ) ) { ?>
                                        <div class="small text-black-50 col-md-12 pt-1 px-0"><?php echo sprintf( esc_html__( 'To whitelist your website IP address, add: %s', 'hide-my-wp' ), '<strong>'.$ip.'</strong>' ) ?></div>
	                                <?php } ?>
                                </div>
                            </div>

                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-md-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Whitelist Paths', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('Add paths that can pass plugin security', 'hide-my-wp') ?></div>
                                    <div class="small text-black-50"><?php echo esc_html__('e.g. /cart/', 'hide-my-wp') ?></div>
                                    <div class="small text-black-50"><?php echo esc_html__('e.g. /checkout/', 'hide-my-wp') ?></div>
                                </div>
                                <div class="col-md-8 p-0 input-group input-group">
                                    <?php
                                    $whitelist_urls = array();
                                    if (HMWP_Classes_Tools::getOption('whitelist_urls')) {
                                        $whitelist_urls = json_decode(HMWP_Classes_Tools::getOption('whitelist_urls'), true);
                                    }
                                    ?>
                                    <textarea type="text" class="form-control" name="whitelist_urls" style="height: 100px"><?php echo esc_html(!empty($whitelist_urls) ? implode(PHP_EOL, $whitelist_urls) : '') ?></textarea>
                                </div>
                            </div>

                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3 border-bottom">
                                <div class="col-sm-4 p-1">
                                    <div class="font-weight-bold"><?php echo esc_html__('Whitelist Options', 'hide-my-wp'); ?></div>
                                    <div class="text-black-50 small"><?php echo esc_html__('Chose what to do when accessing from whitelist IP addresses and whitelisted paths.', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group mb-1">
                                    <select name="whitelist_level" class="selectpicker form-control">
                                        <option value="0" <?php echo selected(0, HMWP_Classes_Tools::getOption('whitelist_level')) ?> ><?php echo esc_html__('Allow Hidden Paths', 'hide-my-wp'); ?></option>
                                        <option value="1" <?php echo selected(1, HMWP_Classes_Tools::getOption('whitelist_level')) ?> ><?php echo esc_html__('Show Default Paths & Allow Hidden Paths', 'hide-my-wp'); ?></option>
                                        <option value="2" <?php echo selected(2, HMWP_Classes_Tools::getOption('whitelist_level')) ?> ><?php echo esc_html__('Show Defaults Paths & Allow Everything', 'hide-my-wp'); ?></option>
                                    </select>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card col-sm-12 p-0 m-0 mt-3 hmwp_help" >
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Help & FAQs', 'hide-my-wp'); ?></h3>
                        <div class="card-body">
                            <?php if(HMWP_Classes_Tools::isNginx()) { ?>
                                <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setup-wp-ghost-on-nginx-server/' ) ?>" target="_blank">Setup The Plugin On Nginx Server</a></div>
                                <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setup-wp-ghost-on-nginx-web-server-with-virtual-private-server/' ) ?>" target="_blank">Setup The Plugin On Nginx Server with Virtual Private Server</a></div>
                                <div class="border-bottom my-3"></div>
                            <?php }?>
                            <?php if(HMWP_Classes_Tools::isWpengine()) { ?>
                                <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/wp-engine-wp-ghost-setup/' ) ?>" target="_blank">Setup The Plugin On WP Engine</a></div>
                                <div class="border-bottom my-3"></div>
                            <?php }?>
                            <?php if(HMWP_Classes_Tools::isGodaddy()) { ?>
                                <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/godaddy-hosting-wp-ghost-setup/' ) ?>" target="_blank">Setup The Plugin On Godaddy</a></div>
                                <div class="border-bottom my-3"></div>
                            <?php }?>
                            <?php if(HMWP_Classes_Tools::isIIS()) { ?>
                                <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/setup-wp-ghost-on-windows-iis-server/' ) ?>" target="_blank">Setup The Plugin On Windows IIS Server</a></div>
                                <div class="border-bottom my-3"></div>
                            <?php }?>
                            <?php if(HMWP_Classes_Tools::isInmotion() && HMWP_Classes_Tools::isNginx()) { ?>
                                <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/inmotion-wordpress-hosting-wp-ghost-setup/' ) ?>" target="_blank">Setup The Plugin On Inmotion Server</a></div>
                                <div class="border-bottom my-3"></div>
                            <?php }?>

                            <div class="mb-2 text-success font-weight-bold"><i class="dashicons dashicons-editor-help"></i><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/wp-ghost-settings-best-practice/' ) ?>" target="_blank">WP Ghost Settings – Best Practice</a><i class="dashicons dashicons-editor-help"></i></div>
                            <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-from-wordpress-theme-detectors/' ) ?>" target="_blank">How To Hide Your Site From Detectors & Hackers Bots</a></div>
                            <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/activate-brute-force-protection/' ) ?>" target="_blank">How To Use Brute Force Protection</a></div>
                            <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-use-website-events-log/' ) ?>" target="_blank">How To Use Events Log</a></div>

                            <div class="border-bottom my-3"></div>
                            <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/theme-not-loading-correctly-website-loads-slower/' ) ?>" target="_blank">Theme Not Loading Correctly & Website Loads Slower</a></div>
                            <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/wp-ghost-compatibility-plugins-list/' ) ?>" target="_blank">Compatibility Plugins List</a></div>
                            <div class="mb-2"><a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/' ) ?>" target="_blank"><?php echo esc_html__('More Help'); ?>>></a></div>
                        </div>
                    </div>

                    <div class="card col-sm-12 p-0 m-0 mt-4 hmwp_help" >
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Troubleshooting', 'hide-my-wp'); ?></h3>
                        <div class="card-body">

                            <h6 class="mb-2">In case your configs are wrong: </h6>
                            <ul style="margin: 0;padding: 0;list-style: initial;">
                                <li style="margin: 0 0 0 40px;padding: 0;line-height: 30px;">
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/theme-not-loading-correctly-website-loads-slower/' ) ?>" target="_blank">Theme Not Loading Correctly & Website Loads Slower</a>
                                </li>
                                <li style="margin: 0 0 0 40px;padding: 0;line-height: 30px;">
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-plugins-like-woocommerce-and-elementor/' ) ?>" target="_blank">Hide plugins like WooCommerce and Elementor</a>
                                </li>
                                <li style="margin: 0 0 0 40px;padding: 0;line-height: 30px;">
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/disable-wp-ghost-in-case-of-error/' ) ?>" target="_blank">Remove Plugin Through File Manager</a>
                                </li>
                            </ul>


                            <div class="mt-3" <?php echo((HMWP_Classes_Tools::getOption('hmwp_mode') == 'default') ? 'style="display:none"' : '') ?>>
                                <h6 class="mb-2"><?php echo sprintf(esc_html__("Copy the %s SAFE URL %s and use it to deactivate all the custom paths if you can't login.", 'hide-my-wp'), '<strong><a href="'. esc_url(site_url() . "/wp-login.php?" . HMWP_Classes_Tools::getOption('hmwp_disable_name') . "=" . HMWP_Classes_Tools::getOption('hmwp_disable')) .'" class="text-danger" target="_blank">', '</a></strong>'); ?></h6>
                                <h6><a href="<?php echo esc_url(site_url() . "/wp-login.php?" . HMWP_Classes_Tools::getOption('hmwp_disable_name') . "=" . HMWP_Classes_Tools::getOption('hmwp_disable'))?>" target="_blank"><?php echo esc_url(site_url() . "/wp-login.php?" . HMWP_Classes_Tools::getOption('hmwp_disable_name') . "=" . HMWP_Classes_Tools::getOption('hmwp_disable'))?></a></h6>
                            </div>

                        </div>
                    </div>

                </div>
                <div id="newadmin" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Admin Security', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-and-hide-wp-admin-path-with-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                        <?php if (defined('HMWP_DEFAULT_ADMIN') && HMWP_DEFAULT_ADMIN && HMW_RULES_IN_CONFIG ) {
                            echo ' <div class="text-danger col-sm-12 border-bottom border-light py-3 mx-0 my-3">' . sprintf(esc_html__('Your admin URL is changed by another plugin/theme in %s. To activate this option, disable the custom admin in the other plugin or deativate it.', 'hide-my-wp'), '<strong>' . esc_html(HMWP_DEFAULT_ADMIN) . '</strong>') . '</div>';
                            echo '<input type="hidden" name="hmwp_admin_url" value="' . esc_attr(HMWP_Classes_Tools::getDefault('hmwp_admin_url')) . '"/>';
                        } else {
                            if (HMWP_Classes_Tools::isGodaddy() ) {
                                echo ' <div class="text-danger col-sm-12 border-bottom border-light py-3 mx-0 my-3">' . sprintf(esc_html__("Your admin URL can't be changed on %s hosting because of the %s security terms.", 'hide-my-wp'), '<strong>Godaddy</strong>', '<strong>Godaddy</strong>') . '</div>';
                                echo '<input type="hidden" name="hmwp_admin_url" value="' . esc_attr(HMWP_Classes_Tools::getDefault('hmwp_admin_url')) . '"/>';
                            } elseif (HMWP_Classes_ObjController::getClass('HMWP_Models_Rules')->isConfigAdminCookie() ) {
                                echo ' <div class="text-danger col-sm-12 border-bottom border-light py-3 mx-0 my-3">' . sprintf(esc_html__("The constant ADMIN_COOKIE_PATH is defined in wp-config.php by another plugin. You can't change %s unless you remove the line define('ADMIN_COOKIE_PATH', ...);.", 'hide-my-wp'), '<strong>' . esc_html(HMWP_Classes_Tools::getDefault('hmwp_admin_url')) . '</strong>') . '</div>';
                                echo '<input type="hidden" name="hmwp_admin_url" value="' . esc_attr(HMWP_Classes_Tools::getDefault('hmwp_admin_url')) . '"/>';
                            } else {
                                ?>
                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                    <div class="col-sm-4 p-0 font-weight-bold">
                                        <?php echo esc_html__('Custom Admin Path', 'hide-my-wp'); ?>:
                                        <div class="small text-black-50"><?php echo esc_html__('eg. adm, back', 'hide-my-wp'); ?></div>
                                    </div>
                                    <div class="col-sm-8 p-0 input-group">
                                        <input type="text" class="form-control" name="hmwp_admin_url" maxlength="32" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_admin_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_admin_url')) ?>"/>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-and-hide-wp-admin-path-with-wp-ghost/#ghost-changing-wp-admin-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_admin" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_admin" name="hmwp_hide_admin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_admin') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_admin"><?php echo esc_html__('Hide "wp-admin"', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide /wp-admin path from visitors.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_admin_loggedusers" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_admin_loggedusers" name="hmwp_hide_admin_loggedusers" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_admin_loggedusers') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_admin_loggedusers"><?php echo esc_html__('Hide "wp-admin" From Non-Admin Users', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide /wp-admin path from non-administrator users.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1 p-2 hmwp_hide_newadmin_div" <?php echo(HMWP_Classes_Tools::getOption('hmwp_admin_url') == HMWP_Classes_Tools::getDefault('hmwp_admin_url') ? 'style="display:none;"' : '') ?>>
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_newadmin" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_newadmin" name="hmwp_hide_newadmin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_newadmin') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_newadmin"><?php echo esc_html__('Hide the New Admin Path', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide the new admin path from visitors. Show the new admin path only for logged users.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="admin_warning col-sm-12 my-3 text-danger p-0 text-center small" style="display: none">
                                <?php echo esc_html__("Some themes don't work with custom Admin and Ajax paths. In case of ajax errors, switch back to wp-admin and admin-ajax.php.", 'hide-my-wp'); ?>
                            </div>
                            <div class="col-sm-12 text-center border-light py-1 m-0">
                                <a href="<?php echo esc_url(HMWP_Classes_Tools::getSettingsUrl('hmwp_tweaks#tab=redirects', true)) ?>">
                                    <?php echo esc_html__('Manage Login and Logout Redirects', 'hide-my-wp'); ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                    </div>
                </div>
                <div id="newlogin" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Login Security', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-and-hide-wp-login-path-with-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                        <?php if (defined('HMWP_DEFAULT_LOGIN') && HMWP_DEFAULT_LOGIN ) {
                            echo '<div class="text-danger col-sm-12 border-bottom border-light py-3 mx-0 my-3">' . sprintf(esc_html__('Your login URL is changed by another plugin/theme in %s. To activate this option, disable the custom login in the other plugin or deativate it.', 'hide-my-wp'), '<strong>' . esc_html(HMWP_DEFAULT_LOGIN) . '</strong>') . '</div>';
                            echo '<input type="hidden" name="hmwp_login_url" value="' . esc_attr(HMWP_Classes_Tools::getDefault('hmwp_login_url')) . '"/>';
                            echo '<input type="hidden" name="hmwp_lostpassword_url" value=""/>';
                            echo '<input type="hidden" name="hmwp_register_url" value=""/>';
                            echo '<input type="hidden" name="hmwp_logout_url" value=""/>';
                            echo '<input type="hidden" name="hmwp_activate_url" value=""/>';
                            ?>

                            <div class="col-sm-12 row mb-1 ml-1 p-2" <?php echo(HMWP_DEFAULT_LOGIN == HMWP_Classes_Tools::getDefault('hmwp_login_url') ? 'style="display:none;"' : '') ?>>
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_wplogin" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_wplogin" name="hmwp_hide_wplogin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_wplogin') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_wplogin"><?php echo esc_html__('Hide "wp-login.php"', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide /wp-login.php path from visitors.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <?php if(HMWP_DEFAULT_LOGIN == HMWP_Classes_Tools::getDefault('hmwp_login_url') || HMWP_DEFAULT_LOGIN == 'login'){ ?>
                                <input type="hidden" name="hmwp_hide_login" value="0"/>
                            <?php }else{ ?>
                                <div class="col-sm-12 row mb-1 ml-1 p-2" >
                                    <div class="checker col-sm-12 row my-2 py-0">
                                        <div class="col-sm-12 p-0 switch switch-sm">
                                            <input type="hidden" name="hmwp_hide_login" value="0"/>
                                            <input type="checkbox" id="hmwp_hide_login" name="hmwp_hide_login" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_login') ? 'checked="checked"' : '') ?> value="1"/>
                                            <label for="hmwp_hide_login"><?php echo esc_html__('Hide "login" Path', 'hide-my-wp'); ?></label>
                                            <div class="text-black-50 ml-5"><?php echo esc_html__('Hide /login path from visitors.', 'hide-my-wp'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php }?>

                            <div class="col-sm-12 row mb-1 ml-1 p-2" <?php echo(HMWP_DEFAULT_LOGIN == HMWP_Classes_Tools::getDefault('hmwp_login_url') ? 'style="display:none;"' : '') ?>>
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_newlogin" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_newlogin" name="hmwp_hide_newlogin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_newlogin') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_newlogin"><?php echo esc_html__('Hide the New Login Path', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide the new login path from visitors. Show the new login path only for direct access.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                        <?php } else { ?>
                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Custom Login Path', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('eg. login or signin', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" name="hmwp_login_url" maxlength="32" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_login_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_login_url')) ?>"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-and-hide-wp-login-path-with-wp-ghost/#ghost-changing-wp-login-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1 p-2 hmwp_hide_wplogin_div" <?php echo(HMWP_Classes_Tools::getOption('hmwp_login_url') == HMWP_Classes_Tools::getDefault('hmwp_login_url') ? 'style="display:none;"' : '') ?>>
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_wplogin" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_wplogin" name="hmwp_hide_wplogin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_wplogin') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_wplogin"><?php echo esc_html__('Hide "wp-login.php"', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide /wp-login.php path from visitors.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1 p-2 hmwp_hide_login_div" <?php echo(HMWP_Classes_Tools::getOption('hmwp_login_url') == HMWP_Classes_Tools::getDefault('hmwp_login_url') ? 'style="display:none;"' : '') ?>>
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_login" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_login" name="hmwp_hide_login" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_login') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_login"><?php echo esc_html__('Hide "login" Path', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide /login path from visitors.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 row mb-1 ml-1 p-2 hmwp_hide_newlogin_div" <?php echo(HMWP_Classes_Tools::getOption('hmwp_login_url') == HMWP_Classes_Tools::getDefault('hmwp_login_url') ? 'style="display:none;"' : '') ?>>
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_newlogin" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_newlogin" name="hmwp_hide_newlogin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_newlogin') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_newlogin"><?php echo esc_html__('Hide the New Login Path', 'hide-my-wp'); ?></label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__('Hide the new login path from visitors. Show the new login path only for direct access.', 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            if(function_exists('get_available_languages')) {
                                $languages = get_available_languages();
                                if (!empty($languages)) {
                                    ?>
                                    <div class="col-sm-12 row mb-1 ml-1 p-2">
                                        <div class="checker col-sm-12 row my-2 py-0">
                                            <div class="col-sm-12 p-0 switch switch-sm">
                                                <input type="hidden" name="hmwp_disable_language_switcher" value="0"/>
                                                <input type="checkbox" id="hmwp_disable_language_switcher"
                                                       name="hmwp_disable_language_switcher"
                                                       class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_disable_language_switcher') ? 'checked="checked"' : '') ?>
                                                       value="1"/>
                                                <label for="hmwp_disable_language_switcher"><?php echo esc_html__('Hide Language Switcher', 'hide-my-wp'); ?></label>
                                                <div class="text-black-50 ml-5"><?php echo esc_html__("Hide the language switcher option on the login page", 'hide-my-wp'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            }?>

                            <div class="border-bottom border-gray"></div>

                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold" style="font-size: 0.9rem">
                                    <?php echo esc_html__('Custom Lost Password Path', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('eg. lostpass or forgotpass', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" maxlength="32" name="hmwp_lostpassword_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_lostpassword_url')) ?>" placeholder="?action=lostpassword"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-lost-password-path-with-wp-ghost/#ghost-change-lost-password-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>
                            </div>

                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Custom Register Path', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('eg. newuser or register', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" maxlength="32" name="hmwp_register_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_register_url')) ?>" placeholder="?action=register"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-register-path-with-wp-ghost/#ghost-change-register-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>
                            </div>


                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Custom Logout Path', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('eg. logout or disconnect', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" maxlength="32" name="hmwp_logout_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_logout_url')) ?>" placeholder="?action=logout"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-logout-path-with-wp-ghost/#ghost-change-logout-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>
                            </div>

                            <?php if (HMWP_Classes_Tools::isMultisites() ) { ?>
                                <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                    <div class="col-sm-4 p-0 font-weight-bold">
                                        <?php echo esc_html__('Custom Activation Path', 'hide-my-wp'); ?>:
                                        <div class="small text-black-50"><?php echo esc_html__('eg. multisite activation link', 'hide-my-wp'); ?></div>
                                    </div>
                                    <div class="col-sm-8 p-0 input-group">
                                        <input type="text" class="form-control" maxlength="32" name="hmwp_activate_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_activate_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_activate_url')) ?>"/>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-wp-activate-path-with-hide-my-wp-ghost/#ghost-change-wp-activate-php-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-sm-12 text-center border-light py-1 m-0">
                                <a href="<?php echo esc_url(HMWP_Classes_Tools::getSettingsUrl('hmwp_tweaks#tab=redirects', true)) ?>" class="mr-3">
                                    <?php echo esc_html__('Manage Login and Logout Redirects', 'hide-my-wp'); ?>
                                </a>
                                |
                                <a href="<?php echo esc_url(HMWP_Classes_Tools::getSettingsUrl('hmwp_brute#tab=brute', true)) ?>" class="ml-3">
                                    <?php echo esc_html__('Manage Brute Force Protection', 'hide-my-wp'); ?>
                                </a>
                            </div>
                        <?php } ?>

                    </div>
                    </div>
                </div>
                <div id="author" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('User Security', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-author-path-and-hide-id-with-hide-my-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">

                        <?php if (!HMWP_Classes_Tools::isMultisiteWithPath() && !HMWP_Classes_Tools::isNginx() && !HMWP_Classes_Tools::isWpengine() ) { ?>
                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Custom author Path', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('eg. profile, usr, writer', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" maxlength="32" name="hmwp_author_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_author_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_author_url')) ?>"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-author-path-and-hide-id-with-hide-my-wp-ghost/#ghost-change-author-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="hmwp_author_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_author_url')) ?>"/>
                        <?php } ?>
                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_authors" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_authors" name="hmwp_hide_authors" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_authors') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_authors"><?php echo esc_html__('Hide Author ID URL', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-author-path-and-hide-id-with-hide-my-wp-ghost/#ghost-hide-author-id-url' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__("Don't let URLs like domain.com?author=1 show the user login name", 'hide-my-wp'); ?></div>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__( "Hide user discoverability from API calls, sitemaps, oEmbed, and more.", 'hide-my-wp' ); ?></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    </div>
                </div>
                <div id="ajax" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Ajax Security', 'hide-my-wp'); ?>
                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-admin-ajax-php-path-with-hide-my-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                    </h3>
                        <div class="card-body">
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom admin-ajax Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. ajax, json', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_admin-ajax_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_admin-ajax_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_admin-ajax_url')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-admin-ajax-php-path-with-hide-my-wp-ghost/#ghost-change-admin-ajax-php-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2 hmwp_hideajax_admin_div">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hideajax_admin" value="0"/>
                                    <input type="checkbox" id="hmwp_hideajax_admin" name="hmwp_hideajax_admin" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hideajax_admin') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hideajax_admin"><?php echo esc_html__('Hide wp-admin from Ajax URL', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-admin-ajax-php-path-with-hide-my-wp-ghost/#ghost-hide-wp-admin-from-ajax-url' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo sprintf(esc_html__('Show /%s instead of /%s', 'hide-my-wp'), esc_html(HMWP_Classes_Tools::getOption('hmwp_admin-ajax_url')), esc_html(HMWP_Classes_Tools::getOption('hmwp_admin_url') . '/' . HMWP_Classes_Tools::getOption('hmwp_admin-ajax_url'))); ?></div>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('(works only with the custom admin-ajax path to avoid infinite loops)', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hideajax_paths" value="0"/>
                                    <input type="checkbox" id="hmwp_hideajax_paths" name="hmwp_hideajax_paths" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hideajax_paths') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hideajax_paths"><?php echo esc_html__('Change Paths in Ajax Calls', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-admin-ajax-php-path-with-hide-my-wp-ghost/#ghost-change-paths-in-ajax-calls' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('This will prevent from showing the old paths when an image or font is called through ajax', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div id="core" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('WP Core Security', 'hide-my-wp'); ?></h3>
                        <div class="card-body">

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom wp-content Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. core, inc, include', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_wp-content_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_wp-content_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_wp-content_url')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-wp-content-path-with-wp-ghost/#ghost-change-wp-content-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom wp-includes Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. lib, library', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_wp-includes_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_wp-includes_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_wp-includes_url')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-wp-includes-path-with-wp-ghost/#ghost-change-wp-includes-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">

                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom uploads Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. images, files', 'hide-my-wp'); ?></div>
                            </div>
                            <?php if (!HMWP_Classes_Tools::isDifferentUploadPath() ) { ?>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" maxlength="32" name="hmwp_upload_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_upload_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_upload_url')) ?>"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-wp-content-uploads-path-with-wp-ghost/#ghost-changing-the-wp-content-uploads-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>
                            <?php } else { ?>
                                <div class="col-sm-8 text-danger p-0">
                                    <?php echo sprintf(esc_html__("You already defined a different wp-content/uploads directory in wp-config.php %s", 'hide-my-wp'), ': <strong>' . esc_html(UPLOADS) . '</strong>'); ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom comment Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. comments, discussion', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_wp-comments-post" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_wp-comments-post')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_wp-comments-post')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-comments-path-using-wp-ghost/#ghost-change-the-comments-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_oldpaths" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_oldpaths" name="hmwp_hide_oldpaths" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_oldpaths') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_oldpaths"><?php echo esc_html__('Hide WordPress Common Paths', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-wordpress-common-paths-and-files/#ghost-hide-wordpress-common-paths' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('Hide the old /wp-content, /wp-include paths once they are changed with the new ones', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-1 my-3 hmwp_hide_oldpaths">
                            <div class="col-sm-4 p-1">
                                <div class="font-weight-bold"><?php echo esc_html__('Hide File Extensions', 'hide-my-wp'); ?>:</div>
                                <div class="text-black-50 small"><?php echo esc_html__("Select the file extensions you want to hide on old paths", 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <select multiple name="hmwp_hide_oldpaths_types[]" class="selectpicker form-control mb-1">
                                    <?php
                                    $alltypes = array('css', 'js', 'media', 'php', 'txt', 'html', 'lock', 'xml', 'json');

                                    //If the option to hide image old paths is set by the user
                                    if (apply_filters('hmwp_hide_old_images', HMW_HIDE_OLD_IMAGES)) {
                                        $alltypes[] = 'image';
                                    }

                                    $types = (array)HMWP_Classes_Tools::getOption('hmwp_hide_oldpaths_types');
                                    if (empty($types) ) {
                                        $types = array('php', 'txt', 'html', 'lock');
                                    }
                                    foreach ( $alltypes as $key ) {
                                        echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $types) ? 'selected="selected"' : '') . '>' .  esc_html(strtoupper($key) . ' ' .'files', 'hide-my-wp') . '</option>';
                                    } ?>
                                </select>
                            </div>

                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm ">
                                    <input type="hidden" name="hmwp_hide_commonfiles" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_commonfiles" name="hmwp_hide_commonfiles" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_commonfiles') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_commonfiles"><?php echo esc_html__('Hide WordPress Common Files', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-wordpress-common-paths-and-files/#ghost-hide-wordpress-common-files' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('Hide wp-config.php , wp-config-sample.php, readme.html, license.txt, upgrade.php and install.php files', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-1 my-3 hmwp_hide_commonfiles">
                            <div class="col-sm-4 p-1">
                                <div class="font-weight-bold"><?php echo esc_html__('Hide Common Files', 'hide-my-wp'); ?>:</div>
                                <div class="text-black-50 small"><?php echo esc_html__("Select the files you want to hide on old paths", 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <select multiple name="hmwp_hide_commonfiles_files[]" class="selectpicker form-control mb-1">
                                    <?php
                                    $allfiles = array('wp-config.php', 'wp-config-sample.php', 'wp-load.php', 'wp-settings.php', 'wp-blog-header.php', 'readme.html', 'readme.txt', 'install.php', 'license.txt', 'php.ini', 'hidemywp.conf', 'bb-config.php', 'error_log', 'debug.log');

                                    //let the user add more files in the list
                                    $allfiles = apply_filters('hmwp_hide_commonfiles_files', $allfiles);

                                    if(HMWP_Classes_Tools::getDefault('hmwp_wp-comments-post') <> HMWP_Classes_Tools::getOption('hmwp_wp-comments-post')) {
                                        array_unshift($allfiles, 'wp-comments-post.php');
                                    }

                                    if(HMWP_Classes_Tools::isNginx()) {
                                        $allfiles[] = 'hidemywp.conf';
                                    }

                                    $files = (array)HMWP_Classes_Tools::getOption('hmwp_hide_commonfiles_files');

                                    foreach ( $allfiles as $key ) {
                                        echo '<option value="' . esc_attr($key) . '" ' . (in_array($key, $files) ? 'selected="selected"' : '') . '>' . esc_html($key) . '</option>';
                                    } ?>
                                </select>
                            </div>

                        </div>

                        <?php if (HMWP_Classes_Tools::isNginx() || HMWP_Classes_Tools::isApache() || HMWP_Classes_Tools::isLitespeed() ) { ?>

                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <?php $uploads = wp_upload_dir() ?>
                                        <input type="hidden" name="hmwp_disable_browsing" value="0"/>
                                        <input type="checkbox" id="hmwp_disable_browsing" name="hmwp_disable_browsing" class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_disable_browsing') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_disable_browsing"><?php echo esc_html__('Disable Directory Browsing', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/hide-wordpress-common-paths-and-files/#ghost-disable-directory-browsing' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo sprintf(esc_html__("Don't let hackers see any directory content. See %sUploads Directory%s", 'hide-my-wp'), '<a href="' . esc_url($uploads['baseurl']) . '" target="_blank">', '</a>'); ?></div>
                                        <div class="text-danger my-2 ml-5"><?php echo sprintf(esc_html__("Normally, the option to block visitors from browsing server directories is activated by the host through server configuration, and activating it twice in the config file may cause errors, so it's best to first check if the %sUploads Directory%s is visible.", 'hide-my-wp'), '<a href="' . esc_url($uploads['baseurl']) . '" target="_blank">', '</a>'); ?></div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                    </div>
                    </div>
                </div>
                <div id="plugin" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Plugins Settings', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-plugins-path-with-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom plugins Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. modules', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_plugin_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_plugin_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_plugin_url')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-plugins-path-with-wp-ghost/#ghost-change-the-plugins-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_plugins" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_plugins" name="hmwp_hide_plugins" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_plugins') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_plugins"><?php echo esc_html__('Hide Plugin Names', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-plugins-path-with-wp-ghost/#ghost-hide-plugin-names' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('Give random names to each plugin', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 row mb-1 ml-1 p-2 hmwp_hide_plugins">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_all_plugins" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_all_plugins" name="hmwp_hide_all_plugins" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_all_plugins') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_all_plugins"><?php echo esc_html__('Hide All The Plugins', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-plugins-path-with-wp-ghost/#ghost-hide-all-the-plugins' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('Hide both active and deactivated plugins', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_oldpaths_plugins" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_oldpaths_plugins" name="hmwp_hide_oldpaths_plugins" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_oldpaths_plugins') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_oldpaths_plugins"><?php echo esc_html__('Hide WordPress Old Plugins Path', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-plugins-path-with-wp-ghost/#ghost-hide-wordpress-old-plugins-path' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__("Hide the old /wp-content/plugins path once it's changed with the new one", 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="border-top mt-3 pt-3 hmwp_hide_plugins">
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_plugins_advanced" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_plugins_advanced" name="hmwp_hide_plugins_advanced" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_plugins_advanced') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_plugins_advanced"><?php echo esc_html__('Show Advanced Options', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-plugins-path-with-wp-ghost/#ghost-show-advanced-options' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                            <span class="text-black-50 small">(<?php echo esc_html__("not recommended", 'hide-my-wp'); ?>)</span>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Manually customize each plugin name and overwrite the random name", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 row py-4 border-bottom hmwp_hide_plugins_advanced" >
                                <input type="hidden" class="form-control" name="hmw_plugins_mapping[]" value="" />
                                <?php
                                $plugins = (array)HMWP_Classes_Tools::getOption('hmw_plugins_mapping');
                                $all_plugins =  (array)HMWP_Classes_Tools::getAllPlugins(); ?>

                                <?php if(!empty($plugins) && !empty($all_plugins)){ ?>
                                    <div class="col-sm-12 m-0 p-0 hmw_plugins_mappings">
                                    <?php foreach ( $plugins as $plugin => $value ) {
                                        if($value == '') { continue;
                                        }
                                        ?>
                                        <div class="col-sm-12 row pb-3 m-0 my-1 border-0">
                                            <div class="hmwp_mapping_remove" onclick="jQuery(this).parent().remove()" title="<?php echo esc_attr__('Remove') ?>">x</div>
                                            <div class="col-sm-4 p-0 my-2 font-weight-bold">
                                                <?php echo esc_html(rtrim(plugin_dir_path($plugin), '/')) ?>
                                            </div>
                                            <div class="col-sm-8 p-0 input-group">
                                                <input type="text" class="form-control" maxlength="32" name="hmw_plugins_mapping[<?php echo esc_attr($plugin)?>]" value="<?php echo esc_attr($value) ?>" />
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>

                                    <?php if(count($all_plugins) > (count($plugins) - 1)) { ?>
                                    <div class="col-sm-12 row pb-3 m-0 my-1 border-0 hmw_plugins_mapping_select">

                                        <div class="col-sm-12 row pb-3 m-0 my-1 border-0 hmw_plugins_mapping_new" style="display: none">
                                            <div class="hmwp_mapping_remove" onclick="jQuery(this).parent().remove()" title="<?php echo esc_attr__('Remove') ?>">x</div>
                                            <div class="col-sm-4 p-0 my-2 font-weight-bold hmw_plugins_mapping_title"></div>
                                            <div class="col-sm-8 p-0 input-group"><input type="text" maxlength="32" class="form-control" /> </div>
                                        </div>


                                        <div class="col-sm-4 p-0 my-2 font-weight-bold">
                                            <?php echo esc_html__('Customize Plugin Names', 'hide-my-wp'); ?>
                                        </div>
                                        <div class="col-sm-8 p-0 input-group">
                                            <select id="hmw_plugins_mapping_new" class=" selectpicker form-control mb-1">
                                                <option value=""></option>
                                                <?php  foreach ($all_plugins as $plugin){
                                                    if(!in_array($plugin, array_keys($plugins), true)) {
                                                        echo '<option value="' . esc_attr($plugin) . '" >' . esc_html(rtrim(plugin_dir_path($plugin), '/')) . '</option>';
                                                    }
                                                }  ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div id="theme" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('Themes Security', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-themes-path-with-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom themes Path', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. aspect, templates, styles', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_themes_url" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_themes_url')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_themes_url')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-themes-path-with-wp-ghost/#ghost-change-the-themes-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_themes" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_themes" name="hmwp_hide_themes" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_themes') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_themes"><?php echo esc_html__('Hide Theme Names', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-themes-path-with-wp-ghost/#ghost-hide-theme-names' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__('Give random names to each theme (works in WP multisite)', 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-0">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_oldpaths_themes" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_oldpaths_themes" name="hmwp_hide_oldpaths_themes" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_oldpaths_themes') ? 'checked="checked"' : '') ?> value="1"/>
                                    <label for="hmwp_hide_oldpaths_themes"><?php echo esc_html__('Hide WordPress Old Themes Path', 'hide-my-wp'); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-themes-path-with-wp-ghost/#ghost-hide-wordpress-old-themes-path' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__("Hide the old /wp-content/themes path once it's changed with the new one", 'hide-my-wp'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="border-bottom border-gray"></div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-sm-4 p-0 font-weight-bold">
                                <?php echo esc_html__('Custom theme style name', 'hide-my-wp'); ?>:
                                <div class="small text-black-50"><?php echo esc_html__('eg. main.css,  theme.css, design.css', 'hide-my-wp'); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group">
                                <input type="text" class="form-control" maxlength="32" name="hmwp_themes_style" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_themes_style')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_themes_style')) ?>"/>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-themes-path-with-wp-ghost/#ghost-custom-theme-style-name' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>
                        </div>


                        <div class="border-top mt-3 pt-3 hmwp_hide_themes">
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_themes_advanced" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_themes_advanced" name="hmwp_hide_themes_advanced" class="switch" <?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_themes_advanced') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_themes_advanced"><?php echo esc_html__('Show Advanced Options', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-themes-path-with-wp-ghost/#ghost-show-advanced-options' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                            <span class="text-black-50 small">(<?php echo esc_html__("not recommended", 'hide-my-wp'); ?>)</span>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Manually customize each theme name and overwrite the random name", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 row py-4 border-bottom hmwp_hide_themes_advanced" >
                                <input type="hidden" class="form-control" name="hmw_themes_mapping[]" value="" />
                                <?php
                                $themes = (array)HMWP_Classes_Tools::getOption('hmw_themes_mapping');
                                $all_themes =  (array)HMWP_Classes_Tools::getAllThemes();?>

                                <?php if(!empty($themes) && !empty($all_themes)){ ?>
                                    <div class="col-sm-12 m-0 p-0 hmw_themes_mappings">
                                        <?php foreach ( $themes as $theme => $value ) {
                                            if($value == '') { continue;
                                            }
                                            ?>
                                            <div class="col-sm-12 row pb-3 m-0 my-1 border-0">
                                                <div class="hmwp_mapping_remove" onclick="jQuery(this).parent().remove()" title="<?php echo esc_attr__('Remove') ?>">x</div>
                                                <div class="col-sm-4 p-0 my-2 font-weight-bold">
                                                    <?php echo esc_html($theme) ?>
                                                </div>
                                                <div class="col-sm-8 p-0 input-group">
                                                    <input type="text" class="form-control" maxlength="32" name="hmw_themes_mapping[<?php echo esc_attr($theme)?>]" value="<?php echo esc_attr($value) ?>" />
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>

                                    <?php if(count($all_themes) > (count($themes) - 1)) { ?>
                                    <div class="col-sm-12 row pb-3 m-0 my-1 border-0 hmw_themes_mapping_select">

                                        <div class="col-sm-12 row pb-3 m-0 my-1 border-0 hmw_themes_mapping_new" style="display: none">
                                            <div class="hmwp_mapping_remove" onclick="jQuery(this).parent().remove()" title="<?php echo esc_attr__('Remove') ?>">x</div>
                                            <div class="col-sm-4 p-0 my-2 font-weight-bold hmw_themes_mapping_title"></div>
                                            <div class="col-sm-8 p-0 input-group"><input type="text" maxlength="32" class="form-control" /> </div>
                                        </div>


                                        <div class="col-sm-4 p-0 my-2 font-weight-bold">
                                            <?php echo esc_html__('Customize Theme Names', 'hide-my-wp'); ?>
                                        </div>
                                        <div class="col-sm-8 p-0 input-group">
                                            <select id="hmw_themes_mapping_new" class=" selectpicker form-control mb-1">
                                                <option value=""></option>
                                                <?php
                                                //Initialize WordPress Filesystem
                                                $wp_filesystem = HMWP_Classes_ObjController::initFilesystem();

                                                foreach ($all_themes as $theme => $value){
                                                    if ($wp_filesystem->is_dir($value['theme_root']) ) {
                                                        if (!in_array($theme, array_keys($themes), true)) {
                                                            echo '<option value="' . esc_attr($theme) . '" >' . esc_html($theme) . '</option>';
                                                        }
                                                    }
                                                }  ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div id="api" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__('API Settings', 'hide-my-wp'); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-rest-api-path-with-wp-ghost/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                            <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                                <div class="col-sm-4 p-0 font-weight-bold">
                                    <?php echo esc_html__('Custom wp-json Path', 'hide-my-wp'); ?>:
                                    <div class="small text-black-50"><?php echo esc_html__('eg. json, api, call', 'hide-my-wp'); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group">
                                    <input type="text" class="form-control" maxlength="32" name="hmwp_wp-json" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_wp-json')) ?>" placeholder="<?php echo esc_attr(HMWP_Classes_Tools::getDefault('hmwp_wp-json')) ?>"/>
                                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-rest-api-path-with-wp-ghost/#ghost-change-the-wp-json-path' ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 20%;"><i class="dashicons dashicons-editor-help"></i></a>
                                </div>

                                <div class="col-sm-12 mt-2 p-2 alert-danger text-center"><?php echo sprintf(esc_html__("Update the settings on %s to refresh the paths after changing REST API path.", 'hide-my-wp'), '<a href="'.esc_url(admin_url('options-permalink.php')).'">'.esc_html__('Settings') . ' > ' . esc_html__('Permalinks').'</a>'); ?></div>

                            </div>
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_rest_api" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_rest_api" name="hmwp_hide_rest_api" class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_rest_api') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_rest_api"><?php echo esc_html__('Hide REST API URL link', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-rest-api-path-with-wp-ghost/#ghost-hide-rest-api-url-link' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Hide wp-json & ?rest_route link tag from website header", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_disable_rest_api" value="0"/>
                                        <input type="checkbox" id="hmwp_disable_rest_api" name="hmwp_disable_rest_api" class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_disable_rest_api') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_disable_rest_api"><?php echo esc_html__('Disable REST API Access', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-rest-api-path-with-wp-ghost/#ghost-disable-rest-api-access' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                            <span class="text-black-50 small">(<?php echo esc_html__("not recommended", 'hide-my-wp'); ?>)</span>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Disable REST API access for not logged in users", 'hide-my-wp'); ?></div>
                                        <div class="text-danger my-2 ml-5"><?php echo esc_html__("The REST API is crucial for many plugins as it allows them to interact with the WordPress database and perform various actions programmatically.", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_disable_rest_api_param" value="0"/>
                                        <input type="checkbox" id="hmwp_disable_rest_api_param" name="hmwp_disable_rest_api_param" class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_disable_rest_api_param') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_disable_rest_api_param"><?php echo esc_html__('Disable "rest_route" Param Access', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-rest-api-path-with-wp-ghost/#ghost-disable-rest-route-parameter-access' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Disable REST API access using the parameter 'rest_route'", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_disable_xmlrpc" value="0"/>
                                        <input type="checkbox" id="hmwp_disable_xmlrpc" name="hmwp_disable_xmlrpc" class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_disable_xmlrpc') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_disable_xmlrpc"><?php echo esc_html__('Disable XML-RPC Access', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/disable-xml-rpc-access-using-wp-ghost/#ghost-disable-xml-rpc-access' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Disable the access to /xmlrpc.php to prevent Brute force attacks via XML-RPC", 'hide-my-wp'); ?></div>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Remove pingback link tag from the website header.", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 row mb-1 ml-1 p-2">
                                <div class="checker col-sm-12 row my-2 py-0">
                                    <div class="col-sm-12 p-0 switch switch-sm">
                                        <input type="hidden" name="hmwp_hide_rsd" value="0"/>
                                        <input type="checkbox" id="hmwp_hide_rsd" name="hmwp_hide_rsd" class="switch"<?php echo(HMWP_Classes_Tools::getOption('hmwp_hide_rsd') ? 'checked="checked"' : '') ?> value="1"/>
                                        <label for="hmwp_hide_rsd"><?php echo esc_html__('Disable RSD Endpoint from XML-RPC', 'hide-my-wp'); ?>
                                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/change-rest-api-path-with-wp-ghost/#ghost-disable-rsd-endpoint-from-xml-rpc' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                        </label>
                                        <div class="text-black-50 ml-5"><?php echo esc_html__("Disable the RSD (Really Simple Discovery) support for XML-RPC & remove RSD tag from header", 'hide-my-wp'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="firewall" class="col-sm-12 p-0 m-0 tab-panel">
                    <input type="hidden" name="hmwp_sqlinjection" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_sqlinjection')) ?>"/>
                    <input type="hidden" name="hmwp_sqlinjection_level" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_sqlinjection_level')) ?>"/>
                    <input type="hidden" name="hmwp_hide_unsafe_headers" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_hide_unsafe_headers')) ?>"/>
                    <input type="hidden" name="hmwp_sqlinjection_location" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_sqlinjection_location')) ?>"/>
                    <input type="hidden" name="hmwp_hide_unsafe_headers" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_hide_unsafe_headers')) ?>"/>
                    <input type="hidden" name="hmwp_detectors_block" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_detectors_block')) ?>"/>
                    <input type="hidden" name="hmwp_security_header" value="<?php echo esc_attr(HMWP_Classes_Tools::getOption('hmwp_security_header')) ?>"/>
                </div>

                <?php do_action('hmwp_change_paths_form_end') ?>

                <?php if (HMWP_Classes_Tools::getOption('test_frontend') || HMWP_Classes_Tools::getOption('logout') || HMWP_Classes_Tools::getOption('error') ) { ?>
                    <div class="col-sm-12 m-0 py-3 px-0">
                        <button type="submit" class="btn rounded-0 btn-success px-5 mr-5 save"><?php echo esc_html__('Save', 'hide-my-wp'); ?></button>
                    </div>
                <?php } else { ?>
                    <div class="col-sm-12 m-0 p-2 bg-light text-center" style="position: fixed; bottom: 0; right: 0; z-index: 100; box-shadow: 0 0 8px -3px #444;">
                        <button type="submit" class="btn rounded-0 btn-success px-5 mr-5 save"><?php echo esc_html__('Save', 'hide-my-wp'); ?></button>
                    </div>
                <?php } ?>


            </form>
        </div>

        <div class="hmwp_col hmwp_col_side p-0 pr-2 mr-2">
            <?php $view->show('blocks/ChangeCacheFiles'); ?>
            <?php $view->show('blocks/SecurityCheck'); ?>
            <?php $view->show('blocks/FrontendCheck'); ?>
        </div>
    </div>
</div>

<div id="hmwp_safe_mode_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><?php echo esc_html__('Safe Mode', 'hide-my-wp') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <h5 class="my-3">
                    <?php echo esc_html__('Safe Mode will set these predefined paths', 'hide-my-wp') ?>:
                </h5>

                <?php
                $default = HMWP_Classes_Tools::$default;
                $changed = @array_merge($default, HMWP_Classes_Tools::$lite);
                ?>

                <ul class="px-3">
                    <li><span><?php echo esc_html__('Login Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_login_url'])?></strong> => <strong>/<?php echo esc_html($changed['hmwp_login_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Core Contents Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-content_url'])  ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_wp-content_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Core Includes Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-includes_url'])?></strong> => <strong>/<?php echo esc_html($changed['hmwp_wp-includes_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Uploads Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-content_url'] .'/'. $default['hmwp_upload_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_upload_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Author Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_author_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_author_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Plugins Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_plugin_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_plugin_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Themes Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-content_url'] .'/'. $default['hmwp_themes_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_themes_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Comments Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-comments-post'])  ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_wp-comments-post']) ?></strong></li>
                </ul>
                <div class="my-2 text-info">
                    <?php echo sprintf(esc_html__('Note! %sPaths are NOT physically change%s on your server.', 'hide-my-wp'), '<strong>', '</strong>') ?>
                </div>
                <div class="my-2">
                    <?php echo esc_html__('The Safe Mode will add the rewrites rules in the config file to hide the old paths from hackers.', 'hide-my-wp') ?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col text-left">
                        <?php echo sprintf(esc_html__('Click %sContinue%s to set the predefined paths.', 'hide-my-wp'), '<strong>', '</strong>') ?><br />
                        <?php echo sprintf(esc_html__('After, click %sSave%s to apply the changes.', 'hide-my-wp'), '<strong>', '</strong>') ?>
                    </div>
                    <div class="col text-right">
                        <button type="button" class="btn btn-secondary safe_cancelaition" data-dismiss="modal"><?php echo esc_html__('Cancel', 'hide-my-wp') ?></button>
                        <button type="button" class="btn btn-success safe_confirmation" data-dismiss="modal"><?php echo esc_html__('Continue', 'hide-my-wp') ?> >></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hmwp_ghost_mode_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><?php echo esc_html__('Ghost Mode', 'hide-my-wp') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <h5 class="my-3">
                    <?php echo esc_html__('Ghost Mode will set these predefined paths', 'hide-my-wp') ?>:
                </h5>

                <?php
                $default = HMWP_Classes_Tools::$default;
                $changed = @array_merge($default, HMWP_Classes_Tools::$ninja);
                ?>

                <ul class="px-3">
                    <li><span><?php echo esc_html__('Admin Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_admin_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_admin_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Login Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_login_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_login_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Ajax URL', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_admin_url'] . '/' . $default['hmwp_admin-ajax_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_admin-ajax_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Core Contents Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-content_url'])  ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_wp-content_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Core Includes Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-includes_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_wp-includes_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Uploads Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-content_url'] .'/'. $default['hmwp_upload_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_upload_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Author Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_author_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_author_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Plugins Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_plugin_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_plugin_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Themes Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-content_url'] .'/'. $default['hmwp_themes_url']) ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_themes_url']) ?></strong></li>
                    <li><span><?php echo esc_html__('Comments Path', 'hide-my-wp') ?>:</span> <strong>/<?php echo esc_html($default['hmwp_wp-comments-post'])  ?></strong> => <strong>/<?php echo esc_html($changed['hmwp_wp-comments-post']) ?></strong></li>
                </ul>
                <div class="my-2 text-info">
                    <?php echo sprintf(esc_html__('Note! %sPaths are NOT physically change%s on your server.', 'hide-my-wp'), '<strong>', '</strong>') ?>
                </div>
                <div class="my-2">
                    <?php echo esc_html__('The Ghost Mode will add the rewrites rules in the config file to hide the old paths from hackers.', 'hide-my-wp') ?>
                </div>
                <div class="my-2">
                    <?php echo sprintf(esc_html__('If you notice any functionality issue please select the %sSafe Mode%s.', 'hide-my-wp'), '<strong>', '</strong>') ?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col text-left">
                        <?php echo sprintf(esc_html__('Click %sContinue%s to set the predefined paths.', 'hide-my-wp'), '<strong>', '</strong>') ?><br />
                        <?php echo sprintf(esc_html__('After, click %sSave%s to apply the changes.', 'hide-my-wp'), '<strong>', '</strong>') ?>
                    </div>
                    <div class="col text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__('Cancel', 'hide-my-wp') ?></button>
                        <button type="button" class="btn btn-success ghost_confirmation" data-dismiss="modal"><?php echo esc_html__('Continue', 'hide-my-wp') ?> >></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
