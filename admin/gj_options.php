<?php
	if(isset($_POST['gj_hidden']) && $_POST['gj_hidden'] == 'gj_form_update_options') {
		//Form data sent
		$styles = isset($_POST['gj_styles']);
		update_option('gj_styles', $styles);

    $poi_list = isset($_POST['gj_poi_list']);
    update_option('gj_poi_list', $poi_list);

    $map_styles = $_POST['gj_map_styles'];
    update_option('gj_map_styles', $map_styles);
    $map_styles_strip = stripslashes($map_styles);

    $cat_default = $_POST['gj_cat_default'];
    update_option('gj_cat_default', $cat_default);

		$center_lat = $_POST['gj_center_lat'];
		update_option('gj_center_lat', $center_lat);

		$center_lng = $_POST['gj_center_lng'];
		update_option('gj_center_lng', $center_lng);

    $map_zoom = $_POST['gj_map_zoom'];
    update_option('gj_map_zoom', $map_zoom);
		?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		<?php
	} else {
		//Normal page display
		$styles = get_option('gj_styles');
    $poi_list = get_option('gj_poi_list');
    $cat_default = get_option('gj_cat_default');
		$center_lat = get_option('gj_center_lat');
		$center_lng = get_option('gj_center_lng');
    $map_zoom = get_option('gj_map_zoom');
    $map_styles = get_option('gj_map_styles');
    $map_styles_strip = stripslashes($map_styles);
	}
?>

<div class="wrap">
  <?php    echo "<h2>" . __( 'GJ Maps - Settings', 'gj_trdom' ) . "</h2>"; ?>
  <?php    echo "<h3>" . __( 'Basic', 'gj_trdom' ) . "</h3>"; ?>
  <form name="gj_form_update_options" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="gj_hidden" value="gj_form_update_options">
      <p><?php _e("Use GJ Maps styles: " ); ?><input type="checkbox" name="gj_styles" <?php if ($styles) echo 'checked'; ?>></p>
      <p><?php _e("Show POI list: " ); ?><input type="checkbox" name="gj_poi_list" <?php if ($poi_list) echo 'checked'; ?>></p>
      <p><?php _e("View All Default Color: " ); ?><input type="text" name="gj_cat_default" class="color-picker" value="<?php echo $cat_default; ?>"/></p>
      <p><?php _e("Center Latitude: " ); ?><input type="text" name="gj_center_lat" value="<?php echo $center_lat; ?>"></p>
      <p><?php _e("Center Longitude: " ); ?><input type="text" name="gj_center_lng" value="<?php echo $center_lng; ?>"></p>
      <p><?php _e("Map Zoom: " ); ?><input type="text" name="gj_map_zoom" value="<?php echo $map_zoom; ?>"></p>
      <p><?php _e("Map Styles: " ); ?><br /><a href="http://snazzymaps.com/" target="_blank">[Samples]</a><textarea type="textarea" name="gj_map_styles"><?php echo $map_styles_strip; ?></textarea></p>
      <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update Options', 'gj_trdom' ) ?>" />
      </p>
  </form>
</div>

<?php

require_once('db.php');

  if(isset($_POST['gj_hidden']) && $_POST['gj_hidden'] == 'gj_form_delete') {
    deletePOI();
    echo '<h4>Your Data has been deleted.</h4>';
  } else {
    ?>
  <div class="wrap">
    <?php    echo "<h3>" . __( 'Delete All POI Data', 'gj_trdom' ) . "</h3>"; ?>
    <form name="gj_form_delete" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
      <input type="hidden" name="gj_hidden" value="gj_form_delete">
      <?php    echo "<h4>" . __( 'Are you sure you want to delete all data?', 'gj_trdom' ) . "</h4>"; ?>
      <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Delete All Data', 'gj_trdom' ) ?>" />
      </p>
    </form>
  </div>
  <?php
  }
?>