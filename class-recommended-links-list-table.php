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
  public function __construct() {
    parent::__construct();

  }

  public function manage_columns() {
    return array(
      'id' => 'ID',
      'url' => 'URL',
      'description' => 'Description',
      'display_mode' => 'Display mode',
      'created' => 'Created'
    );
  }

  public function get_columns() {
    return array(
      'id' => 'ID',
      'url' => 'URL',
      'description' => 'Description',
      'display_mode' => 'Display mode',
      'created' => 'Created'
    );
  }

  public function column_default($item, $column_name)
  {
    return $item->$column_name;
  }

  public function ajax_user_can() {
    die( 'function WP_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
  }

  public function prepare_items() {
    global $wpdb;

    $links_table = $wpdb->prefix . 'e11_recommended_links';

    $total_items = $wpdb->get_var('SELECT COUNT(id) FROM ' . $links_table);

    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page' => 25
    ));
  }

}

add_filter('manage_settings_page_e11_recommended_links_columns',
                        array('RecommendedLinksListTable', 'manage_columns'));
