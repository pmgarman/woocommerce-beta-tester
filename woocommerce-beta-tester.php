<?php

/*
Plugin Name: WooCommerce Beta Tester
Plugin URI: http://www.patrickgarman.com/wordpress-plugins/woocommerce-beta-tester/
Description: Update your WooCommerce plugin straight from the GitHub repository and run the bleeding edge version. ** This is not recommended for production sites.
Version: 0.2
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

add_action('init', 'wc_beta_tester_init');
function wc_beta_tester_init() {
	require_once('classes/class-wc-github-updater.php');
	if( is_admin() )
		new WC_GitHub_Updater();
}