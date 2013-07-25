<?php
/*
Plugin Name: Hippy Archive
Description: A hippy archive widget
Version: 1.0
Author: Kamweti Muriuki
Author URI: http://github.com/kamweti
Text Domain: hippy-archive
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2013

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class HippyArchive extends WP_Widget{

  function __construct(){
    // load plugin text domain
    add_action( 'init', array( $this, 'widget_textdomain' ) );

    parent::__construct(
      'hippy-archive',
      __('HippyArchive', 'hippy-archive-locale' ),
      array(
        'classname' => 'widget-hippy-archive',
        'description' => __( 'A hippy archive widget', 'hippy-archive-locale' )
      )
    );

    // Register site styles and scripts
    add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );

  }

  /**
   * Frontend display of the widget
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance){
    extract( $args );

    $archive = $this->get_archive();
    $c = ! empty( $instance['count'] ) ? '1' : '0';
    $title = apply_filters('widget_title', empty($instance['title']) ? __('Archives') : $instance['title'], $instance, $this->id_base);

    echo $before_widget;
    if ( ! empty( $title ) ) {
      echo $args['before_title'] . $title . $args['after_title'];
    }
    include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );
    echo $after_widget;
  }

  /**
   * Admin form for the widget
   *
   * @param  array $instance array of keys and values of the widget
   */
  public function form( $instance ){
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, ) );
    $title = strip_tags($instance['title']);

    $count = $instance['count'] ? 'checked="checked"' : '';

    include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );
  }

  public function update( $new_instance, $old_instance ) {
    $instance = array();

    $instance = $old_instance;
    $new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0 ) );
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['count'] = $new_instance['count'] ? 1 : 0;

    return $instance;
  }


  /**
   * Register widget styles
   */
  public function register_widget_styles(){
    wp_enqueue_style( 'hippy-archive-styles', plugins_url( 'hippy-archive/css/widget.css' ) );
  }

  /**
   * Registers widget scripts.
   */
  public function register_widget_scripts() {
    wp_enqueue_script( 'widget-name-script', plugins_url( 'hippy-archive/js/widget.js' ), array('jquery') );

  }


  /**
   * Loads the Widget's text domain for localization and translation.
   */
  public function widget_textdomain() {
    load_plugin_textdomain( 'hippy-archive-locale', false, plugin_dir_path( __FILE__ ) . '/lang/' );
  }


  /**
   * Generate a posts archive array,
   *
   *
   * @return  array array of year => $monts
   */
  private function get_archive(){
    // get all post dates for published posts
    global $wpdb;

    $query = "
      select $wpdb->posts.post_date
      from $wpdb->posts
      where $wpdb->posts.post_status = 'publish'
      order by $wpdb->posts.post_date desc
    ";

    $posts = $wpdb->get_results( $query );

    $archive = array(); //as a list of $key year and monthname values
    $months = array(
      'December' => 0,
      'November' => 0,
      'October' => 0,
      'September' => 0,
      'August' => 0,
      'July' => 0,
      'June' => 0,
      'May' => 0,
      'April' => 0,
      'March' => 0,
      'February' => 0,
      'January' => 0,
    );

    foreach( $posts as $post ){
      $year_published = date('Y', strtotime($post->post_date));
      $month_published = date('F', strtotime($post->post_date));

      if( ! array_key_exists($year_published, $archive) ) {
        if( $year_published == date('Y') ) {
          $archive[$year_published] = array_slice($months, - date('n')); //fetch the months up until the current month
        } else {
          $archive[$year_published] = $months;
        }
      }
      $archive[$year_published][$month_published] = ++ $archive[$year_published][$month_published];
    }

    return $archive;
  }

}

add_action( 'widgets_init', create_function( '', 'register_widget("HippyArchive");' ) );
