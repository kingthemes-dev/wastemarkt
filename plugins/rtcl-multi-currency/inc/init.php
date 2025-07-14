<?php
defined( 'ABSPATH' ) || exit;
require_once 'utils/constants.php';
require_once 'helpers/functions.php';

require_once 'Models/RtclMc_Dependencies.php';
$dependence = Dependencies::getInstance();

if ( $dependence->check() ) {
	require_once 'Models/RtclMc_Data.php';

	if ( is_admin() ) {
		require_once 'admin/RtclMc_Admin.php';
	}
	if ( RtclMc_Data::instance()->get_enable() ) {
		if ( 'static' === RtclMc_Data::instance()->get_type() ) {
			require_once 'static/RtclMcStaticHooks.php';
			require_once 'static/RtclMcStaticScripts.php';
		}
		if ( 'dynamic' === RtclMc_Data::instance()->get_type() ) {
			require_once 'hooks/RtclMcDynamicHooks.php';

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				require_once 'frontend/RtclMc_Frontend_Update.php';
				require_once 'frontend/RtclMc_Frontend_Display.php';
				require_once 'frontend/RtclMc_Frontend_Switcher.php';
				require_once 'frontend/RtclMc_Frontend_Price_Filters.php';
			}
		}
	}


}
