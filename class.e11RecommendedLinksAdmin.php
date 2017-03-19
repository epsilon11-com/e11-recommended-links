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

class e11RecommendedLinksAdmin {
  private static $initialized = false;
  private static $linksTableName;

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

    wp_register_style('e11-recommended-links-admin.css',
                        plugin_dir_url(__FILE__) . 'css/e11-recommended-links-admin.css',
                        array(),
                        E11_RECOMMENDED_LINKS_VERSION);

    wp_enqueue_style('e11-recommended-links-admin.css');

    // Set table name for recommended links.

    self::$linksTableName = $wpdb->prefix . 'e11_recommended_links';

    // Trigger update procedure on version change.

    if (get_option('e11_recommended_links_version')
                                      != E11_RECOMMENDED_LINKS_VERSION) {
      self::perform_update();
    }
  }

  /**
   * Create/update database table(s) if plugin was just installed or upgraded
   * to a new version.
   *
   * @static
   */
  private static function perform_update() {

    // Create recommended links table.

    $sql = 'CREATE TABLE ' . self::$linksTableName . ' (
		    id integer NOT NULL AUTO_INCREMENT,
		    created datetime DEFAULT "0000-00-00 00:00:00" NOT NULL,
		    display_mode tinyint NOT NULL DEFAULT 1, 
		    url varchar(1024) NOT NULL,
		    description text NOT NULL,
		    
		    PRIMARY KEY (id)
      );';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Update plugin version.

    update_option('e11_recommended_links_version',
      E11_RECOMMENDED_LINKS_VERSION);
  }
}
