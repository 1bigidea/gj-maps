<?php

$databaseFunctions = new gjMapsDB();
$adminFunctions = new gjMapsAdmin();


/*
* This is our DELETE handling
*/

if(isset($_GET['delete'])) {

  $delete_map_id = $_GET['delete'];

  $response = $adminFunctions->deleteMap($delete_map_id);

}

/*
* This is our POST handling
*/

if(!empty($_POST)) {

  if($_POST['form_name'] === 'gj_maps_map_name') {

    $response = $adminFunctions->renameMap($_POST);

  }

  if($_POST['form_name'] === 'geocode') {

    $response = $adminFunctions->geocodePOI($_GET['map_id']);

  }

  if($_POST['form_name'] === 'gj_maps_poi' ) {


    foreach($_POST as $post) {

      

      if(isset($post['delete']) && $post['delete'] === 'on') {

        $deleteItems[] = $post;

      } elseif(isset($post['mode']) && $post['mode'] === 'update') {

        $updateItems[] = $post;

      } elseif(isset($post['mode']) && $post['mode'] === 'create') {

        $createItems[] = $post;

      }

    }

    if(!empty($deleteItems)) {

      $response = $adminFunctions->deletePOI($deleteItems);

    }

    if(!empty($updateItems)) {

      $response = $adminFunctions->editPOI($updateItems);

    }

    if(!empty($createItems)) {

      $response = $adminFunctions->createPOI($createItems);

    }

  }

  if($_GET['map_id'] === 'new') {

    $createMap = false;

  }


}

/*
* This is the maps tabbing system
*/

if(isset($createMap) && $createMap === false) {

  $map_id = $_POST['id'];

} else {

  $map_id = $adminFunctions->tabsMapID($_GET);

}

$map = $databaseFunctions->get_map();
$map_name = $databaseFunctions->getMaps(array('map_id' => $map_id));
echo $adminFunctions->mapsTab('poi', $map, $map_id);

/*
* These calls are for retrieving the POI data for the table.
*/

if($map_id != null) {

  $pagination = $adminFunctions->gjMapsPaginateTable($map_id, 30);
  $options = array(
    'type' => 'OBJECT',
    'map_id' => $map_id,
    'offset' => $pagination['sql_offset'],
    'length' => $pagination['sql_length']
    );

  $poi = $databaseFunctions->getPOI($options);
  $cat = $databaseFunctions->get_cat($type='OBJECT', $map_id);

  wp_localize_script('gj_maps_admin_js', 'cat', $cat);
  wp_localize_script('gj_maps_admin_js', 'map', array('id' => $map_id));

} else {

  $poi = false;

}

/*
* This is our response messaging
*/

if(isset($response)) {

  if($response['status'] === 'success') {

    echo '<div id="message" class="updated"><p>'.$response['message'].'</p></div>';

  } else if ($response['status'] === 'error') {

    echo '<div id="message" class="error"><p>'.$response['message'].'</p></div>';

  }

}

/*
* Sets up the pagination && urls
*/

$url = $adminFunctions->gjMapsBuildURL($map_id);

/*
* Sets our POST action
*/

$current_uri = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
$parsed_uri = parse_url($current_uri);
$post_uri = $map_id ? $parsed_uri['path'].'?page=gj_maps&map_id='.$map_id : $current_uri; ?>


<div class="wrap"><?php

  if($poi !== false) { ?>

    <form name="gj_maps_geocode" class="top-form" method="post" action="<?php echo $post_uri; ?>">
      <input type="hidden" name="form_name" value="geocode"/>
      <button type="submit" class="btn button">Find Geocodes</button>
    </form>

    <form name="gj_maps_map_name" class="top-form" method="post" action="<?php echo $post_uri; ?>">
      <input type="hidden" name="form_name" value="gj_maps_map_name">
      <input type="hidden" name="id" value="<?php echo $map_id; ?>">
      <input type="text" name="name" placeholder="Map Name" value="<?php echo !empty($map_name[0]->name) ? $map_name[0]->name : ''; ?>"/>
      <button type="submit" class="btn button">Change Map Name</button>
    </form>
    <a href="?page=gj_maps&delete=<?php echo $map_id; ?>" id="delete">Delete Map</a>

    <form name="gj_maps_poi" method="post" action="<?php echo $post_uri; ?>">
      <input type="hidden" name="form_name" value="gj_maps_poi">

      <div id="gj-table-container">
        <table class="wp-list-table widefat fixed gj-maps">
          <thead class="">
            <tr>
              <th scope="col" id="cb" class="column-cb check-column">
                <input id="cb-select-all-1" type="checkbox">
              </th>
              <th class="th-name"><span>Name</span></th>
              <th class="th-category"><span>Category</span></th>
              <th data-column="address" class="th-header active"><span>Address</span></th>
              <th data-column="city" class="th-header"><span>City</span></th>
              <th data-column="state" class="th-header"><span>State</span></th>
              <th data-column="zip" class="th-header"><span>Zip</span></th>
              <th data-column="country" class="th-header"><span>Country</span></th>
              <th data-column="phone" class="th-header"><span>Phone</span></th>
              <th data-column="url" class="th-header"><span>URL</span></th>
              <th data-column="latitude" class="th-header"><span>Latitude</span></th>
              <th data-column="longitude" class="th-header"><span>Longitude</span></th>
            </tr>
          </thead>
          <tbody><?php


          foreach ($poi as $point) { ?>

            <tr id="map-<?php echo $point->id; ?>" class="alternate poi" data-id="<?php echo $point->id; ?>" data-map="<?php echo $map_id; ?>">
              <input type="hidden" name="<?php echo $point->id; ?>[id]" value="<?php echo $point->id; ?>">
              <input type="hidden" name="<?php echo $point->id; ?>[map_id]" value="<?php echo $map_id; ?>">
              <input type="hidden" class="mode" name="<?php echo $point->id; ?>[mode]" value="">
              <th class="check-column">
                <input type="checkbox" class="maps-detect-change delete-box" name="<?php echo $point->id; ?>[delete]">
              </th>
              <td><input type="text" class="maps-detect-change full-width" name="<?php echo $point->id; ?>[name]" value="<?php echo $point->name; ?>"></td>
              <td>
                <select class="maps-detect-change" name="<?php echo $point->id; ?>[cat_id]"><?php

                  foreach ($cat as $key=>$value) {

                    if ( $point->cat_id == $value->id ) {
                      echo '<option value='.$value->id.' selected>'.$value->name.'</option>';
                    } else {
                      echo '<option value='.$value->id.'>'.$value->name.'</option>';
                    }

                  } ?>

                </select>
              </td>
              <td><input data-column="address" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[address]" value="<?php echo $point->address; ?>"></td>
              <td><input data-column="city" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[city]" value="<?php echo $point->city; ?>"></td>
              <td><input data-column="state" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[state]" value="<?php echo $point->state; ?>"></td>
              <td><input data-column="zip" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[zip]" value="<?php echo $point->zip; ?>"></td>
              <td><input data-column="country" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[country]" value="<?php echo $point->country; ?>"></td>
              <td><input data-column="phone" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[phone]" value="<?php echo $point->phone; ?>"></td>
              <td><input data-column="url" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[url]" value="<?php echo $point->url; ?>"></td>
              <td><input data-column="latitude" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[lat]" id="lat<?php echo $point->id; ?>" value="<?php echo $point->lat; ?>"></td>
              <td><input data-column="longitude" type="text" class="widen maps-detect-change full-width" name="<?php echo $point->id; ?>[lng]" id="lng<?php echo $point->id; ?>" value="<?php echo $point->lng; ?>"></td><?php
          } ?>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="gj-buttons">
        <div class="btn button table-button add-poi-row">Add POI</div>
        <button class="btn button table-button" type="submit">Update POI</button>
      </div>

    </form><?php

    if(isset($pagination)) { ?>

      <div class="tablenav bottom">
        <div class="tablenav-pages">
          <span class="displaying-num"><?php echo $pagination['total_items'].' items'; ?></span>
          <span class="pagination-links">
            <a 
              class="first-page <?php echo $pagination['current_page'] - 1 > 0 ? '' : 'disabled'; ?>" 
              title="Go to the first page" href="<?php echo $url.'&paged=1'; ?>">«
            </a>
            <a 
              class="prev-page <?php echo $pagination['current_page'] - 1 > 0 ? '' : 'disabled'; ?>" 
              title="Go to the previous page" 
              href="<?php echo $url.'&paged='.($pagination['current_page'] - 1 > 0 ? $pagination['current_page'] - 1 : $pagination['current_page']); ?>">‹
            </a>
            <span 
              class="paging-input"><?php echo $pagination['current_page']; ?> of 
              <span class="total-pages"><?php echo $pagination['pages'] == 0 ? '1' : $pagination['pages']; ?></span>
            </span>
            <a 
              class="next-page <?php echo $pagination['current_page'] + 1 > $pagination['pages'] ? 'disabled' : ''; ?>" 
              title="Go to the next page" 
              href="<?php echo $url.'&paged='.($pagination['current_page'] + 1 > $pagination['pages'] ? $pagination['current_page'] : $pagination['current_page'] + 1); ?>">›
            </a>
            <a 
              class="last-page <?php echo $pagination['current_page'] + 1 > $pagination['pages'] ? 'disabled' : ''; ?>" 
              title="Go to the last page" 
              href="<?php echo $url.'&paged='.$pagination['pages']; ?>">»
            </a>
          </span>
        </div>
      </div><?php

    }

  } else {

    echo '<h2>Hit the + to create your first map.</h2>';

  } ?>

</div>