<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );
if ( ! isset( $view ) ) {
	return;
} ?>
<noscript>
    <style>#hmwp_wrap .tab-panel:not(.tab-panel-first) {
            display: block
        }</style>
</noscript>
<div id="hmwp_wrap" class="d-flex flex-row p-0 my-3">
	<?php echo $view->getAdminTabs( HMWP_Classes_Tools::getValue( 'page', 'hmwp_advanced' ) ); ?>
    <div class="hmwp_row d-flex flex-row p-0 m-0">
        <div class="hmwp_col flex-grow-1 p-0 pr-2 mr-2 mb-3">
            <form method="POST">
				<?php wp_nonce_field( 'hmwp_firewall', 'hmwp_nonce' ) ?>
                <input type="hidden" name="action" value="hmwp_firewall"/>

				<?php do_action( 'hmwp_firewall_form_beginning' ) ?>

                <div id="firewall" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__( 'Firewall', 'hide-my-wp' ); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/firewall-security/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_sqlinjection" value="0"/>
                                    <input type="checkbox" id="hmwp_sqlinjection" name="hmwp_sqlinjection" class="switch"<?php echo( HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection' ) ? 'checked="checked"' : '' ) ?> value="1"/>
                                    <label for="hmwp_sqlinjection"><?php echo esc_html__( 'Firewall Against Script Injection', 'hide-my-wp' ); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/firewall-security/#ghost-activate-firewall-against-injections' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__( 'Most WordPress installations are hosted on the popular Apache, Nginx and IIS web servers.', 'hide-my-wp' ); ?></div>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__( 'A thorough set of rules can prevent many types of SQL Injection and URL hacks from being interpreted.', 'hide-my-wp' ); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light px-2 py-2 mx-0 my-3 hmwp_sqlinjection border-bottom">
                            <div class="col-sm-4 p-1">
                                <div class="font-weight-bold"><?php echo esc_html__( 'Firewall Strength', 'hide-my-wp' ); ?>:</div>
                                <div class="text-black-50 small"><?php echo sprintf( esc_html__( 'Learn more about %s 7G firewall %s.', 'hide-my-wp' ), '<a href="https://perishablepress.com/7g-firewall/" target="_blank">', '</a>' ); ?></div>
                                <div class="text-black-50 small"><?php echo sprintf( esc_html__( 'Learn more about %s 8G firewall %s.', 'hide-my-wp' ), '<a href="https://perishablepress.com/8g-firewall/" target="_blank">', '</a>' ); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group mb-1">
                                <select name="hmwp_sqlinjection_level" class="selectpicker form-control">
                                    <option value="1" <?php echo selected( 1, HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection_level' ) ) ?>><?php echo esc_html__( 'Minimal', 'hide-my-wp' ); ?></option>
                                    <option value="2" <?php echo selected( 2, HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection_level' ) ) ?>><?php echo esc_html__( 'Medium', 'hide-my-wp' ); ?></option>
                                    <option value="3" <?php echo selected( 3, HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection_level' ) ) ?>><?php echo esc_html__( '7G Firewall', 'hide-my-wp' ); ?></option>
                                    <option value="4" <?php echo selected( 4, HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection_level' ) ) ?>><?php echo esc_html__( '8G Firewall', 'hide-my-wp' ); ?></option>
                                </select>
                                <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/firewall-security/#ghost-8g-firewall' ) ?>" target="_blank" class="position-absolute float-right" style="right: 27px;top: 12%;"><i class="dashicons dashicons-editor-help"></i></a>
                            </div>

                        </div>

						<?php if ( HMWP_Classes_Tools::isApache() || HMWP_Classes_Tools::isLitespeed() ) { ?>
                            <div class="col-sm-12 row border-bottom border-light px-2 py-2 mx-0 my-3 hmwp_sqlinjection border-bottom">
                                <div class="col-sm-4 p-1">
                                    <div class="font-weight-bold"><?php echo esc_html__( 'Firewall Location', 'hide-my-wp' ); ?>:</div>
                                    <div class="text-black-50 small"><?php echo esc_html__( 'Where to add the firewall rules.', 'hide-my-wp' ); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 input-group mb-1">
                                    <select name="hmwp_sqlinjection_location" class="selectpicker form-control">
                                        <option value="onload" <?php echo selected( 'onload', HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection_location' ) ) ?>><?php echo esc_html__( 'On website initialization', 'hide-my-wp' ); ?> (<?php echo esc_html__( 'recommended', 'hide-my-wp' ); ?>)</option>
                                        <option value="file" <?php echo selected( 'file', HMWP_Classes_Tools::getOption( 'hmwp_sqlinjection_location' ) ) ?>><?php echo esc_html__( 'In .htaccess file', 'hide-my-wp' ); ?></option>
                                    </select>
                                </div>

                            </div>
						<?php } else { ?>
                            <input type="hidden" name="hmwp_sqlinjection_location" value="onload"/>
						<?php } ?>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_hide_unsafe_headers" value="0"/>
                                    <input type="checkbox" id="hmwp_hide_unsafe_headers" name="hmwp_hide_unsafe_headers" class="switch"<?php echo( HMWP_Classes_Tools::getOption( 'hmwp_hide_unsafe_headers' ) ? 'checked="checked"' : '' ) ?> value="1"/>
                                    <label for="hmwp_hide_unsafe_headers"><?php echo esc_html__( 'Remove Unsafe Headers', 'hide-my-wp' ); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/firewall-security/#ghost-remove-unsafe-headers' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__( 'Remove PHP version, Server info, Server Signature from header.', 'hide-my-wp' ); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_detectors_block" value="0"/>
                                    <input type="checkbox" id="hmwp_detectors_block" name="hmwp_detectors_block" class="switch"<?php echo( HMWP_Classes_Tools::getOption( 'hmwp_detectors_block' ) ? 'checked="checked"' : '' ) ?> value="1"/>
                                    <label for="hmwp_detectors_block"><?php echo esc_html__( 'Block Theme Detectors Crawlers', 'hide-my-wp' ); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/firewall-security/#ghost-block-theme-detectors' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__( 'Block known Users-Agents from popular Theme Detectors.', 'hide-my-wp' ); ?></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    </div>
                </div>

                <div id="header" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__( 'Header Security', 'hide-my-wp' ); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/header-security/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_security_header" value="0"/>
                                    <input type="checkbox" id="hmwp_security_header" name="hmwp_security_header" class="switch"<?php echo( HMWP_Classes_Tools::getOption( 'hmwp_security_header' ) ? 'checked="checked"' : '' ) ?> value="1"/>
                                    <label for="hmwp_security_header"><?php echo esc_html__( 'Add Security Headers for XSS and Code Injection Attacks', 'hide-my-wp' ); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/header-security/#ghost-activate-header-security' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                </div>
                                <div class="text-black-50 col-sm-12 p-0 ml-5"><?php echo esc_html__( "Add Strict-Transport-Security header", 'hide-my-wp' ); ?>
                                    <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security" target="_blank"><?php echo esc_html__( 'more details', 'hide-my-wp' ) ?></a>
                                </div>
                                <div class="text-black-50 col-sm-12 p-0 ml-5"><?php echo esc_html__( "Add Content-Security-Policy header", 'hide-my-wp' ); ?>
                                    <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP" target="_blank"><?php echo esc_html__( 'more details', 'hide-my-wp' ) ?></a>
                                </div>
                                <div class="text-black-50 col-sm-12 p-0 ml-5"><?php echo esc_html__( "Add X-XSS-Protection header", 'hide-my-wp' ); ?>
                                    <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection" target="_blank"><?php echo esc_html__( 'more details', 'hide-my-wp' ) ?></a>
                                </div>
                                <div class="text-black-50 col-sm-12 p-0 ml-5"><?php echo esc_html__( "Add X-Content-Type-Options header", 'hide-my-wp' ); ?>
                                    <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options" target="_blank"><?php echo esc_html__( 'more details', 'hide-my-wp' ) ?></a>
                                </div>

                            </div>

                            <div class="col-sm-12 row py-4 border-bottom hmwp_security_header">
                                <input type="hidden" class="form-control w-100" name="hmwp_security_headers[]" value=""/>
								<?php
								$headers = (array) HMWP_Classes_Tools::getOption( 'hmwp_security_headers' );
								$help    = array(
									"Strict-Transport-Security"       => array(
										"title"   => "Tells browsers that it should only be accessed using HTTPS, instead of using HTTP.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security",
										"default" => "max-age=63072000"
									), "Content-Security-Policy"      => array(
										"title"   => "Adds layer of security that helps to detect and mitigate certain types of attacks, including Cross-Site Scripting (XSS) and data injection attacks.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP",
										"default" => "object-src 'none'"
									), "X-XSS-Protection"             => array(
										"title"   => "Stops pages from loading when they detect reflected cross-site scripting (XSS) attacks.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection",
										"default" => "1; mode=block"
									), "X-Content-Type-Options"       => array(
										"title"   => "Blocks content sniffing that could transform non-executable MIME types into executable MIME types.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options",
										"default" => "nosniff"
									), "Cross-Origin-Embedder-Policy" => array(
										"title"   => "Prevents a document from loading any cross-origin resources that don't explicitly grant the document permission.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Embedder-Policy",
										"default" => "unsafe-none"
									), "Cross-Origin-Opener-Policy"   => array(
										"title"   => "Allows you to ensure a top-level document does not share a browsing context group with cross-origin documents.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Opener-Policy",
										"default" => "unsafe-none"
									), "X-Frame-Options"              => array(
										"title"   => "Can be used to indicate whether or not a browser should be allowed to render a page in a frame, iframe, embed, object.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options",
										"default" => "SAMEORIGIN"
									), "Permissions-Policy"           => array(
										"title"   => "Provides a mechanism to allow and deny the use of browser features in its own frame, and in content within any iframe elements in the document.",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy",
										"default" => "interest-cohort=(), accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=(), interest-cohort=()"
									), "Referrer-Policy"              => array(
										"title"   => "HTTP header controls how much referrer information (sent with the Referer header) should be included with requests..",
										"link"    => "https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy",
										"default" => "origin-when-cross-origin"
									),
								); ?>

                                <div class="col-sm-12 m-0 p-0 hmwp_security_headers">
									<?php foreach ( $headers as $name => $value ) {
										if ( $value == '' ) {
											continue;
										}
										?>
                                        <div class="col-sm-12 row pb-3 m-0 my-1 border-0">
                                            <div class="hmwp_security_header_remove" onclick="jQuery(this).parent().remove()" title="<?php echo esc_attr__( 'Remove' ) ?>">x</div>
                                            <div class="col-sm-4 p-0 my-2 font-weight-bold">
												<?php echo esc_html( $name ) ?>:
												<?php if ( isset( $help[ $name ]['default'] ) ) { ?>
                                                    <div class="text-black-50 small"><?php echo esc_html__( 'default', 'hide-my-wp' ) . ': ' . esc_html( $help[ $name ]['default'] ); ?></div>
												<?php } ?>
                                            </div>
                                            <div class="col-sm-8 p-0">
                                                <div class=" input-group">
                                                    <input type="text" class="form-control w-100" name="hmwp_security_headers[<?php echo esc_attr( $name ) ?>]" value="<?php echo esc_attr( $value ) ?>"/>
													<?php if ( isset( $help[ $name ]['link'] ) ) { ?>
                                                        <a href="<?php echo esc_url( $help[ $name ]['link'] ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 10px;"><i class="dashicons dashicons-editor-help"></i></a>
													<?php } ?>
                                                </div>
												<?php if ( isset( $help[ $name ]['title'] ) ) { ?>
                                                    <div class="text-black-50 small"><?php echo esc_html( $help[ $name ]['title'] ); ?></div>
												<?php } ?>
                                            </div>

                                        </div>
									<?php } ?>
                                </div>

								<?php if ( count( $help ) > ( count( $headers ) - 1 ) ) { ?>
                                    <div class="col-sm-12 row pb-3 m-0 my-1 border-0 hmwp_security_headers_new">

										<?php foreach ( $help as $name => $value ) {
											if ( ! in_array( $name, array_keys( $headers ), true ) ) {
												?>
                                                <div class="col-sm-12 row pb-3 m-0 my-1 border-0 <?php echo esc_attr( $name ) ?>" style="display: none">
                                                    <div class="hmwp_security_header_remove" onclick="jQuery(this).parent().remove()" title="<?php echo esc_attr__( 'Remove' ) ?>">x</div>
                                                    <div class="col-sm-4 p-0 my-2 font-weight-bold">
														<?php echo esc_html( $name ) ?>:
														<?php if ( isset( $value['default'] ) ) { ?>
                                                            <div class="text-black-50 small"><?php echo esc_html__( 'default', 'hide-my-wp' ) . ': ' . esc_html( $value['default'] ); ?></div>
														<?php } ?>
                                                    </div>
                                                    <div class="col-sm-8 p-0 input-group">
                                                        <input type="text" class="form-control w-100"/>
														<?php if ( isset( $value['link'] ) ) { ?>
                                                            <a href="<?php echo esc_url( $value['link'] ) ?>" target="_blank" class="position-absolute float-right" style="right: 7px;top: 10px;"><i class="dashicons dashicons-editor-help"></i></a>
														<?php } ?>

														<?php if ( isset( $value['title'] ) ) { ?>
                                                            <div class="text-black-50 small"><?php echo esc_html( $value['title'] ); ?></div>
														<?php } ?>
                                                    </div>

                                                </div>
											<?php }
										} ?>


                                        <div class="col-sm-4 p-0 my-2 font-weight-bold">
											<?php echo esc_html__( 'Add Security Header', 'hide-my-wp' ); ?>
                                        </div>
                                        <div class="col-sm-8 p-0 input-group">
                                            <select id="hmwp_security_headers_new" class=" form-control mb-1">
                                                <option value=""></option>
												<?php
												foreach ( $help as $name => $value ) {
													if ( ! in_array( $name, array_keys( $headers ), true ) ) {
														echo '<option value="' . esc_attr( $value['default'] ) . '" >' . esc_html( $name ) . '</option>';
													}
												}
												?>
                                            </select>
                                        </div>

                                    </div>
								<?php } ?>
                                <div class="col-sm-12 alert-danger text-center mt-3 p-2 small"><?php echo esc_html__( "Changing the predefined security headers may affect the website funtionality.", 'hide-my-wp' ); ?>
                                    <br/><?php echo esc_html__( "Make sure you know what you do when changing the headers.", 'hide-my-wp' ); ?>
                                </div>
                                <div class="col-sm-12 text-center mt-3 small"><?php echo esc_html__( "Test your website headers with", 'hide-my-wp' ); ?>
                                    <a href="https://securityheaders.com/?q=<?php echo esc_url(home_url()) ?>" target="_blank">securityheaders.com</a>
                                </div>

                            </div>

                        </div>

                    </div>
                    </div>
                </div>

                <div id="geoblock" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__( 'Geo Security', 'hide-my-wp' ); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/geo-security-country-blocking/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">

                        <div class="col-sm-12 row mb-1 ml-1 p-2">
                            <div class="checker col-sm-12 row my-2 py-1">
                                <div class="col-sm-12 p-0 switch switch-sm">
                                    <input type="hidden" name="hmwp_geoblock" value="0"/>
                                    <input type="checkbox" id="hmwp_geoblock" name="hmwp_geoblock" class="switch"<?php echo( HMWP_Classes_Tools::getOption( 'hmwp_geoblock' ) ? 'checked="checked"' : '' ) ?> value="1"/>
                                    <label for="hmwp_geoblock"><?php echo esc_html__( 'Country Blocking', 'hide-my-wp' ); ?>
                                        <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/geo-security-country-blocking/#ghost-activate-country-blocking' ) ?>" target="_blank" class="d-inline ml-1"><i class="dashicons dashicons-editor-help d-inline"></i></a>
                                    </label>
                                    <div class="text-black-50 ml-5"><?php echo esc_html__( 'Geographic Security is a feature designed to stops attacks from different countries, and to put an end to harmful activity that comes from specific regions.', 'hide-my-wp' ); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light px-2 py-2 mx-0 my-3 border-bottom hmwp_geoblock">
                            <div class="col-sm-4 p-1">
                                <div class="font-weight-bold"><?php echo esc_html__( 'Block Countries', 'hide-my-wp' ); ?>:</div>
                                <div class="text-black-50 small"><?php echo esc_html__( 'Choose the countries where access to the website should be restricted.', 'hide-my-wp' ); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 mb-1">
                                <select name="hmwp_geoblock_countries[]" class="form-control  selectpicker" multiple data-live-search="true">
									<?php
									//get all countries and codes
									$countries = HMWP_Classes_ObjController::getClass( 'HMWP_Models_Geoip_GeoLocator' )->getCountryCodes();

									//get blocked countries
									$blocked_countries = HMWP_Classes_Tools::getOption( 'hmwp_geoblock_countries' );
									if ( ! empty( $blocked_countries ) && ! is_array( $blocked_countries ) ) {
										$blocked_countries = json_decode( $blocked_countries, true );
									}

									//show all countries and block countries
									foreach ( $countries as $code => $country ) {
										echo '<option value="' . esc_attr($code) . '" ' . selected( true, in_array( $code, (array) $blocked_countries ) ) . '>' . esc_html($country) . '</option>';
									}
									?>
                                </select>
                                <div class="col-sm-12 m-0 px-0">
                                    <input type="checkbox" id="hmwp_geoblock_selectall">
                                    <label for="hmwp_geoblock_selectall"><?php echo esc_html__( 'Select all', 'hide-my-wp' ); ?></label>
                                </div>

                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 px-2 mx-1 my-3 hmwp_geoblock">
                            <div class="col-md-4 p-0 font-weight-bold ">
								<?php echo esc_html__( 'Block Specific Paths', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'Add paths that will be blocked for the selected countries.', 'hide-my-wp' ) ?></div>
                                <div class="small text-black-50"><?php echo esc_html__( 'Leave it blank to block all paths for the selected countries.', 'hide-my-wp' ) ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group">
								<?php
								$geoblock_urls = HMWP_Classes_Tools::getOption( 'hmwp_geoblock_urls' );
								if ( ! empty( $geoblock_urls ) ) {
									$geoblock_urls = json_decode( $geoblock_urls, true );
								}
								?>
                                <textarea type="text" class="form-control " name="hmwp_geoblock_urls" style="height: 100px"><?php echo esc_html( ! empty( $geoblock_urls ) ? implode( PHP_EOL, $geoblock_urls ) : '' ) ?></textarea>
                                <div class="small text-black-50 col-md-12 py-2 px-0"><?php echo esc_html__( 'e.g. /post-type/ will block all path starting with /post-type/', 'hide-my-wp' ) ?></div>
                            </div>
                        </div>


						<?php if ( ! empty( $blocked_countries ) ) { ?>
                            <div class="col-sm-12 row border-bottom border-light px-2 py-2 mx-0 my-3 border-bottom hmwp_geoblock">
                                <div class="col-sm-4 p-1">
                                    <div class="font-weight-bold"><?php echo esc_html__( 'Selected Countries', 'hide-my-wp' ); ?>:</div>
                                    <div class="text-black-50 small"><?php echo esc_html__( 'Here is the list of select counties where your website will be restricted..', 'hide-my-wp' ); ?></div>
                                </div>
                                <div class="col-sm-8 p-0 mb-1">
                                    <ul class="row">
										<?php

										//show all countries and block countries
										foreach ( $countries as $code => $country ) {
											if ( in_array( $code, $blocked_countries ) ) {
												echo '<li class="font-weight-bold ' . ( count( (array) $blocked_countries ) > 6 ? 'col-4' : 'col-12' ) . '">' . esc_html($country) . '</li>';
											}
										}
										?>
                                    </ul>
                                </div>
                            </div>
						<?php } ?>
                    </div>
                    </div>
                </div>

                <div id="whitelist" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__( 'Whitelist', 'hide-my-wp' ); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/whitelist-ips-paths/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-md-4 p-0 font-weight-bold">
								<?php echo esc_html__( 'Whitelist IPs', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'Add IP addresses that can pass plugin security.', 'hide-my-wp' ) ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group pl-2">
								<?php
								$whitelist_ip = HMWP_Classes_Tools::getOption( 'whitelist_ip' );
								if ( ! empty( $whitelist_ip ) ) {
									$whitelist_ip = json_decode( $whitelist_ip, true );
								}
								?>
                                <textarea type="text" class="form-control " name="whitelist_ip" style="height: 100px"><?php echo esc_html( ! empty( $whitelist_ip ) ? implode( PHP_EOL, $whitelist_ip ) : '' ) ?></textarea>
                                <div class="small text-black-50 col-md-12 pt-2 px-0"><?php echo sprintf( esc_html__( 'You can white-list a single IP address like 192.168.0.1 or a range of 245 IPs like 192.168.0.*. Find your IP with %s', 'hide-my-wp' ), '<a href="https://whatismyipaddress.com/" target="_blank">https://whatismyipaddress.com/</a>' ) ?></div>
	                            <?php
	                            $domain = ( HMWP_Classes_Tools::isMultisites() && defined( 'BLOG_ID_CURRENT_SITE' ) ) ? get_home_url( BLOG_ID_CURRENT_SITE ) : site_url();
	                            if( $ip = @gethostbyname( wp_parse_url($domain, PHP_URL_HOST) ) ) { ?>
                                    <div class="small text-black-50 col-md-12 pt-1 px-0"><?php echo sprintf( esc_html__( 'To whitelist your website IP address, add: %s', 'hide-my-wp' ), '<strong>'.$ip.'</strong>' ) ?></div>
	                            <?php } ?>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-md-4 p-0 font-weight-bold ">
								<?php echo esc_html__( 'Whitelist Paths', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'Add paths that can pass plugin security', 'hide-my-wp' ) ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group pl-2">
								<?php
								$whitelist_urls = HMWP_Classes_Tools::getOption( 'whitelist_urls' );
								if ( ! empty( $whitelist_urls ) ) {
									$whitelist_urls = json_decode( $whitelist_urls, true );
								}
								?>
                                <textarea type="text" class="form-control " name="whitelist_urls" style="height: 100px"><?php echo esc_html( ! empty( $whitelist_urls ) ? implode( PHP_EOL, $whitelist_urls ) : '' ) ?></textarea>
                                <div class="small text-black-50 col-md-12 py-2 px-0"><?php echo esc_html__( 'e.g. /cart/ will whitelist all path starting with /cart/', 'hide-my-wp' ) ?></div>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3 border-bottom">
                            <div class="col-sm-4 p-1">
                                <div class="font-weight-bold"><?php echo esc_html__( 'Whitelist Options', 'hide-my-wp' ); ?></div>
                                <div class="text-black-50 small"><?php echo esc_html__( 'Chose what to do when accessing from whitelist IP addresses and whitelisted paths.', 'hide-my-wp' ); ?></div>
                            </div>
                            <div class="col-sm-8 p-0 input-group mb-1 pl-2">
                                <select name="whitelist_level" class="selectpicker form-control">
                                    <option value="0" <?php echo selected( 0, HMWP_Classes_Tools::getOption( 'whitelist_level' ) ) ?> ><?php echo esc_html__( 'Allow Hidden Paths', 'hide-my-wp' ); ?></option>
                                    <option value="1" <?php echo selected( 1, HMWP_Classes_Tools::getOption( 'whitelist_level' ) ) ?> ><?php echo esc_html__( 'Show Default Paths & Allow Hidden Paths', 'hide-my-wp' ); ?></option>
                                    <option value="2" <?php echo selected( 2, HMWP_Classes_Tools::getOption( 'whitelist_level' ) ) ?> ><?php echo esc_html__( 'Show Defaults Paths & Allow Everything', 'hide-my-wp' ); ?></option>
                                </select>
                            </div>

                        </div>

                    </div>
                    </div>
                </div>

                <div id="blacklist" class="col-sm-12 p-0 m-0 tab-panel">
                    <div class="card col-sm-12 p-0 m-0">
                        <h3 class="card-title hmwp_header p-2 m-0"><?php echo esc_html__( 'Blacklist', 'hide-my-wp' ); ?>
                            <a href="<?php echo esc_url( HMWP_Classes_Tools::getOption('hmwp_plugin_website') . '/kb/blacklist/' ) ?>" target="_blank" class="d-inline-block float-right mr-2" style="color: white"><i class="dashicons dashicons-editor-help"></i></a>
                        </h3>
                        <div class="card-body">
                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-md-4 p-0 font-weight-bold">
								<?php echo esc_html__( 'Blacklist IPs', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'Add  IP addresses that should always be blocked from accessing this website.', 'hide-my-wp' ) ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group pl-2">
								<?php
								$banlist_ip = HMWP_Classes_Tools::getOption( 'banlist_ip' );
								if ( ! empty( $banlist_ip ) ) {
									$banlist_ip = json_decode( $banlist_ip, true );
								}
								?>
                                <textarea type="text" class="form-control " name="banlist_ip"><?php echo esc_html( ! empty( $banlist_ip ) ? implode( PHP_EOL, $banlist_ip ) : '' ) ?></textarea>
                                <div class="small text-black-50 col-md-12 py-2 px-0"><?php echo esc_html__( 'You can ban a single IP address like 192.168.0.1 or a range of 245 IPs like 192.168.0.*. These IPs will not be able to access the login page.', 'hide-my-wp' ) ?></div>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-md-4 p-0 font-weight-bold">
								<?php echo esc_html__( 'Block User Agents', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. acapbot' ); ?></div>
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. gigabot' ); ?></div>
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. alexibot' ); ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group pl-2">
								<?php
								$user_agents = HMWP_Classes_Tools::getOption( 'banlist_user_agent' );
								if ( ! empty( $user_agents ) ) {
									$user_agents = json_decode( $user_agents, true );
								}
								?>
                                <textarea type="text" class="form-control " name="banlist_user_agent"><?php echo esc_html( ! empty( $user_agents ) ? implode( PHP_EOL, $user_agents ) : '' ) ?></textarea>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-md-4 p-0 font-weight-bold">
								<?php echo esc_html__( 'Block Referrer', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. xanax.com' ); ?></div>
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. badsite.com' ); ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group pl-2">
								<?php
								$referrers = HMWP_Classes_Tools::getOption( 'banlist_referrer' );
								if ( ! empty( $referrers ) ) {
									$referrers = json_decode( $referrers, true );
								}
								?>
                                <textarea type="text" class="form-control " name="banlist_referrer"><?php echo esc_html( ! empty( $referrers ) ? implode( PHP_EOL, $referrers ) : '' ) ?></textarea>
                            </div>
                        </div>

                        <div class="col-sm-12 row border-bottom border-light py-3 mx-0 my-3">
                            <div class="col-md-4 p-0 font-weight-bold">
								<?php echo esc_html__( 'Block Hostnames', 'hide-my-wp' ); ?>:
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. *.colocrossing.com' ); ?></div>
                                <div class="small text-black-50"><?php echo esc_html__( 'e.g. kanagawa.com' ); ?></div>
                            </div>
                            <div class="col-md-8 p-0 input-group input-group pl-2">
								<?php
								$hostnames = HMWP_Classes_Tools::getOption( 'banlist_hostname' );
								if ( ! empty( $hostnames ) ) {
									$hostnames = json_decode( $hostnames, true );
								}
								?>
                                <textarea type="text" class="form-control " name="banlist_hostname"><?php echo esc_html( ! empty( $hostnames ) ? implode( PHP_EOL, $hostnames ) : '' ) ?></textarea>
                                <div class="col-12 px-0 py-2 small text-danger"><?php echo esc_html__( 'Resolving hostnames may affect the website loading speed.' ); ?></div>
                            </div>
                        </div>

                    </div>
                    </div>
                </div>

				<?php do_action( 'hmwp_firewall_form_end' ) ?>

                <div class="col-sm-12 m-0 p-2 bg-light text-center" style="position: fixed; bottom: 0; right: 0; z-index: 100; box-shadow: 0 0 8px -3px #444;">
                    <button type="submit" class="btn rounded-0 btn-success px-5 mr-5 save"><?php echo esc_html__( 'Save', 'hide-my-wp' ); ?></button>
                </div>
            </form>

        </div>

        <div class="hmwp_col hmwp_col_side p-0 pr-2 mr-2">
			<?php $view->show( 'blocks/SecurityCheck' ); ?>
        </div>
    </div>
