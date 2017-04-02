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

class e11RecommendedLinksWidget extends \WP_Widget {
  /**
   * Construct widget
   */
  public function __construct() {
    $widget_ops = array(
      'classname' => 'e11-recommended-links-widget',
      'description' => 'Show random list of admin-recommended links',
    );
    parent::__construct( 'e11-recommended-links-widget', 'e11 Recommended Links', $widget_ops );
  }

  /**
   * Outputs the content of the widget
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    global $wpdb;

    $linksTableName = $wpdb->prefix . 'e11_recommended_links';

    // Display widget title

    $title = apply_filters( 'widget_title', empty($instance['title']) ? __( 'Recommended links' ) : $instance['title'],
      $instance, $this->id_base );

    echo $args['before_widget'];

    if ( $title ) {
      echo $args['before_title'] . $title . $args['after_title'];
    }

    // [TODO] Set the number of results on the config page
    // when it's developed.

    $limit = 3;

    $links = $wpdb->get_results($wpdb->prepare('
      SELECT title, url, description 
      FROM ' . $linksTableName . '
      WHERE display_mode = 2 OR display_mode = 3
      ORDER BY RAND()
      LIMIT %d
    ', $limit));

    foreach ($links as $link) {
?>
      <div class="link">
        <a class="link-title" href="<?php echo esc_url($link->url); ?>"><?php echo $link->title; ?></a>
        <div class="link-description"><?php echo $link->description; ?></div>
      </div>
<?php
    }

    echo $args['after_widget'];
  }
}

add_action( 'widgets_init', function(){
  register_widget( 'e11RecommendedLinksWidget' );
});

?>