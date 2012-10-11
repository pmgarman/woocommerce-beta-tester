<?php

/*
Plugin Name: WooCommerce Beta Tester
Plugin URI: http://www.patrickgarman.com/wordpress-plugins/woocommerce-beta-tester/
Description: Update your WooCommerce plugin straight from the GitHub repository and run the bleeding edge version. ** This is not recommended for production sites.
Version: 0.1
Author: Patrick Garman
Author URI: http://www.patrickgarman.com/
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_action('init', 'github_plugin_updater_test_init');
function github_plugin_updater_test_init() {

	require_once('classes/class-wc-github-updater.php');

	define('WP_GITHUB_FORCE_UPDATE', true);

	if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin

		new WC_GitHub_Updater( array() );
		
	}

}