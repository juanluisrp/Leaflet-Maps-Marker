<?php
/*
    Tools - Leaflet Maps Marker Plugin
*/
?>
<div class="wrap">
<?php include('leaflet-admin-header.php'); ?>
<?php
global $wpdb;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$markercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_markers.''); 
$layercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.''); 
$action = isset($_POST['action']) ? $_POST['action'] : '';
if (!empty($action)) {
	$toolnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($toolnonce, 'tool-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').''); };
  if ($action == 'mass_assign') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET layer = %d where layer = %d", $_POST['layer_assign_to'], $_POST['layer_assign_from'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('All markers from layer ID %1$s have been successfully assigned to layer ID %2$s','lmm'), $_POST['layer_assign_from'], $_POST['layer_assign_to']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
		
  }
  elseif ($action == 'mass_delete_from_layer') {
		$result = $wpdb->prepare( "DELETE FROM $table_name_markers where layer = %d", $_POST['delete_from_layer']);
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('All markers from layer ID %1$s have been successfully deleted','lmm'), $_POST['delete_from_layer']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'mass_delete_all_markers') {
		$result = $wpdb->prepare( "DELETE FROM $table_name_markers");
		$wpdb->query( $result );
  		$delete_confirm_checkbox = isset($_POST['delete_confirm_checkbox']) ? '1' : '0';
	  	if ($delete_confirm_checkbox == 1) {
			echo '<p><div class="updated" style="padding:10px;">' . __('All markers from all layers have been successfully deleted','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Please confirm that you want to delete all markers by checking the checkbox','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
		}
  }
  elseif ($action == 'basemap') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET basemap = %s", $_POST['basemap'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('The basemap for all markers has been successfully set to %1$s','lmm'), $_POST['basemap']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }  
  elseif ($action == 'overlays') {
		$overlays_checkbox = isset($_POST['overlays_custom']) ? '1' : '0';
		$overlays2_checkbox = isset($_POST['overlays_custom2']) ? '1' : '0';
		$overlays3_checkbox = isset($_POST['overlays_custom3']) ? '1' : '0';
		$overlays4_checkbox = isset($_POST['overlays_custom4']) ? '1' : '0';
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET overlays_custom = %s, overlays_custom2 = %s, overlays_custom3 = %s, overlays_custom4 = %s", $overlays_checkbox, $overlays2_checkbox, $overlays3_checkbox, $overlays4_checkbox );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The overlays status for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'wms') {
		$wms_checkbox = isset($_POST['wms']) ? '1' : '0';
		$wms2_checkbox = isset($_POST['wms2']) ? '1' : '0';
		$wms3_checkbox = isset($_POST['wms3']) ? '1' : '0';
		$wms4_checkbox = isset($_POST['wms4']) ? '1' : '0';
		$wms5_checkbox = isset($_POST['wms5']) ? '1' : '0';
		$wms6_checkbox = isset($_POST['wms6']) ? '1' : '0';
		$wms7_checkbox = isset($_POST['wms7']) ? '1' : '0';
		$wms8_checkbox = isset($_POST['wms8']) ? '1' : '0';
		$wms9_checkbox = isset($_POST['wms9']) ? '1' : '0';
		$wms10_checkbox = isset($_POST['wms10']) ? '1' : '0';
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d", $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox );
		$wpdb->query( $result );
		echo '<p><div class="updated" style="padding:10px;">' . __('The WMS status for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }  
  elseif ($action == 'mapsize') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET mapwidth = %d, mapwidthunit = %s, mapheight = %d", $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('The map size for all markers has been successfully set to width =  %1$s %2$s and height = %3$s px','lmm'), $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'zoom') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET zoom = %d", $_POST['zoom'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('Zoom level for all markers has been successfully set to %1$s','lmm'), $_POST['zoom']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'controlbox') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET controlbox = %d", $_POST['controlbox'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Controlbox status for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'panel') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET panel = %d", $_POST['panel'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Panel status for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'icon') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET icon = %s", $_POST['icon'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The icon for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'openpopup') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET openpopup = %d", $_POST['openpopup'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The popup status for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>'; 
  }
  elseif ($action == 'popuptext') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET popuptext = %s", $_POST['popuptext'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('The popup text for all markers has been successfully updated','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
} else {
$layerlist = $wpdb->get_results('SELECT * FROM ' . $table_name_layers . ' WHERE id>0', ARRAY_A);
?>
<h3>Tools</h3>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<form method="post">
<input type="hidden" name="action" value="mass_assign" />
<?php wp_nonce_field('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<td colspan="2"><strong><?php _e('Move markers to a layer','lmm') ?></strong></td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<?php _e('Source','lmm') ?>: 
		<select id="layer_assign_from" name="layer_assign_from">
		<?php $markercount_layer0 = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id=0'); ?>
		<option value="0">ID 0 - <?php _e('unassigned','lmm') ?> (<?php echo $markercount_layer0; ?> <?php _e('marker','lmm'); ?>)</option>
		<?php
		foreach ($layerlist as $row) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
			echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes($row['name']) . ' (' . $markercount .' ' . __('marker','lmm') . ')</option>';
		}
		?>
		</select>
		<?php _e('Target','lmm') ?>: 
		<select id="layer_assign_to" name="layer_assign_to">
		<option value="0">ID 0 - <?php _e('unassigned','lmm') ?> (<?php echo $markercount_layer0; ?> <?php _e('marker','lmm'); ?>)</option>
		<?php
		foreach ($layerlist as $row) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
			echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes($row['name']) . ' (' . $markercount .' ' . __('marker','lmm') . ')</option>';
		}
		?>
		</select>
		</td>
		<td>
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_asign-submit" value="<?php _e('move markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to move the selected markers?','lmm') ?>')" />
		</td>
	</tr>
</table>
</form>
<br/><br/>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<form method="post">
<input type="hidden" name="action" value="mass_delete_from_layer" />
<?php wp_nonce_field('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<td colspan="2"><strong><?php _e('Delete all markers from a layer','lmm') ?></strong></td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<?php _e('Layer','lmm') ?>: 
		<select id="delete_from_layer" name="delete_from_layer">
		<option value="0">ID 0 - <?php _e('unassigned','lmm') ?> (<?php echo $markercount_layer0; ?> <?php _e('marker','lmm'); ?>)</option>
		<?php
		foreach ($layerlist as $row) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
			echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes($row['name']) . ' (' . $markercount .' ' . __('marker','lmm') . ')</option>';
		}
		?>
		</select>
		</td>
		<td>
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_delete_from_layer-submit" value="<?php _e('delete all markers from selected layer','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to delete all markers from the selected layer? (cannot be undone)','lmm') ?>')" />
		</td>
	</tr>
</table>
</form>
<br/><br/>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<?php 
		$settings_all_markers = sprintf( esc_attr__('Change settings for all %1$s existing markers','lmm'), $markercount_all);
		?>
		<td colspan="3"><strong><?php echo $settings_all_markers ?></strong></td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="basemap" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Basemap','lmm') ?></strong>
		</td>
		<td>
		<input type="radio" name="basemap" value="osm_mapnik" checked /> <?php echo $lmm_options['default_basemap_name_osm_mapnik']; ?><br />
		<input type="radio" name="basemap" value="osm_osmarender" /> <?php echo $lmm_options['default_basemap_name_osm_osmarender']; ?><br />
		<input type="radio" name="basemap" value="mapquest_osm" /> <?php echo $lmm_options['default_basemap_name_mapquest_osm']; ?><br />
		<input type="radio" name="basemap" value="mapquest_aerial" /> <?php echo $lmm_options['default_basemap_name_mapquest_aerial']; ?><br />
		<input type="radio" name="basemap" value="ogdwien_basemap" /> <?php echo $lmm_options['default_basemap_name_ogdwien_basemap']; ?><br />
		<input type="radio" name="basemap" value="ogdwien_satellite" /> <?php echo $lmm_options['default_basemap_name_ogdwien_satellite']; ?><br />
		<input type="radio" name="basemap" value="custom_basemap" /> <?php echo $lmm_options['custom_basemap_name']; ?><br />
		<input type="radio" name="basemap" value="custom_basemap2" /> <?php echo $lmm_options['custom_basemap2_name']; ?><br />
		<input type="radio" name="basemap" value="custom_basemap3" /> <?php echo $lmm_options['custom_basemap3_name']; ?>
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="basemap-submit" value="<?php _e('change basemap for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the basemap for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="overlays" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Checked overlays in control box','lmm') ?></strong>
		</td>
		<td>
		<input type="checkbox" name="overlays_custom" /> <?php echo $lmm_options['overlays_custom_name']; ?><br />
		<input type="checkbox" name="overlays_custom2" /> <?php echo $lmm_options['overlays_custom2_name']; ?><br />
		<input type="checkbox" name="overlays_custom3" /> <?php echo $lmm_options['overlays_custom3_name']; ?><br />
		<input type="checkbox" name="overlays_custom4" /> <?php echo $lmm_options['overlays_custom4_name']; ?>
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="overlays-submit" value="<?php _e('change overlay status for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the overlay status for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="wms" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Active WMS layers','lmm') ?></strong>
		</td>
		<td>
		<input type="checkbox" name="wms" /> <?php echo $lmm_options['wms_wms_name']; ?><br />
		<input type="checkbox" name="wms2" /> <?php echo $lmm_options['wms_wms2_name']; ?><br />
		<input type="checkbox" name="wms3" /> <?php echo $lmm_options['wms_wms3_name']; ?><br />
		<input type="checkbox" name="wms4" /> <?php echo $lmm_options['wms_wms4_name']; ?><br />
		<input type="checkbox" name="wms5" /> <?php echo $lmm_options['wms_wms5_name']; ?><br />
		<input type="checkbox" name="wms6" /> <?php echo $lmm_options['wms_wms6_name']; ?><br />
		<input type="checkbox" name="wms7" /> <?php echo $lmm_options['wms_wms7_name']; ?><br />
		<input type="checkbox" name="wms8" /> <?php echo $lmm_options['wms_wms8_name']; ?><br />
		<input type="checkbox" name="wms9" /> <?php echo $lmm_options['wms_wms9_name']; ?><br />
		<input type="checkbox" name="wms10" /> <?php echo $lmm_options['wms_wms10_name']; ?><br />
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="wms-submit" value="<?php _e('change active WMS layers for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change active WMS layers for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="mapsize" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Map size','lmm') ?></strong>
		</td>
		<td style="vertical-align:middle;">
		<?php _e('Width','lmm') ?>:
		<input size="2" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo intval($lmm_options[ 'defaults_marker_mapwidth' ]) ?>" />
		<input type="radio" name="mapwidthunit" value="px" checked />
		px&nbsp;&nbsp;&nbsp;
		<input type="radio" name="mapwidthunit" value="%" />%<br/>
		<?php _e('Height','lmm') ?>:
		<input size="2" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo intval($lmm_options[ 'defaults_marker_mapheight' ]) ?>" />px
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mapsize-submit" value="<?php _e('change mapsize for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the map size for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<form method="post">
		<input type="hidden" name="action" value="zoom" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Zoom','lmm') ?></strong>
		</td>
		<td style="vertical-align:middle;">
		<input style="width: 30px;" type="text" id="zoom" name="zoom" value="<?php echo intval($lmm_options[ 'defaults_marker_zoom' ]) ?>" />
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="zoom-submit" value="<?php _e('change zoom for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the zoom level for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="controlbox" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Basemap/overlay controlbox on frontend','lmm') ?></strong>
		</td>
		<td style="vertical-align:middle;">
		<input type="radio" name="controlbox" value="0" /><?php _e('hidden','lmm') ?><br/>
		<input type="radio" name="controlbox" value="1" checked /><?php _e('collapsed (except on mobiles)','lmm') ?><br/>
		<input type="radio" name="controlbox" value="2" /><?php _e('expanded','lmm') ?><br/>
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="controlbox-submit" value="<?php _e('change controlbox status for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the controlbox status for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="panel" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Panel for displaying marker name and API URLs on top of map','lmm') ?></strong>
		</td>
		<td style="vertical-align:middle;">
		<input type="radio" name="panel" value="1" checked />
		<?php _e('show','lmm') ?><br/>
		<input type="radio" name="panel" value="0" />
		<?php _e('hide','lmm') ?></p></td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="panel-submit" value="<?php _e('change panel status for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the panel status for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="icon" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Icon','lmm') ?></strong></td>
		<td style="vertical-align:middle;">
		<div style="text-align:center;float:left;"><img src="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' ?>"/><br/>
		<input type="radio" name="icon" value="" checked />
		</div>
		<?php
		  $iconlist = array();
		  $dir = opendir(LEAFLET_PLUGIN_ICONS_DIR);
		  while ($file = readdir($dir)) {
		    if ($file === false)
		      break;
		    if ($file != "." and $file != "..")
		      if (!is_dir($dir.$file) and substr($file, count($file)-5, 4) == '.png')
		        $iconlist[] = $file;
		  }
		  closedir($dir);
		  sort($iconlist);
		foreach ($iconlist as $row)
		  echo '<div style="text-align:center;float:left;"><img id="iconpreview" src="' . LEAFLET_PLUGIN_ICONS_URL . '/' . $row . '" title="' . $row . '" alt="' . $row . '"/><br/><input type="radio" name="icon" value="'.$row.'" /></div>';
		?>
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="icon-submit" value="<?php _e('update icon for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the icon for all markers? (cannot be undone)','lmm') ?>')" />
		</form>
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="openpopup" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Popup status','lmm') ?></strong></td>
		<td style="vertical-align:middle;">
		<input type="radio" name="openpopup" value="0" checked />
		<?php _e('closed','lmm') ?>&nbsp;&nbsp;&nbsp;
		<input type="radio" name="openpopup" value="1" />
		<?php _e('open','lmm') ?></td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="openpopup-submit" value="<?php _e('change popup status for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the popup status for all markers? (cannot be undone)','lmm') ?>')" />
		</form>		
		</td>
	</tr>
	<tr>
		<td>
		<form method="post">
		<input type="hidden" name="action" value="popuptext" />
		<?php wp_nonce_field('tool-nonce'); ?>
		<strong><?php _e('Popup text','lmm') ?></strong></td>
		<td style="vertical-align:middle;">
		<?php 
			global $wp_version;
			if ( version_compare( $wp_version, '3.3', '>=' ) ) 
			{
				$settings = array( 
						'wpautop' => true,
						'tinymce' => array(
						'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,link,unlink,|,ltr,rtl',
						'theme' => 'advanced',
						'height' => '300',
						'content_css' => LEAFLET_PLUGIN_URL . 'css/leafletmapsmarker-admin-tinymce.css',
						'theme_advanced_statusbar_location' => 'bottom',
						'setup' => 'function(ed) {
								ed.onKeyDown.add(function(ed, e) {
									marker._popup.setContent(ed.getContent());
								});
							}'							
						 ),
						'quicktags' => array(
							'buttons' => 'strong,em,link,block,del,ins,img,code,close'));
				wp_editor( '', 'popuptext', $settings);
			}
			else //info: for WP 3.0, 3.1. 3.2
			{
				if (function_exists( 'wp_tiny_mce' ) ) {
					add_filter( 'teeny_mce_before_init', create_function( '$a', '
					$a["theme_advanced_buttons1"] = "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,outdent,indent,blockquote,|,bullist,numlist,|,link,unlink,image,|,code";
					$a["theme"] = "advanced";
					$a["skin"] = "wp_theme";
					$a["height"] = "250";
					$a["width"] = "640";
					$a["onpageload"] = "";
					$a["mode"] = "exact";
					$a["elements"] = "popuptext";
					$a["editor_selector"] = "mceEditor";
					$a["plugins"] = "inlinepopups";
					$a["forced_root_block"] = "p";
					$a["force_br_newlines"] = true;
					$a["force_p_newlines"] = false;
					$a["convert_newlines_to_brs"] = true;
					$a["theme_advanced_statusbar_location"] = "bottom";
					return $a;'));
					wp_tiny_mce(true);
				}						
			echo '<textarea id="popuptext" name="popuptext"></textarea>';
			}
		?>
		</td>
		<td style="vertical-align:middle;">
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="popuptext-submit" value="<?php _e('change popup text for all markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to change the popup text for all markers? (cannot be undone)','lmm') ?>')" />
		</form>		
		</td>
	</tr>	
</table>
<br/><br/>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<form method="post">
<input type="hidden" name="action" value="mass_delete_all_markers" />
<?php wp_nonce_field('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<?php 
		$delete_all = sprintf( esc_attr__('Delete all %1$s markers from all %2$s layers','lmm'), $markercount_all, $layercount_all);
		?>
		<td colspan="2"><strong><?php echo $delete_all ?></strong></td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<input type="checkbox" id="delete_confirm_checkbox" name="delete_confirm_checkbox" /> <?php _e('Yes','lmm') ?>
		</td>
		<td>
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_delete_all_markers" value="<?php _e('delete all markers from all layers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to delete all markers from all layers? (cannot be undone)','lmm') ?>')" />
		</td>
	</tr>
</table>
</form>
	
</div>
<!--wrap--> 
<?php } ?>