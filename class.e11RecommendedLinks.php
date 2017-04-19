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

    wp_register_style('e11-recommended-links.css',
                        plugin_dir_url(__FILE__) . 'css/e11-recommended-links.css',
                        array(),
                        E11_RECOMMENDED_LINKS_VERSION);

    wp_enqueue_style('e11-recommended-links.css');

    // Set table name for recommended links.

    self::$linksTableName = $wpdb->prefix . 'e11_recommended_links';
  }

  /**
   * Handle plugin activation, adding custom capability
   * 'manage_e11_recommended_links' to the 'Administrator' role.
   */
  public static function handle_activation() {
    $role = get_role('administrator');

    $role->add_cap('manage_e11_recommended_links');
  }

  /**
   * Generate HTML to display recommended links within a list of posts.
   *
   * [TODO] Ensure the timezones of recommended links match WordPress
   *        config / timezones of posts.
   */
  public static function display_links() {
    global $wp_query;
    global $wpdb;

    // Verify one or more posts are present on the page.

    if (count($wp_query->posts) == 0) {
      return;
    }

    // Read options from WordPress settings, setting defaults if not found.

    $label = __('Recommended links', 'e11-recommended-links');
    $date_format = 'M. d';

    $options = get_option('e11_recommended_links_options', array());

    if (isset($options['e11_recommended_links_field_label'])) {
      $label = $options['e11_recommended_links_field_label'];
    }

    if (isset($options['e11_recommended_links_field_date_format'])) {
      $date_format = $options['e11_recommended_links_field_date_format'];
    }

    // Get current page and number of pages.

    $cur_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $num_pages = $wp_query->max_num_pages;

    // Determine query sort parameters.  Don't display links if posts aren't
    // being sorted by post date.  Otherwise, note whether the posts are being
    // displayed in ascending or descending order.

    $order = get_query_var('order');
    $order_by = get_query_var('orderby');

    if (!empty($order_by) && $order_by != 'post_date' && $order_by != 'date') {
      return;
    }

    if (strcasecmp($order, 'asc') == 0) {
      $sortAscending = true;
    } else {
      $sortAscending = false;
    }

    // Set from/to date for link query.

    $date_from = null;
    $date_to = null;

    if ($num_pages == 1) {
      // Select all links if only one page of posts exists.

    } elseif ($sortAscending == false) {
      if ($cur_page != $num_pages) {
        // If not the last page, show all links between the newest post and
        // the first published post after the oldest post.

        $date = get_the_date('Y-m-d H:i:s',
          $wp_query->posts[count($wp_query->posts) - 1]);

        $query = "
              SELECT $wpdb->posts.post_date 
              FROM $wpdb->posts 
              WHERE $wpdb->posts.post_date < '$date'
              AND $wpdb->posts.post_type = 'post'
              AND $wpdb->posts.post_status = 'publish'
              ORDER BY $wpdb->posts.post_date
              LIMIT 1
          ";

        $date = $wpdb->get_var($query);

        if ($date !== null) {
          $date_from = $date;
        }

        // Allow links that are newer than published posts to be
        // displayed on the first page.

        if ($cur_page != 1) {
          $date_to = get_the_date('Y-m-d H:i:s', $wp_query->posts[0]);
        }

      } else {
        // If on the last page, show all links older than the newest post
        // on the page.

        $date_to = get_the_date('Y-m-d H:i:s', $wp_query->posts[0]);
      }
    } else { // $sortAscending == true
      if ($cur_page != $num_pages) {
        // If not the last page, show all links between the oldest post and
        // the first published post after the newest post.

        $date = get_the_date('Y-m-d H:i:s',
          $wp_query->posts[count($wp_query->posts) - 1]);

        $query = "
              SELECT $wpdb->posts.post_date 
              FROM $wpdb->posts 
              WHERE $wpdb->posts.post_date > '$date'
              AND $wpdb->posts.post_type = 'post'
              AND $wpdb->posts.post_status = 'publish'
              ORDER BY $wpdb->posts.post_date
              LIMIT 1
          ";

        $date = $wpdb->get_var($query);

        if ($date !== null) {
          $date_to = $date;
        }

        // Allow links that are older than published posts to be
        // displayed on the first page.

        if ($cur_page != 1) {
          $date_from = get_the_date('Y-m-d H:i:s', $wp_query->posts[0]);
        }

      } else {
        // If on the last page, show all links newer than the oldest post
        // on the page.

        $date_from = get_the_date('Y-m-d H:i:s', $wp_query->posts[0]);
      }
    }

    // Build "WHERE" statement for link query.

    $where = ' WHERE (display_mode = 1 OR display_mode = 3)';

    if ($date_from !== null) {
      if ($date_to !== null) {
        $where .= $wpdb->prepare(' AND created > %s AND created <= %s',
          array($date_from, $date_to));
      } else {
        $where .= $wpdb->prepare(' AND created > %s', array($date_from));
      }
    } else {
      if ($date_to !== null) {
        $where .= $wpdb->prepare(' AND created <= %s', array($date_to));
      }
    }

    // Retrieve links from database.

    if ($sortAscending == true) {
      $order = 'ORDER BY created ASC';
    } else {
      $order = 'ORDER BY created DESC';
    }

    $links = $wpdb->get_results('
      SELECT created, title, url, description 
      FROM ' . self::$linksTableName
    . $where . $order);

    // Output nothing if no links found.

    if ($links === null || empty($links)) {
      return;
    }

    // Output links.

    echo '<div class="e11-recommended-links">';
    echo '<div class="title">' . $label . '</div>';

    foreach ($links as $link) {
      // [TODO] Converting "created" field into a friendlier date format,
      //        but this should be user-customizable once the config page
      //        is written.

      $created = DateTime::createFromFormat('Y-m-d H:i:s', $link->created)->format($date_format);

      // Get host string for display, stripping leading 'www.' if present.

      $host = parse_url($link->url, PHP_URL_HOST);

      if (substr($host, 0, 4) == 'www.') {
          $host = substr($host, 4);
      }
?>
      <div class="link">
        <div class="link-date"><?php echo $created; ?></div>
        <div class="link-label">
          <div class="link-title"><a href="<?php echo esc_url($link->url); ?>"><?php echo $link->title; ?></a> <span class="link-host">(<?php echo esc_html($host); ?>)</span></div>
<?php
        if (!empty($link->description)) {
?>
          <div class="link-description"><?php echo $link->description; ?></div>
<?php
        }
?>
        </div>
      </div>
<?php
    }

    echo '</div>';
  }
}


