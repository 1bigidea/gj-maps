<?php

/*
 * Retrieve POIs from $wpdb
 */

if ( ! class_exists( 'GJ_api') ) {
   class GJ_api {

      function __construct() {
        //Makes gj_get_POI available from front-end
         add_action( 'wp_enqueue_scripts', array( &$this, 'gj_get_POI' ) );
      }

      public function gj_get_POI($type='OBJECT', $where='1=1') {
        //Allows you to set the type of the return value (assc. array or stdClass) and the WHERE clause, if necessary
         global $wpdb;

         $table_name = $wpdb->prefix . "gj_poi";
         $query = $wpdb->get_results(
            "
            SELECT *
            FROM $table_name
            WHERE $where
            ",
            $type
         );

         return $query;

      }

      public static function gj_POI_frontend() {
         if ( ! isset($GJ_api) ) {
            $GJ_api = new GJ_api();
         }

          //Writes the JS to the page, including POIs and categories
          $poi = json_encode($GJ_api->gj_get_POI());
          echo '<script type="text/javascript">';
          echo 'var poi = ';
          print_r($poi);
          echo ';';

          if ( ! isset($GJ_cat) ) {
            $GJ_cat = new GJ_cat();
          }
          $poi = json_encode($GJ_cat->gj_get_cat());
          echo 'var cat = ';
          print_r($poi);
          echo ';';

          $gj_poi_list = get_option('gj_poi_list');
          $center_lat = get_option('gj_center_lat');
          $center_lng = get_option('gj_center_lng');
          $gj_map_zoom = get_option('gj_map_zoom');
          $gj_map_styles = get_option('gj_map_styles');
          $gj_label_color = get_option('gj_label_color');

            // Strip slashes and remove whitespace
            $map_styles = stripslashes($gj_map_styles);
            $map_styles = preg_replace("/\s+/", "", $map_styles);

          echo 'var poi_list = '.($gj_poi_list ? $gj_poi_list : '0').';';
          echo 'var center_lat = '.($center_lat ? $center_lat : '34.0459231').';';
          echo 'var center_lng = '.($center_lng ? $center_lng : '-118.2504648').';';
          echo 'var map_zoom = '.($gj_map_zoom ? $gj_map_zoom : '14').';';
          echo 'var label_color = "'.($gj_label_color ? $gj_label_color : '0').'";';
          echo 'var map_styles = '.($gj_map_styles ? $map_styles : '0').';';
          echo '</script>';
      }
   }
}


if ( class_exists( 'GJ_api' ) ) {
$GJ_api = new GJ_api();
}
