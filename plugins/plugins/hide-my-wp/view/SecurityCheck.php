<?php defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
if ( ! isset( $view ) ) {
	return;
}

$do_check = false;
//Set the alert if security wasn't check
if ( HMWP_Classes_Tools::getOption( 'hmwp_security_alert' ) ) {
	if ( ! get_option( HMWP_SECURITY_CHECK ) ) {
		$do_check = true;
	} elseif ( $securitycheck_time = get_option( HMWP_SECURITY_CHECK_TIME ) ) {
		if ( ( isset( $securitycheck_time['timestamp'] ) && time() - $securitycheck_time['timestamp'] > ( 3600 * 24 * 7 ) ) ) {
			$do_check = true;
		}
	} else {
		$do_check = true;
	}
}
?>
<div id="hmwp_wrap" class="d-flex flex-row p-0 my-3">
    <div class="hmwp_row d-flex flex-row p-0 m-0">
        <div class="hmwp_col flex-grow-1 px-2 py-0 mr-2 mb-3">
            <div class="card col-sm-12 p-0 m-0">
                <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__( 'WordPress Security Check', 'hide-my-wp' ); ?>:
                    <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/website-security-check/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help" style=" vertical-align: top; padding: 5px 0 !important;"></i></a>
                </h3>
                <div class="card-body p-0 m-0">

                    <div class="col-sm-12 border-0 shadow-0 pb-3 m-0">

						<?php do_action( 'hmwp_security_check_beginning' ) ?>

                        <div class="card col-sm-12 p-4 shadow-none border-0">
                            <div class="card-body text-center p-0">
                                <div class="start_securitycheck">
									<?php if ( ! $do_check ) { ?>
                                        <div class="row col-10 my-4 mx-auto">
                                            <div class="col-sm-5" style="text-align: center">
												<?php if ( ( ( count( $view->riskreport ) * 100 ) / count( $view->risktasks ) ) > 90 ) { ?>
                                                    <img src="<?php echo esc_url( _HMWP_ASSETS_URL_ . 'img/speedometer_danger.png' ) ?>" alt="" style="max-width: 60%; margin: 10px auto;"/>
                                                    <div style="font-size: 1rem; font-style: italic; text-align: center; color: red;"><?php echo sprintf( esc_html__( "Your website security %sis extremely weak%s. %sMany hacking doors are available.", 'hide-my-wp' ), '<strong>', '</strong>', '<br />' ) ?></div>
												<?php } elseif ( ( ( count( $view->riskreport ) * 100 ) / count( $view->risktasks ) ) > 50 ) { ?>
                                                    <img src="<?php echo esc_url( _HMWP_ASSETS_URL_ . 'img/speedometer_low.png' ) ?>" alt="" style="max-width: 60%; margin: 10px auto;"/>
                                                    <div style="font-size: 1rem; font-style: italic; text-align: center; color: red;"><?php echo sprintf( esc_html__( "Your website security %sis very weak%s. %sMany hacking doors are available.", 'hide-my-wp' ), '<strong>', '</strong>', '<br />' ) ?></div>
												<?php } elseif ( ( ( count( $view->riskreport ) * 100 ) / count( $view->risktasks ) ) > 20 ) { ?>
                                                    <img src="<?php echo esc_url( _HMWP_ASSETS_URL_ . 'img/speedometer_medium.png' ) ?>" alt="" style="max-width: 60%; margin: 10px auto;"/>
                                                    <div style="font-size: 1rem; font-style: italic; text-align: center; color: orangered;"><?php echo sprintf( esc_html__( "Your website security is still weak. %sSome of the main hacking doors are still available.", 'hide-my-wp' ), '<br />' ) ?></div>
												<?php } elseif ( ( ( count( $view->riskreport ) * 100 ) / count( $view->risktasks ) ) > 0 ) { ?>
                                                    <img src="<?php echo esc_url( _HMWP_ASSETS_URL_ . 'img/speedometer_better.png' ) ?>" alt="" style="max-width: 60%; margin: 10px auto;"/>
                                                    <div style="font-size: 1rem; font-style: italic; text-align: center; color: orangered;"><?php echo sprintf( esc_html__( "Your website security is getting better. %sJust make sure you complete all the security tasks.", 'hide-my-wp' ), '<br />' ) ?></div>
												<?php } else { ?>
                                                    <img src="<?php echo esc_url( _HMWP_ASSETS_URL_ . 'img/speedometer_high.png' ) ?>" alt="" style="max-width: 60%; margin: 10px auto;"/>
                                                    <div style="font-size: 1rem; font-style: italic; text-align: center; color: green;"><?php echo sprintf( esc_html__( "Your website security is strong. %sKeep checking the security every week.", 'hide-my-wp' ), '<br />' ) ?></div>
												<?php } ?>
                                            </div>
                                            <div class="col-sm-7 my-4">
                                                <form id="hmwp_securitycheck" method="POST">
													<?php wp_nonce_field( 'hmwp_securitycheck', 'hmwp_nonce' ) ?>
                                                    <input type="hidden" name="action" value="hmwp_securitycheck"/>

                                                    <button type="submit" class="btn rounded-0 btn-default btn-lg text-white px-5 "><?php echo esc_html__( 'Start Scan', 'hide-my-wp' ); ?></button>
                                                </form>

												<?php
												if ( ! empty( $view->report ) ) {
													$overview = array( 'success' => 0, 'warning' => 0, 'total' => 0 );
													foreach ( $view->report as $row ) {
														$overview['success'] += (int) $row['valid'];
														$overview['warning'] += (int) $row['warning'];
														$overview['total']   += 1;
													}
													echo '<table class="col-sm-12 mt-3 mb-0">';
													echo '<tbody>';
													echo '
                                            <tr>
                                                <td class="text-success border-right"><h6>' . esc_html__( 'Passed', 'hide-my-wp' ) . '</h6><h2>' . esc_html($overview['success']) . '</h2></td>
                                                <td class="text-danger"><h6>' . esc_html__( 'Failed', 'hide-my-wp' ) . '</h6><h2>' . esc_html( $overview['total'] - $overview['success'] ) . '</h2></td>
                                            </tr>';
													echo '</tbody>';
													echo '</table>';

													if ( ( $overview['total'] - $overview['success'] ) == 0 ) { ?>
                                                        <div class="text-center text-success font-weight-bold mt-4"><?php echo esc_html__( "Congratulations! You completed all the security tasks. Make sure you check your site once a week.", 'hide-my-wp' ) ?></div>
														<?php
													}
												}
												?>
                                            </div>
                                        </div>
									<?php } else { ?>
                                        <form id="hmwp_securitycheck" method="POST">
											<?php wp_nonce_field( 'hmwp_securitycheck', 'hmwp_nonce' ) ?>
                                            <input type="hidden" name="action" value="hmwp_securitycheck"/>

                                            <button type="submit" class="btn rounded-0 btn-default btn-lg text-white px-5 "><?php echo esc_html__( 'Start Scan', 'hide-my-wp' ); ?></button>
                                        </form>
									<?php } ?>

									<?php if ( isset( $view->securitycheck_time['timestamp'] ) ) { ?>
                                        <div class="text-center text-black-50 my-1">
                                            <strong><?php echo esc_html__( 'Last check:', 'hide-my-wp' ); ?></strong> <?php echo esc_html(gmdate( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), ( $view->securitycheck_time['timestamp'] + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) )); ?>
                                        </div>
									<?php } ?>
                                    <div class="text-center small mt-4 border-top text-black-50 pt-2"><?php echo sprintf( esc_html__( "According to %sGoogle latest stats%s, over %s 30k websites are hacked every day %s and %s over 30&#37; of them are made in WordPress %s. %s It's better to prevent an attack than to spend a lot of money and time to recover your data after an attack not to mention the situation when your clients' data are stolen.", 'hide-my-wp' ), '<a href="https://transparencyreport.google.com/safe-browsing/overview" target="_blank"><strong>', '</strong></a>', '<strong>', '</strong>', '<strong>', '</strong>', '<br />' ) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-3 p-0 input-group">
							<?php if ( ! empty( $view->report ) ) { ?>
                                <table class="table table_securitycheck border" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html__( 'Name', 'hide-my-wp' ) ?></th>
                                        <th scope="col"><?php echo esc_html__( 'Value', 'hide-my-wp' ) ?></th>
                                        <th scope="col"><?php echo esc_html__( 'Valid', 'hide-my-wp' ) ?></th>
                                        <th scope="col" colspan="2"><?php echo esc_html__( 'Action', 'hide-my-wp' ) ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php foreach ( $view->report as $index => $row ) { ?>
                                        <tr class="<?php echo( $row['valid'] ? 'task_passed' : 'task_failed' ) ?>" style="<?php echo( $row['valid'] ? 'display:none' : '' ) ?>">
                                            <td style="width: 30%; word-break: break-word;"><?php echo wp_kses_post( $row['name'] ) ?></td>
                                            <td style="width: 20%; font-weight: bold; word-break: break-word;"><?php echo wp_kses_post( $row['value'] ) ?></td>
                                            <td style="width: 30%; word-break: break-word;" class="<?php echo( $row['valid'] ? 'text-success' : 'text-danger' ) ?>"><?php echo( $row['valid'] ? '<i class="dashicons dashicons-yes mr-2" style="font-size: 1.6rem !important;"></i>' : '<i class="dashicons dashicons-no mr-2"  style="font-size: 1.6rem !important;"></i>' . ( isset( $row['solution'] ) ? wp_kses_post( $row['solution'] ) : '' ) ) ?></td>
                                            <td style="width: 18%; min-width: 100px; padding-right: 0!important; position: relative">
                                                <div class="modal" id="hmwp_securitydetail<?php echo esc_attr( $index ) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel"><?php echo wp_kses_post( $row['name'] ) ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body"><?php echo wp_kses_post( $row['message'] ) ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="btn btn-default rounded-0 px-3 float-right m-1" type="button" onclick="jQuery('#hmwp_securitydetail<?php echo esc_attr( $index ) ?>').modal('show');"><?php echo esc_html__( 'Info', 'hide-my-wp' ) ?></button>
												<?php
												if ( ! $row['valid'] && isset( $row['javascript'] ) && $row['javascript'] <> '' ) {
													?>
                                                    <button type="button" id="fix<?php echo esc_attr( $index ) ?>" class="btn btn-success mx-0 my-1 rounded-0 float-right  m-1" onclick="<?php echo esc_attr( $row['javascript'] ) ?>"><?php echo esc_html__( 'Fix it', 'hide-my-wp' ) ?></button> <?php
												} elseif ( $row['valid'] && isset( $row['javascript_undo'] ) && $row['javascript_undo'] <> '' ) {
													?>
                                                    <button type="button" class="btn btn-link mx-0 my-1 rounded-0 float-right  m-1" onclick="<?php echo esc_attr( $row['javascript_undo'] ) ?>"><?php echo esc_html__( 'Undo', 'hide-my-wp' ) ?></button> <?php
												} elseif ( $row['valid'] && isset( $row['javascript_custom'] ) && isset( $row['javascript_button'] ) && $row['javascript_custom'] <> '' ) {
													?>
                                                    <button type="button" class="btn btn-link mx-0 my-1 rounded-0 float-right  m-1" onclick="<?php echo esc_attr( $row['javascript_custom'] ) ?>"><?php echo wp_kses_post($row['javascript_button']) ?></button> <?php
												}
												?>
                                            </td>
                                            <td class="px-3" style="width: 50px; position: relative">
                                                <form class="hmwp_securityexclude_form" method="POST" style="position: absolute; top: 13px; right: 0;">
													<?php echo wp_nonce_field( 'hmwp_securityexclude', 'hmwp_nonce' ) ?>
                                                    <input type="hidden" name="action" value="hmwp_securityexclude"/>
                                                    <input type="hidden" name="name" value="<?php echo esc_attr( $index ) ?>"/>
                                                    <button type="submit" class="close my-2 mr-1" aria-label="Close" style="display: none" onclick="if (!confirm('<?php echo esc_html__( 'Are you sure you want to ignore this task in the future?', 'hide-my-wp' ) ?>')) {return false;}">
                                                        <span aria-hidden="true" title="<?php echo esc_attr__( 'Ignore security task', 'hide-my-wp' ) ?>">&times;</span>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
									<?php } ?>
                                    </tbody>
                                </table>

							<?php } ?>
                        </div>
                        <form id="hmwp_fixsettings_form" method="POST">
							<?php wp_nonce_field( 'hmwp_fixsettings', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_fixsettings"/>
                        </form>
                        <form id="hmwp_fixconfig_form" method="POST">
							<?php wp_nonce_field( 'hmwp_fixconfig', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_fixconfig"/>
                        </form>
                        <form id="hmwp_fixprefix_form" method="POST">
							<?php wp_nonce_field( 'hmwp_fixprefix', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_fixprefix"/>
                        </form>
                        <form id="hmwp_fixsalts_form" method="POST">
							<?php wp_nonce_field( 'hmwp_fixsalts', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_fixsalts"/>
                        </form>
                        <form id="hmwp_fixupgrade_form" method="POST">
							<?php wp_nonce_field( 'hmwp_fixupgrade', 'hmwp_nonce' ) ?>
                            <input type="hidden" name="action" value="hmwp_fixupgrade"/>
                        </form>

                        <div class="col-sm-12 text-right">
                            <form id="hmwp_resetexclude" method="POST">
								<?php wp_nonce_field( 'hmwp_resetexclude', 'hmwp_nonce' ) ?>
                                <input type="hidden" name="action" value="hmwp_resetexclude"/>

                                <button type="button" class="btn btn-light show_task_passed"><?php echo esc_html__( 'Show completed tasks', 'hide-my-wp' ) ?></button>
                                <button type="button" class="btn btn-light hide_task_passed" style="display: none"><?php echo esc_html__( 'Hide completed tasks', 'hide-my-wp' ) ?></button>
								<?php if ( get_option( HMWP_SECURITY_CHECK_IGNORE ) ) { ?>
                                    <button type="submit" class="btn btn-light"><?php echo esc_html__( 'Show ignored tasks', 'hide-my-wp' ) ?></button>
								<?php } ?>
                            </form>
                        </div>

						<?php do_action( 'hmwp_security_check_end' ) ?>

                    </div>

					<?php if ( apply_filters( 'hmwp_showaccount', true ) ) { ?>
                        <div class="col-sm-12 my-4 text-center">
                            <a href="<?php echo esc_url(HMWP_Classes_Tools::getCloudUrl( 'websites' )) ?>" target="_blank"><img src="<?php echo esc_url( _HMWP_ASSETS_URL_ . 'img/monitor_panel.png' ) ?>" alt="" style="width: 100%; max-width: 800px;"/></a>
                        </div>
					<?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="hmwp_security_mode_require_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><?php echo esc_html__( 'Ghost Mode', 'hide-my-wp' ) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

				<?php echo sprintf( esc_html__( 'First, you need to activate the %sSafe Mode%s or %sGhost Mode%s in %s', 'hide-my-wp' ), '<a href="' . esc_url(HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks' )) . '"><strong>', '</strong></a>', '<a href="' . esc_url(HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks' )) . '"><strong>', '</strong></a>', '<strong>' . esc_html(HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' )) . '</strong>' ) ?>

            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Cancel', 'hide-my-wp' ) ?></button>
                        <a href="<?php echo esc_url(HMWP_Classes_Tools::getSettingsUrl( 'hmwp_permalinks' )) ?>" type="button" class="btn btn-success"><?php echo esc_html__( 'Continue', 'hide-my-wp' ) ?> >></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hmwp_fixadmin_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><?php echo esc_html__( 'Admin Username', 'hide-my-wp' ) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?php echo esc_html__( "Change the user 'admin' or 'administrator' with another name to improve security.", 'hide-my-wp' ) ?></p>
                <p class="text-danger"><?php echo esc_html__( 'If you are connected with the admin user, you will have to re-login after the change.', 'hide-my-wp' ) ?></p>
                <form id="hmwp_fixadmin_form" method="POST">
                    <div class="input-group">
						<?php wp_nonce_field( 'hmwp_fixadmin', 'hmwp_nonce' ) ?>
                        <input type="hidden" name="action" value="hmwp_fixadmin"/>
                        <label for="hmwp_username" class="lable m-2">New Username</label>
                        <input id="hmwp_username" class="form-control nopopup" type="text" name="hmwp_username" value=""/>
                    </div>
                    <button type="button" onclick="jQuery(this).hmwp_fixAdmin(true);" class="btn btn-success my-3 rounded-0 btn-sm form-control" name="hmwp_username" value=""><?php echo esc_html__( 'Change', 'hide-my-wp' ) ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="hmwp_fixpermissions_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><?php echo esc_html__( 'Fix Permissions', 'hide-my-wp' ) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?php echo sprintf( esc_html__( "Even if the default paths are protected by %s after customization, we recommend setting the correct permissions for all directories and files on your website, use File Manager or FTP to check and change the permissions. %sRead more%s", 'hide-my-wp' ), esc_html(HMWP_Classes_Tools::getOption( 'hmwp_plugin_name' )), '<a href="' . esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/how-to-change-file-permissions-in-wordpress/" target="_blank">' ) . '">', '</a>' ) ?></p>

                <div class="mx-0 my-2">
					<?php echo esc_html__( 'WordPress Default Permissions', 'hide-my-wp' ) ?>:
                    <ol class="my-2" style="list-style: disc">
                        <li class="small text-black-50 m-0"><?php echo esc_html__( 'Directories', 'hide-my-wp' ) ?>: <?php echo esc_attr( sprintf( '%o', HMW_DIR_PERMISSION ) ); ?></li>
                        <li class="small text-black-50 m-0"><?php echo esc_html__( 'Files', 'hide-my-wp' ) ?>: <?php echo esc_attr( sprintf( '%o', HMW_FILE_PERMISSION ) ); ?></li>
                        <li class="small text-black-50 m-0"><?php echo esc_html__( 'Config', 'hide-my-wp' ) ?>: <?php echo esc_attr( sprintf( '%o', HMW_CONFIG_PERMISSION ) ); ?></li>
                    </ol>
                </div>

                <div class="m-0 py-3 border-top">
                    <form id="hmwp_fixpermissions_form" method="POST">
						<?php wp_nonce_field( 'hmwp_fixpermissions', 'hmwp_nonce' ) ?>
                        <input type="hidden" name="action" value="hmwp_fixpermissions"/>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-input" type="radio" name="value" id="quick" value="quick" checked>
                                <label class="form-label" for="quick">
									<?php echo esc_html__( 'Quick Fix', 'hide-my-wp' ) ?>
                                    <div class="small text-black-50"><?php echo esc_html__( 'Fix permission for the main directories and files (~ 5 sec)', 'hide-my-wp' ) ?></div>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-input" type="radio" name="value" id="complete" value="complete">
                                <label class="form-label" for="complete">
									<?php echo esc_html__( 'Complete Fix', 'hide-my-wp' ) ?>
                                    <div class="small text-black-50"><?php echo esc_html__( 'Fix permission for all directories and files (~ 1 min)', 'hide-my-wp' ) ?></div>

                                </label>
                            </div>
                            <button type="button" onclick="jQuery(this).hmwp_fixPermissions(true);" class="btn btn-success my-3 rounded-0 btn-sm form-control" name="hmwp_username" value=""><?php echo esc_html__( 'Fix it', 'hide-my-wp' ) ?></button>


                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
