<?php

// e11 Recommended Links
// Copyright (C) 2017 Eric Adolfson
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

/**
 * @package e11-recommended-links
 */
/*
Plugin Name: e11 Recommended Links
Plugin URI: https://epsilon11.com/e11-recommended-links
Description: Display links to websites recommended by the admin.
Version: 1.0
Author: er11
Author URI: https://epsilon11.com/wordpress-plugins
License: GPLv2 or later
Text Domain: e11-recommended-links
*/

define('E11_RECOMMENDED_LINKS_VERSION', '1.0');

// Don't run if called directly.

if (!function_exists('add_action')) {
  exit;
}

// Register activation hook.

register_activation_hook(__FILE__, array('e11RecommendedLinks', 'handle_activation'));

// Add hooks to init() of plugin classes for user and admin.

require_once(plugin_dir_path(__FILE__) . 'class.e11RecommendedLinks.php');

add_action('init', array('e11RecommendedLinks', 'init'));

if (is_admin()) {
  require_once(plugin_dir_path(__FILE__) . 'class.e11RecommendedLinksAdmin.php');

  add_action('init', array('e11RecommendedLinksAdmin', 'init'));
}
