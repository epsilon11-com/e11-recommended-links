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

class e11RecommendedLinks {
  private static $initialized = false;

  /**
   * Plugin initialization.
   * @static
   */
  public static function init() {

    global $wpdb;

    // Ensure function is called only once.

    if (self::$initialized) {
      return;
    }

    self::$initialized = true;

    // Load stylesheet for plugin.

    wp_register_style('e11-recommended-links.css',
                        plugin_dir_url(__FILE__) . 'css/e11-recommended-links.css',
                        array(),
                        E11_RECOMMENDED_LINKS_VERSION);

    wp_enqueue_style('e11-recommended-links.css');
  }

  /**
   * Retrieve links within the date range of the current page of posts,
   * formatted for display within the content area of an index page.
   *
   * @static
   * @param $attr array Associative array of shortcode attributes keyed by name
   * @return string HTML of relevant links, or empty string if none found.
   */
  public static function links_shortcode($attr) {
    // [TODO] Determine date range of posts on current page.
    // [TODO] Use range to select links from table.
    // [TODO] Format links and return HTML.
  }
}

// Add plugin to WordPress hooks.

add_shortcode('e11_recommended_links',
                          array('e11RecommendedLinks', 'links_shortcode'));

