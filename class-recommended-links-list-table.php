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

require_once( ABSPATH . '/wp-admin/includes/admin.php' );

class RecommendedLinksListTable extends WP_List_Table {

  /**
   * Constructor.  Calls the WP_List_Table constructor with parameters useful
   * for this class.
   */
  public function __construct() {
    parent::__construct(array(
      'plural' => 'links',
      'singular' => 'link',
      'ajax' => true
    ));
  }

  /**
   * Filter callback passed to
   * "manage_settings_page_e11_recommended_links_columns" hook.  The
   * "manage_{$screen->id}_columns" hook is created in get_column_headers()
   * in wp-admin/includes/screen.php and used by
   * WP_List_Table::get_column_info() to get the list of columns.
   *
   * @return array Associative array of fieldname => "Displayed label"
   */

  public function manage_columns() {
    return array(
      'cb' => '<input type="checkbox" />',
      'name' => 'Name',
      'url' => 'URL',
      'description' => 'Description',
      'display_mode' => 'Display mode',
      'created' => 'Created'
    );
  }

  /**
   * Mandatory override from WP_List_Table.  I thought this did what
   * manage_columns() is doing in this class.  It's called in the constructor
   * of WP_List_Table, but doesn't seem to take effect?
   *
   * [TODO] Figure out how to get this to work, to get rid of manage_columns()
   * if it's redundant.
   *
   * It's getting called for get_default_primary_column_name, but not, for
   * some reason, get_column_headers() in wp-admin/includes/screen.php:
   *
   * WP_List_Table->get_column_info( )	.../class-wp-list-table.php:1268
   * WP_List_Table->get_primary_column_name( )	.../class-wp-list-table.php:983
   * WP_List_Table->get_default_primary_column_name( )	.../class-wp-list-table.php:950
   *
   * @return array
   */
  public function get_columns() {
    // trigger_error('in get columns');
    return array(
      'cb' => '<input type="checkbox" />',
      'name' => 'Name',
      'url' => 'URL',
      'description' => 'Description',
      'display_mode' => 'Display mode',
      'created' => 'Created'
    );
  }

  /**
   * Return list of columns that can be sorted by clicking the column header.
   *
   * @return array Associative array, field_name => sort_field.  The
   *               "sort_field" can be array('sort_field', true) to indicate
   *               it should be sorted in descending order on first click.
   */
  protected function get_sortable_columns() {
    return array(
      'name' => 'name',
      'url' => 'url',
      'created' => array('created', true)
    );
  }

  /**
   * Custom output for "cb" column (the checkbox at the left for row selection)
   *
   * @param object $item Link record
   * @return string HTML output for field
   */
  public function column_cb($item) {
    return '<input id="cb-select-' . $item->id
              . '" type="checkbox" name="link[]" value="' . $item->id . '" />';
  }

  /**
   * Custom output for "display_mode" column.  Convert integer to label for
   * display.
   *
   * @param object $item Link record
   * @return string HTML output for field
   */
  public function column_display_mode($item) {
    switch($item->display_mode) {
      case 1:
        return 'Post index';

      case 2:
        return 'Sidebar widget';

      case 3:
        return 'Index and widget';
    }

    return '(invalid)';
  }

  /**
   * Default handler for column display
   *
   * @param object $item Link record
   * @param string $column_name Field to display
   * @return mixed Field value
   */
  public function column_default($item, $column_name)
  {
    return $item->$column_name;
  }

  /**
   * [TODO] Write.
   */
  public function ajax_user_can() {
    die( 'function WP_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
  }

  /**
   * Mandatory override for reading a range of items from the database for
   * display based on parameters (possibly page, sort field, and sort order.)
   */
  public function prepare_items() {
    global $wpdb;

    // Set name of recommended links table and count its records.

    $links_table = $wpdb->prefix . 'e11_recommended_links';

    $total_items = $wpdb->get_var('SELECT COUNT(id) FROM ' . $links_table);

    // Read current page number and set number of items/page.

    $page = $this->get_pagination_arg('page');
    $limit = 10;

    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page' => $limit
    ));

    // Read and validate sort order field and direction.

    if (isset($_REQUEST['orderby']) && isset($_REQUEST['order'])) {
      $order_by = $_REQUEST['orderby'];
      $order = ($_REQUEST['order'] === 'asc' ? 'asc' : 'desc');

      if (!in_array(
                    $order_by, array_keys($this->get_sortable_columns()))) {

        // Ignore "order by" if field isn't a sortable column.

        $order_by = 'id';
        $order = 'desc';
      }
    } else {
      $order_by = 'id';
      $order = 'desc';
    }

    // Calculate offset for query.

    $offset = ($page - 1) * $limit;

    // Read page of links from the table and populate the list of items in
    // the object with the results.

    $links = $wpdb->get_results('
      SELECT id, name, created, url, description, display_mode 
      FROM ' . $links_table . '
      ORDER BY ' . $order_by . ' ' . $order . '
      LIMIT ' . $limit . ' OFFSET ' . $offset);

    $this->items = $links;
  }

}

add_filter('manage_settings_page_e11_recommended_links_columns',
                        array('RecommendedLinksListTable', 'manage_columns'));
