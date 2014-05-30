<?php

class gjMapsDB {

  private $wpdb;

  function __construct() {

    global $wpdb;

    $this->wpdb = $wpdb;

  }

  function deleteAllData() {

    $response['cat'] = $this->deleteAllCat();
    $response['maps'] = $this->deleteAllMaps();
    $response['poi'] = $this->deleteAllPOI();

    return $response;

  }

  /*
  * Table Name Functions
  */

  function mapsTable() {

    $table = $this->wpdb->prefix.'gjm_maps';

    return $table;

  }

  function poiTable() {

    $table = $this->wpdb->prefix.'gjm_poi';

    return $table;

  }

  function catTable() {

    $table = $this->wpdb->prefix.'gjm_cat';

    return $table;

  }


  /*
  * Map Database Functions
  */

  function maxMapID() {

    $table_name = $this->mapsTable();

    $maxMapID = $this->wpdb->get_results(
      "
      SELECT MAX(id)
      FROM $table_name
      "
    );

    return $maxMapID;

  }

  function getMapID($name, $type='OBJECT') {

    $table_name = $this->mapsTable();
    $where = "name = '$name'";

    $query = $this->wpdb->get_results(
      "
      SELECT *
      FROM $table_name
      WHERE $where
      ",
      $type
    );

    return $query;

  }

  function saveMap($id) {

    $table_name = $this->mapsTable();

    $rows_affected = $this->wpdb->insert(
      $table_name,
      array(
        'id'=>$id,
        'name'=>'Map ' . $id,
      )
    );

    return $rows_affected;

  }

  function editMapSettings($ms) {

    $table_name = $this->mapsTable();

    $rows_affected = $wpdb->update(
      $table_name, 
      array(
        'name'=>$ms['name'],
        'c_lat'=>$ms['c_lat'],
        'c_lng'=>$ms['c_lng'],
        'm_zoom'=>$ms['m_zoom']
      ),
      array('id'=>$ms['id'])
    );

  }

  function deleteAllMaps() {

    $table_name = $this->mapsTable();

    $response = $this->wpdb->query(
      "TRUNCATE TABLE $table_name"
    );

    return $response;

  }

  /*
  * POI Database Functions
  */

  function savePOI ($poi) {

    global $wpdb;
    $table_name = $wpdb->prefix . "gjm_poi";

    foreach ($poi as $key=>$value) {
      $rows_affected = $wpdb->insert( $table_name, array(
        'cat_id'=>$value['cat_id'],
        'map_id'=>$value['map_id'],
        'name'=>$value['name'],
        'address'=>$value['address'],
        'city'=>$value['city'],
        'state'=>$value['state'],
        'zip'=>$value['zip'],
        'country'=>$value['country'],
        'phone'=>$value['phone'],
        'url'=>$value['url'],
        'lat'=>$value['lat'],
        'lng'=>$value['lng']
      ) );
    }

  }

  function editPOI ($poi) {

    global $wpdb;
    $table_name = $wpdb->prefix . "gjm_poi";

    $rows_affected = $wpdb->update(
      $table_name,
      array(
        'cat_id'=>$poi['cat_id'],
        'name'=>$poi['name'],
        'address'=>$poi['address'],
        'city'=>$poi['city'],
        'state'=>$poi['state'],
        'zip'=>$poi['zip'],
        'country'=>$poi['country'],
        'phone'=>$poi['phone'],
        'url'=>$poi['url'],
        'lat'=>$poi['lat'],
        'lng'=>$poi['lng']
      ),
      array( 'id'=>$poi['id'] )
    );
  }

  function deletePOI ($id = false) {
    global $wpdb;

    $table_name = $wpdb->prefix . "gjm_poi";

    if ($id) {
      $wpdb->query(
          $wpdb->prepare(
            "
            DELETE FROM $table_name 
            WHERE id = %d
            ",
            $id
        )
      );
    } else {
      $wpdb->query(
          "TRUNCATE TABLE $table_name"
       );
     }
  }

  function deleteAllPOI() {

    $table_name = $this->poiTable();

    $response = $this->wpdb->query(
      "TRUNCATE TABLE $table_name"
    );

    return $response;

  }

  /*
  * Category Database Functions
  */

  function saveCat ($cat) {

    global $wpdb;
    $table_name = $wpdb->prefix . "gjm_cat";
    $rows_affected = $wpdb->insert( $table_name, array( 'name'=>$cat['name'], 'color'=>$cat['color'], 'icon'=>$cat['icon'], ) );

  }

  function editCat ($cat) {
    if(array_key_exists('icon',$cat) && $cat['icon'] == null) {
      unset($cat['icon']);
    }
    global $wpdb;
    $table_name = $wpdb->prefix . "gjm_cat";

    $rows_affected = $wpdb->update( 
      $table_name, $cat, array( 'id'=>$cat['id']) 
    );

  }

  function deleteCat ($id) {
    global $wpdb;

    $table_name = $wpdb->prefix . "gjm_cat";

    $wpdb->query(
      $wpdb->prepare(
        "
        DELETE FROM $table_name 
        WHERE id = %d
        ",
        $id
      )
    );

  }

  function deleteAllCat() {

    $table_name = $this->catTable();

    $response = $this->wpdb->query(
      "TRUNCATE TABLE $table_name"
    );

    return $response;

  }

  /*
  * Retrieval Database Functions
  */

  function get_poi($type='OBJECT', $where='1=1') {
    //Allows you to set the type of the return value (assc. array or stdClass) and the WHERE clause, if necessary
    global $wpdb;

    $table_name = $wpdb->prefix . "gjm_poi";
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

  function get_cat($type='OBJECT', $where='1=1') {
    //Allows you to set the type of the return value (assc. array or stdClass) and the WHERE clause, if necessary
    global $wpdb;

    $table_name = $wpdb->prefix . "gjm_cat";
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

  function get_map($type='OBJECT', $where='1=1') {
    //Allows you to set the type of the return value (assc. array or stdClass) and the WHERE clause, if necessary
    global $wpdb;

    $table_name = $wpdb->prefix . "gjm_maps";
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

  function get_map_key($id, $obj) {
    foreach ($obj as $key => $value) {
      if ($value->id == $id) {
        return $key;
      }
    }
  }


}
