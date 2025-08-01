<?php
/*
TranslatePress - SEO Pack Add-on

License: GPL2

== Copyright ==
Copyright 2020 Cozmoslabs (www.cozmoslabs.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();


function trp_in_sp_run(){

	
    require_once plugin_dir_path( __FILE__ ) . 'class-seo-pack.php';
    if ( class_exists( 'TRP_Translate_Press' ) ) {
        new TRP_IN_Seo_Pack();
    }

}
add_action( 'plugins_loaded', 'trp_in_sp_run', 0 );

