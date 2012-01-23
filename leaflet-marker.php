<?php
/*
    Edit marker - Leaflet Maps Marker Plugin
*/
?>
<div class="wrap">
<?php include('leaflet-admin-header.php'); ?>
<?php
global $wpdb;
$lmm_options = get_option( 'leafletmapsmarker_options' );
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : ''); 
$addtoLayer = isset($_GET['addtoLayer']) ? intval($_GET['addtoLayer']) : (isset($_POST['layer']) ? $_POST['layer'] : ''); 
$layername = isset($_GET['Layername']) ? stripslashes($_GET['Layername']) : ''; 
$layer_show_button = ($addtoLayer != NULL && $addtoLayer != 0) ? "<a class='button-secondary' href=" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $addtoLayer .">" . __('edit assigned layer','lmm') . "</a>&nbsp;&nbsp;&nbsp;" : "";
$oid = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? intval($_GET['id']) : '');
$lat_check = isset($_POST['lat']) ? $_POST['lat'] : (isset($_GET['lat']) ? $_GET['lat'] : '');
$lon_check = isset($_POST['lon']) ? $_POST['lon'] : (isset($_GET['lon']) ? $_GET['lon'] : '');
	
if (!empty($action)) {
$markernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
if (! wp_verify_nonce($markernonce, 'marker-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').'');
  $layer = isset($_POST['layer']) ? intval($_POST['layer']) : 0;
  if ($action == 'add') {
	  if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
		global $current_user;
		get_currentuserinfo();
		//info: set values for wms checkboxes status
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
		$markername_quotes = str_replace("\"", "'", $_POST['markername']);	
		$result = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d )", $markername_quotes, $_POST['basemap'], $layer, floatval(str_replace(',', '.', $_POST['lat'])), floatval(str_replace(',', '.', $_POST['lon'])), $_POST['icon'], $_POST['popuptext'], $_POST['zoom'], $_POST['openpopup'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $_POST['panel'], $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox ); 
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully added','lmm') . '</div>' . __('Shortcode and API URLs','lmm') . ': <input style=\'width:200px;background:#f3efef;\' type=\'text\' value=\'[' . $lmm_options[ 'shortcode' ] . ' marker="' . $wpdb->insert_id . '"]\'>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $wpdb->insert_id . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a href=\'http://www.mapsmarker.com/kml\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $wpdb->insert_id . '\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> ' . __('Fullscreen','lmm') . '</a> <span title=\'' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></span>&nbsp;&nbsp;&nbsp;&nbsp;<a style=\'text-decoration:none;\' href=\'https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $wpdb->insert_id . '\' target=\'_blank\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> ' . __('QR code','lmm') . '</a> <span title=\'' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $wpdb->insert_id . '&callback=jsonp' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a href=\'http://www.mapsmarker.com/geojson\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $wpdb->insert_id . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" /> GeoRSS</a> <a href=\'http://www.mapsmarker.com/georss\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $wpdb->insert_id . '\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a href=\'http://www.mapsmarker.com/wikitude\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a><br/><br/>' . __('Please copy the shortcode above and paste it into the post or page where you want the map to appear or use one of the API URLs for embedding in external websites or apps','lmm') . '.<br/><br/><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $wpdb->insert_id . '\'>' . __('edit marker','lmm') . '</a>&nbsp;&nbsp;&nbsp;'. $layer_show_button . '<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('show all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new maker','lmm') . '</a></p>';
			if ( $lmm_options[ 'misc_global_stats' ] == 'enabled' ) { 
			echo '<p><iframe src="http://www.mapsmarker.com/counter/go.php?id=marker_add" frameborder="0" height="0" width="0" name="counter" scrolling="no"></iframe></p>';
			}
	   }
	   else 
	   {
		echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
	   }
  }
  elseif ($action == 'edit') {
	  if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
		global $current_user;
		get_currentuserinfo();
		//info: set values for wms checkboxes status
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
		$markername_quotes = str_replace("\"", "'", $_POST['markername']);	
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET markername = %s, basemap = %s, layer = %d, lat = %s, lon = %s, icon = %s, popuptext = %s, zoom = %d, openpopup = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %s, overlays_custom2 = %s, overlays_custom3 = %s, overlays_custom4 = %s, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d WHERE id = %d", $markername_quotes, $_POST['basemap'], $layer, floatval(str_replace(',', '.', $_POST['lat'])), floatval(str_replace(',', '.', $_POST['lon'])), $_POST['icon'], $_POST['popuptext'], $_POST['zoom'], $_POST['openpopup'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $_POST['panel'], $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $oid );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
	        echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully updated','lmm') . '</div>' . __('Shortcode and API URLs','lmm') . ': <input style=\'width:200px;background:#f3efef;\' type=\'text\' value=\'[' . $lmm_options[ 'shortcode' ] . ' marker="' . $_POST['id'] . '"]\'>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $_POST['id'] . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a href=\'http://www.mapsmarker.com/kml\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $_POST['id'] . '\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> ' . __('Fullscreen','lmm') . '</a> <span title=\'' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></span>&nbsp;&nbsp;&nbsp;&nbsp;<a style=\'text-decoration:none;\' href=\'https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $_POST['id'] . '\' target=\'_blank\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> ' . __('QR code','lmm') . '</a> <span title=\'' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $_POST['id'] . '&callback=jsonp' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a href=\'http://www.mapsmarker.com/geojson\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $_POST['id'] . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" /> GeoRSS</a> <a href=\'http://www.mapsmarker.com/georss\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $_POST['id'] . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a href=\'http://www.mapsmarker.com/wikitude\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></a><br/><br/>' . __('Please copy the shortcode above and paste it into the post or page where you want the map to appear or use one of the API URLs for embedding in external websites or apps','lmm') . '.<br/><br/><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $_POST['id'] . '\'>' . __('edit marker','lmm') . '</a>&nbsp;&nbsp;&nbsp;'. $layer_show_button . '<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('show all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
	    }
		else 
		{
		echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
    	}
  }
  elseif ($action == 'delete') {
    if (!empty($oid)) {
		$result = $wpdb->prepare( "DELETE FROM $table_name_markers WHERE id = %d", $oid );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
        echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully deleted','lmm') . '</div><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('show all markers','lmm') . '</a><br/><br/><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
    }
  }
}
else {
  //info: get icons list
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
  global $current_user;
  get_currentuserinfo();	  
  //info: get layers list
  $layerlist = $wpdb->get_results('SELECT * FROM '.$table_name_layers.' WHERE id>0', ARRAY_A);
  $id = '';
  $markername = '';
  $basemap = $lmm_options[ 'standard_basemap' ];
  $layer = '';
  $lat = floatval($lmm_options[ 'defaults_marker_lat' ]);
  $lon = floatval($lmm_options[ 'defaults_marker_lon' ]);
  $icon = '';
  $popuptext = '';
  $zoom = intval($lmm_options[ 'defaults_marker_zoom' ]);
  $openpopup = $lmm_options[ 'defaults_marker_openpopup' ];
  $mapwidth = intval($lmm_options[ 'defaults_marker_mapwidth' ]);
  $mapwidthunit = $lmm_options[ 'defaults_marker_mapwidthunit' ];
  $mapheight = intval($lmm_options[ 'defaults_marker_mapheight' ]);
  $panel = $lmm_options[ 'defaults_marker_panel' ];
  $mcreatedby = '';
  $mcreatedon = '';
  $mupdatedby = '';
  $mupdatedon = ''; 
  $controlbox = $lmm_options[ 'defaults_marker_controlbox' ];
  $overlays_custom = ( (isset($lmm_options[ 'defaults_marker_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
  $overlays_custom2 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
  $overlays_custom3 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
  $overlays_custom4 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
  $wms = ( (isset($lmm_options[ 'defaults_marker_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms_active' ] == 1 ) ) ? '1' : '0';
  $wms2 = ( (isset($lmm_options[ 'defaults_marker_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms2_active' ] == 1 ) ) ? '1' : '0';
  $wms3 = ( (isset($lmm_options[ 'defaults_marker_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms3_active' ] == 1 ) ) ? '1' : '0';
  $wms4 = ( (isset($lmm_options[ 'defaults_marker_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms4_active' ] == 1 ) ) ? '1' : '0';
  $wms5 = ( (isset($lmm_options[ 'defaults_marker_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms5_active' ] == 1 ) ) ? '1' : '0';
  $wms6 = ( (isset($lmm_options[ 'defaults_marker_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms6_active' ] == 1 ) ) ? '1' : '0';
  $wms7 = ( (isset($lmm_options[ 'defaults_marker_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms7_active' ] == 1 ) ) ? '1' : '0';
  $wms8 = ( (isset($lmm_options[ 'defaults_marker_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms8_active' ] == 1 ) ) ? '1' : '0';
  $wms9 = ( (isset($lmm_options[ 'defaults_marker_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms9_active' ] == 1 ) ) ? '1' : '0';
  $wms10 = ( (isset($lmm_options[ 'defaults_marker_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms10_active' ] == 1 ) ) ? '1' : '0';
  
  $isedit = isset($_GET['id']);
  if ($isedit) {
    $id = intval($_GET['id']);
    $row = $wpdb->get_row('SELECT markername,basemap,layer,lat,lon,icon,popuptext,zoom,openpopup,mapwidth,mapwidthunit,mapheight,panel,createdby,createdon,updatedby,updatedon,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10 FROM '.$table_name_markers.' WHERE id='.$id, ARRAY_A);
    $markername = $row['markername'];
    $basemap = $row['basemap'];
    $layer = $row['layer'];
    $lat = $row['lat'];
    $lon = $row['lon'];
    $icon = $row['icon'];
    $popuptext = $row['popuptext'];
    $zoom = $row['zoom'];
    $openpopup = $row['openpopup'];
    $mapwidth = $row['mapwidth'];
    $mapwidthunit = $row['mapwidthunit'];
    $mapheight = $row['mapheight'];
    $panel = $row['panel'];
    $mcreatedby = $row['createdby'];
    $mcreatedon = $row['createdon'];
    $mupdatedby = $row['updatedby'];
    $mupdatedon = $row['updatedon'];
    $controlbox = $row['controlbox'];
    $overlays_custom = $row['overlays_custom'];
    $overlays_custom2 = $row['overlays_custom2'];
    $overlays_custom3 = $row['overlays_custom3'];
    $overlays_custom4 = $row['overlays_custom4'];
    $wms = $row['wms'];
    $wms2 = $row['wms2'];
    $wms3 = $row['wms3'];
    $wms4 = $row['wms4'];
    $wms5 = $row['wms5'];
    $wms6 = $row['wms6'];
    $wms7 = $row['wms7'];
    $wms8 = $row['wms8'];
    $wms9 = $row['wms9'];
    $wms10 = $row['wms10'];
  }
?>
<?php //info: check if marker exists - part 1
if ($lat == NULL) {
$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $_GET['id']); 
echo '<p><div class="error" style="padding:10px;">' . $error_marker_not_exists . '</div></p>';
echo '<p><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('show all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
} else { ?>
	<?php $nonce= wp_create_nonce('marker-nonce'); ?>
	<form method="post">
		<?php wp_nonce_field('marker-nonce'); ?>
		<input type="hidden" name="id" value="<?php echo $id ?>" />
		<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
		<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
		<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $overlays_custom ?>" />
		<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $overlays_custom2 ?>" />
		<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $overlays_custom3 ?>" />
		<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $overlays_custom4 ?>" />		
		<h3><?php ($isedit === true) ? _e('Edit marker','lmm') : _e('Add new marker','lmm') ?>
			<?php echo ($isedit === true) ? '(ID '.$id.')' : '' ?></h3>
		
		<table class="widefat fixed">
			<tr style="background-color:#efefef;">
				<th class="column-parameter"><strong><?php _e('Parameter','lmm') ?></strong></th>
				<th class="column-value"><strong><?php _e('Value','lmm') ?></strong></th>
			</tr>
			<?php if ($isedit === true) { ?>
			<tr>
				<td><label for="shortcode"><strong><?php _e('Shortcode and API URLs','lmm') ?>:</strong></label></td>
				<td><input style="width:200px;background:#f3efef;" type="text" value="[<?php echo $lmm_options[ 'shortcode' ]; ?> marker=&quot;<?php echo $id?>&quot;]" readonly> <a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a href="http://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> <?php _e('Fullscreen','lmm'); ?></a> <span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://chart.googleapis.com/chart?chs=<?php echo $lmm_options[ 'misc_qrcode_size' ]; ?>x<?php echo $lmm_options[ 'misc_qrcode_size' ]; ?>&cht=qr&chl=<?php echo LEAFLET_PLUGIN_URL ?>'/leaflet-fullscreen.php?marker=<?php echo $id ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> <?php _e('QR code','lmm'); ?></a> <span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a href="http://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-georss.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoRSS</a> <a href="http://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a href="http://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br><small><?php _e('Use this shortcode in posts or pages on your website or one of the API URLs for embedding in external websites or apps','lmm') ?></small>
					</td>
			</tr>
			<?php } ?>
			<tr>
				<td><label for="markername"><strong><?php _e('Marker name','lmm') ?>:</strong></label></td>
				<td><input style="width:640px;" type="text" id="markername" name="markername" value="<?php echo stripslashes($markername) ?>" /></td>
			</tr>
			<tr>
				<td><label for="layer"><strong><?php _e('Layer','lmm') ?>:</strong></label></td>
				<td><?php if ($addtoLayer == NULL) { //info: addtoLayer part1/3 ?>
					<select id="layer" name="layer">
						<option value="0">
						<?php _e('Do not assign marker to a layer','lmm') ?>
						</option>
						<?php
							foreach ($layerlist as $row)
							echo '<option value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . '>' . stripslashes($row['name']) . ' (ID ' . $row['id'] . ')</option>';
						?>
					</select>
					<br>
					<small> <?php echo $layereditlink = ($layer != 0) ? "<a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=".$layer."\">" . __('edit layer','lmm') . " (ID ".$layer.")</a> " . __('or','lmm') . "" : "" ?> <a href="<?php WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer">
					<?php _e('add new layer','lmm') ?>
					</a></small> 
					<?php } else { //info: addtoLayer part2/3 ?>
					<input type="hidden" name="layer" value="<?php echo $addtoLayer ?>" />
					<a href="<?php WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer&id=<?php echo $addtoLayer ?>"><?php echo $layername ?> (ID <?php echo $addtoLayer ?>)</a>
					<?php } //info: addtoLayer part3/3 ?></td>
			</tr>
			<tr>
				<td><label for="coords"><strong><?php _e('Coordinates','lmm') ?>:</strong></label></td>
				<td><p><label for="placesearch"><?php _e('Please select a place or an address','lmm') ?></label> <?php if (current_user_can('activate_plugins')) { echo '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#google_places">' . __('(Settings)','lmm') . '</a>'; } ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/powered-by-google.png" /></a><br/>
					<input style="width: 640px;" type="text" id="placesearch" name="placesearch" value="<?php $placesearch = ''; echo $placesearch ?>" />
					<br>
					<?php _e('or paste coordinates here','lmm') ?> -  
					<?php _e('latitude','lmm') ?>: <input style="width: 100px;" type="text" id="lat" name="lat" value="<?php echo $lat; ?>" />
					<?php _e('longitude','lmm') ?>: <input style="width: 100px;" type="text" id="lon" name="lon" value="<?php echo $lon; ?>" />
					<br>
					<?php _e('or set marker coordinates by clicking on the preview map','lmm') ?>:</small></p></td>
			</tr>
			<tr>
				<td><p><label for="mapwidth"><strong><?php _e('Map size','lmm') ?>:</strong></label><br/>
					<?php _e('Width','lmm') ?>:
					<input size="2" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo $mapwidth ?>" />
					<input type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>>
					px&nbsp;&nbsp;&nbsp;
					<input type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>>%<br/>
					<?php _e('Height','lmm') ?>:
					<input size="2" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" />px
					<br/><br/>
					<label for="zoom"><strong><?php _e('Zoom','lmm') ?>:</strong></label><br/>
					<input style="width: 30px;" type="text" id="zoom" name="zoom" value="<?php echo $zoom ?>" readonly />
					<br>
					<small>
					<?php _e('Please change zoom level by clicking on + or - symbols or using your mouse wheel on preview map','lmm') ?>
					</small>
					<br/><br/>
					<label for="controlbox"><strong><?php _e('Basemap/overlay controlbox on frontend','lmm') ?>:</strong></label><br/>
					<input type="radio" name="controlbox" value="0" <?php checked($controlbox, 0); ?>><?php _e('hidden','lmm') ?><br/>
					<input type="radio" name="controlbox" value="1" <?php checked($controlbox, 1); ?>><?php _e('collapsed (except on mobiles)','lmm') ?><br/>
					<input type="radio" name="controlbox" value="2" <?php checked($controlbox, 2); ?>><?php _e('expanded','lmm') ?><br/>
					<small><?php _e('Controlbox on backend is always expanded','lmm') ?></small>
					<br/><br/>
					<label for="panel"><strong><?php _e('Panel for displaying marker name and API URLs on top of map','lmm') ?>:</strong></label><br/>
					<input type="radio" name="panel" value="1" <?php checked($panel, 1 ); ?>>
					<?php _e('show','lmm') ?><br/>
					<input type="radio" name="panel" value="0" <?php checked($panel, 0 ); ?>>
					<?php _e('hide','lmm') ?></p>
				</td>
				<td id="wmscheckboxes">
					<?php 
					echo '<div id="lmm" style="float:left;width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
					//info: panel for marker name and API URLs
					$panel_state = ($panel == 1) ? 'block' : 'none';
					echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]) . ';">'.PHP_EOL;
					echo '<div class="lmm-panel-api">';
						if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
								if ($lmm_options['directions_provider'] == 'googlemaps') {
									if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $yours_transport_type_icon = 'icon-walk.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
									$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
									$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
									$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
									$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
									echo '<a href="http://maps.google.com/maps?daddr=' . $lat . ',' . $lon . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . '&hl=' . $lmm_options[ 'directions_googlemaps_host_language' ] . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
								} else if ($lmm_options['directions_provider'] == 'yours') {
									if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $yours_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $yours_transport_type_icon = 'icon-walk.png'; }
									echo '<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
								} else if ($lmm_options['directions_provider'] == 'ors') {
									if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Pedestrian') { $yours_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
									echo '<a href="http://openrouteservice.org/index.php?end=' . $lon . ',' . $lat . '&pref=' . $lmm_options[ 'directions_ors_route_preferences' ] . '&lang=' . $lmm_options[ 'directions_ors_language' ] . '&noMotorways=' . $lmm_options[ 'directions_ors_no_motorways' ] . '&noTollways=' . $lmm_options[ 'directions_ors_no_tollways' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
								}
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as KML for Google Earth/Google Maps','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
						echo '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp" style="text-decoration:none;" title="' . __('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
						}
					echo '</div>'.PHP_EOL;
					echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ]) . '">' . (($markername == NULL) ? __('if set, markername will be inserted here','lmm') : stripslashes($markername)) . '</div>'.PHP_EOL;
					?>
					</div> <!--end lmm-panel-->
					<div id="selectlayer" style="height:<?php echo $mapheight; ?>px;"></div>
					</div><!--end mapsmarker div-->
					<div style="float:right;margin-top:10px;"><p><strong><?php _e('WMS layers','lmm') ?></strong> <?php if (current_user_can('activate_plugins')) { echo '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#wms">' . __('(Settings)','lmm') . '</a>'; } ?></p>
					<?php 
					//info: define available wms layers (for markers and layers) 
					if ( (isset($lmm_options[ 'wms_wms_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms" name="wms"';
						if ($wms == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms">' . htmlspecialchars_decode($lmm_options[ 'wms_wms_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms2_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms2_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms2" name="wms2"';
						if ($wms2 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms2">' . htmlspecialchars_decode($lmm_options[ 'wms_wms2_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms3_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms3_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms3" name="wms3"';
						if ($wms3 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms3">' . htmlspecialchars_decode($lmm_options[ 'wms_wms3_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms4_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms4_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms4" name="wms4"';
						if ($wms4 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms4">' . htmlspecialchars_decode($lmm_options[ 'wms_wms4_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms5_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms5_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms5" name="wms5"';
						if ($wms5 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms5">' . htmlspecialchars_decode($lmm_options[ 'wms_wms5_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms6_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms6_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms6" name="wms6"';
						if ($wms6 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms6">' . htmlspecialchars_decode($lmm_options[ 'wms_wms6_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms7_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms7_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms7" name="wms7"';
						if ($wms7 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms7">' . htmlspecialchars_decode($lmm_options[ 'wms_wms7_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms8_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms8_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms8" name="wms8"';
						if ($wms8 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms8">' . htmlspecialchars_decode($lmm_options[ 'wms_wms8_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms9_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms9_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms9" name="wms9"';
						if ($wms9 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms9">' . htmlspecialchars_decode($lmm_options[ 'wms_wms9_name' ]) . ' </label><br/>';
					}
					if ( (isset($lmm_options[ 'wms_wms10_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms10_available' ] == 1 ) ) {
						echo '<input type="checkbox" id="wms10" name="wms10"';
						if ($wms10 == 1) { echo ' checked="checked"'; }
						echo '/>&nbsp;<label for="wms10">' . htmlspecialchars_decode($lmm_options[ 'wms_wms10_name' ]) . ' </label>';
					}
					?>
				</div>		
				</td>
			</tr>
			
			<tr>
				<td><label for="icon"><strong><?php _e('Icon', 'lmm') ?>:</strong></label>
					<br/>
					<br/>
					<a title="Maps Icons Collection - http://mapicons.nicolasmollet.com" href="http://mapicons.nicolasmollet.com" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/logo-mapicons.gif" width="88" heigh="31" /></a><br/>
					<small>
					<?php	
					$mapicons_admin = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and upload the new icons to the directory %2$s/','lmm'), '<a href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a>', LEAFLET_PLUGIN_ICONS_URL); 
					$mapicons_user = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and ask your WordPress admin to upload the new icons to the directory %2$s/','lmm'), '<a href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a>', LEAFLET_PLUGIN_ICONS_URL); 
					if (current_user_can('activate_plugins')) { echo $mapicons_admin; } else { echo $mapicons_user; } 
					?>
					</small>
				</td>
				<td><div style="text-align:center;float:left;"><img src="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' ?>"/><br/>
						<input onchange="updateicon(this.value);" type="radio" name="icon" value="" <?php echo ($icon == NULL ? ' checked' : '')?>>
					</div>
					<?php
foreach ($iconlist as $row)
  echo '<div style="text-align:center;float:left;"><img id="iconpreview" src="' . LEAFLET_PLUGIN_ICONS_URL . '/' . $row . '" title="' . $row . '" alt="' . $row . '"/><br/><input onchange="updateicon(this.value);" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';
?></td>
			</tr>
			<tr>
				<td><label for="popuptext"><strong><?php _e('Popup text','lmm') ?>:</strong></label>
				<br /><br />
				<?php _e('open by default?','lmm') ?><br/>
				<input type="radio" name="openpopup" value="0" <?php checked($openpopup, 0 ); ?>>
				<?php _e('no','lmm') ?>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="openpopup" value="1" <?php checked($openpopup, 1); ?>>
				<?php _e('yes','lmm') ?><br/>
				<small>
				<?php _e('If no is selected, the popup will only be visible after clicking on the marker on marker- or layer-maps. If yes is selected, the popup is shown by default on marker-maps but not on layer-maps, where this feature is not supported','lmm') ?>
				</small></p>
				</td>
				<td>
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
						wp_editor( stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$popuptext)), 'popuptext', $settings);
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
					echo '<textarea id="popuptext" name="popuptext">' . stripslashes($popuptext) . '</textarea>';
					}
				?>
				<small>
					<?php _e('Note: image width gets resized to 260px automatically to fit in popup','lmm') ?>
					</small>
				</td>
			</tr>
			<?php if ($mcreatedby != NULL) {?>
			<tr>
				<td><small><strong><?php _e('Audit','lmm') ?>:</strong></small></td>
				<td><small>
					<?php _e('Marker added by','lmm') ?>
					<?php echo $mcreatedby ; ?> - <?php echo $mcreatedon ; ?>
					<?php if ($mupdatedby != NULL) { ?>,
					<?php _e('last update by','lmm') ?>
					<?php echo $mupdatedby ; ?> - <?php echo $mupdatedon ; ?>
					<?php }; ?>
					</small></td>
			</tr>
			<?php }; ?>
		</table>
	<div style="margin:20px 0 0 0;"><input style="font-weight:bold;" type="submit" name="marker" class="submit button-primary" value="<?php ($isedit === true) ? _e('update marker','lmm') : _e('add marker','lmm') ?> &raquo;" /></div>
	</form>
	
	<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
		<form method="post">
			<?php wp_nonce_field('marker-nonce'); ?>
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="action" value="delete" />
				<?php $confirm = sprintf( esc_attr__('Do you really want to delete marker %1$s (ID %2$s)?','lmm'), $markername, $id) ?>
				<div class="submit" style="margin:15px 0 0 0;">
				<input style="color:#FF0000;" type="submit" name="marker" value="<?php _e('delete marker', 'lmm') ?> &raquo;" onclick="return confirm('<?php echo $confirm ?>')" />
				</div>
		</form>
	<?php } ?>
</div>
<!--wrap--> 
<script type="text/javascript">
/* //<![CDATA[ */
var marker,selectlayer,osm_mapnik,osm_osmarender,mapquest_osm,mapquest_aerial,ogdwien_basemap,ogdwien_satellite,custom_basemap,custom_basemap2,custom_basemap3,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl;
(function($) {
  selectlayer = new L.Map("selectlayer", { crs: <?php echo $lmm_options['misc_projections'] ?> });
	<?php 
		$attrib_prefix = __("Plugin","lmm").': <a href=\"http://mapsmarker.com/go\" target=\"_blank\" title=\"powered by \'Leaflet Maps Marker\'-Plugin for WordPress\">MapsMarker.com</a> (<a href=\"http://leaflet.cloudmade.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses the JavaScript library \'Leaflet\' for interactive maps by CloudMade\">Leaflet</a>, <a href=\"http://mapicons.nicolasmollet.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses icons from the \'Maps Icons Collection\'\">Icons</a>)'; 
		//info: difference osm mapnik/osmarender + ogdwien basemap/satellite: style=\"\" -> if exactly the same, attribution link doesnt work
		$attrib_osm_mapnik = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\" style=\"\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
		$attrib_osm_osmarender = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
		$attrib_mapquest_osm = __("Map",'lmm').': Tiles Courtesy of <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'img/logo-mapquest.png\" style=\"\" />';
		$attrib_mapquest_aerial = __("Map",'lmm').': <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'img/logo-mapquest.png\" />, Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency';
		$attrib_ogdwien_basemap = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\" style=\"\">data.wien.gv.at</a>)';
		$attrib_ogdwien_satellite = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\">data.wien.gv.at</a>)';
		$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap_attribution' ]);
		$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap2_attribution' ]);
		$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap3_attribution' ]);
	?>
	selectlayer.attributionControl.setPrefix("<?php echo $attrib_prefix; ?>");
	
	osm_mapnik = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {mmid: 'osm_mapnik', maxZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_osm_mapnik; ?>"});
	osm_osmarender = new L.TileLayer("http://{s}.tah.openstreetmap.org/Tiles/tile/{z}/{x}/{y}.png", {mmid: 'osm_osmarender', maxZoom: 17, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_osm_osmarender; ?>"});
	mapquest_osm = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {mmid: 'mapquest_osm', maxZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_mapquest_osm; ?>", subdomains: ['otile1','otile2','otile3','otile4']});
	mapquest_aerial = new L.TileLayer("http://{s}.mqcdn.com/naip/{z}/{x}/{y}.png", {mmid: 'mapquest_aerial', maxZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_mapquest_aerial; ?>", subdomains: ['oatile1','oatile2','oatile3','oatile4']});
	ogdwien_basemap = new L.TileLayer("http://{s}.wien.gv.at/wmts/fmzk/pastell/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_basemap', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", maxZoom: 19, minZoom: 11, attribution: "<?php echo $attrib_ogdwien_basemap; ?>", subdomains: ['maps','maps1', 'maps2', 'maps3']});
	ogdwien_satellite = new L.TileLayer("http://{s}.wien.gv.at/wmts/lb/farbe/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_satellite', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", maxZoom: 19, minZoom: 11, attribution: "<?php echo $attrib_ogdwien_satellite; ?>", subdomains: ['maps','maps1', 'maps2', 'maps3']});
	//info: check if subdomains are set for custom basemaps
	<?php 
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	?>
	var custom_basemap = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap_tileurl' ] ?>", {mmid: 'custom_basemap', maxZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>});
	var custom_basemap2 = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap2_tileurl' ] ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>});
	var custom_basemap3 = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap3_tileurl' ] ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>});
	//info: check if subdomains are set for custom overlays
	<?php 
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	?>
	
	overlays_custom = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom_tileurl' ] ?>", {olid: 'overlays_custom', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom_minzoom' ]) ?><?php echo $overlays_custom_subdomains ?>});
	overlays_custom2 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom2_tileurl' ] ?>", {olid: 'overlays_custom2', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom2_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom2_minzoom' ]) ?><?php echo $overlays_custom2_subdomains ?>});
	overlays_custom3 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom3_tileurl' ] ?>", {olid: 'overlays_custom3', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom3_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom3_minzoom' ]) ?><?php echo $overlays_custom3_subdomains ?>});
	overlays_custom4 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom4_tileurl' ] ?>", {olid: 'overlays_custom4', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom4_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom4_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom4_minzoom' ]) ?><?php echo $overlays_custom4_subdomains ?>});
	//info: check if subdomains are set for wms layers
	<?php 
	$wms_subdomains = ((isset($lmm_options[ 'wms_wms_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms2_subdomains = ((isset($lmm_options[ 'wms_wms2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms3_subdomains = ((isset($lmm_options[ 'wms_wms3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms4_subdomains = ((isset($lmm_options[ 'wms_wms4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms5_subdomains = ((isset($lmm_options[ 'wms_wms5_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms5_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms5_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms6_subdomains = ((isset($lmm_options[ 'wms_wms6_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms6_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms6_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms7_subdomains = ((isset($lmm_options[ 'wms_wms7_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms7_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms7_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms8_subdomains = ((isset($lmm_options[ 'wms_wms8_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms8_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms8_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms9_subdomains = ((isset($lmm_options[ 'wms_wms9_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms9_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms9_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$wms10_subdomains = ((isset($lmm_options[ 'wms_wms10_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms10_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms10_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	
	//info: define wms legends
	$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms2_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms3_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms4_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms5_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms6_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms7_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms8_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms9_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms10_legend' ] . '&quot; target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . ''; 
	?>
	
	//info: define wms layers
	wms = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms_baseurl' ] ?>", {wmsid: 'wms', layers: '<?php echo addslashes($lmm_options[ 'wms_wms_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms_format' ])?>', attribution: '<?php echo $wms_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms_version' ])?>'<?php echo $wms_subdomains ?>});
	wms2 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms2_baseurl' ] ?>", {wmsid: 'wms2', layers: '<?php echo addslashes($lmm_options[ 'wms_wms2_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms2_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms2_format' ])?>', attribution: '<?php echo $wms2_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms2_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms2_version' ])?>'<?php echo $wms2_subdomains ?>});
	wms3 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms3_baseurl' ] ?>", {wmsid: 'wms3', layers: '<?php echo addslashes($lmm_options[ 'wms_wms3_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms3_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms3_format' ])?>', attribution: '<?php echo $wms3_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms3_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms3_version' ])?>'<?php echo $wms3_subdomains ?>});
	wms4 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms4_baseurl' ] ?>", {wmsid: 'wms4', layers: '<?php echo addslashes($lmm_options[ 'wms_wms4_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms4_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms4_format' ])?>', attribution: '<?php echo $wms4_attribution ?>', transparent: '<?php echo $lmm_options[ 'wms_wms4_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms4_version' ])?>'<?php echo $wms4_subdomains ?>});
	wms5 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms5_baseurl' ] ?>", {wmsid: 'wms5', layers: '<?php echo addslashes($lmm_options[ 'wms_wms5_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms5_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms5_format' ])?>', attribution: '<?php echo $wms5_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms5_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms5_version' ])?>'<?php echo $wms5_subdomains ?>});
	wms6 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms6_baseurl' ] ?>", {wmsid: 'wms6', layers: '<?php echo addslashes($lmm_options[ 'wms_wms6_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms6_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms6_format' ])?>', attribution: '<?php echo $wms6_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms6_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms6_version' ])?>'<?php echo $wms6_subdomains ?>});
	wms7 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms7_baseurl' ] ?>", {wmsid: 'wms7', layers: '<?php echo addslashes($lmm_options[ 'wms_wms7_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms7_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms7_format' ])?>', attribution: '<?php echo $wms7_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms7_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms7_version' ])?>'<?php echo $wms7_subdomains ?>});
	wms8 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms8_baseurl' ] ?>", {wmsid: 'wms8', layers: '<?php echo addslashes($lmm_options[ 'wms_wms8_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms8_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms8_format' ])?>', attribution: '<?php echo $wms8_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms8_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms8_version' ])?>'<?php echo $wms8_subdomains ?>});
	wms9 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms9_baseurl' ] ?>", {wmsid: 'wms9', layers: '<?php echo addslashes($lmm_options[ 'wms_wms9_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms9_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms9_format' ])?>', attribution: '<?php echo $wms9_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms9_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms9_version' ])?>'<?php echo $wms9_subdomains ?>});
	wms10 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms10_baseurl' ] ?>", {wmsid: 'wms10', layers: '<?php echo addslashes($lmm_options[ 'wms_wms10_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms10_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms10_format' ])?>', attribution: '<?php echo $wms10_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms10_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms10_version' ])?>'<?php echo $wms10_subdomains ?>});
	//info: controlbox - define basemaps
	layersControl = new L.Control.Layers( 
	{
	<?php 
		$basemaps_available = "";
		if ( (isset($lmm_options[ 'controlbox_osm_mapnik' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_mapnik' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_osm_mapnik' ]) . "': osm_mapnik,";
		if ( (isset($lmm_options[ 'controlbox_osm_osmarender' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_osmarender' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_osm_osmarender' ]) . "': osm_osmarender,";
		if ( (isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_mapquest_osm' ]) . "': mapquest_osm,";
		if ( (isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_mapquest_aerial' ]) . "': mapquest_aerial,";
		if ( (isset($lmm_options[ 'controlbox_ogdwien_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_basemap' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_ogdwien_basemap' ]) . "': ogdwien_basemap,";
		if ( (isset($lmm_options[ 'controlbox_ogdwien_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_satellite' ] == 1 ) )
			$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_ogdwien_satellite' ]) . "': ogdwien_satellite,";
		if ( (isset($lmm_options[ 'controlbox_custom_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap' ] == 1 ) )
			$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap_name' ])."': custom_basemap,";
		if ( (isset($lmm_options[ 'controlbox_custom_basemap2' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap2' ] == 1 ) )
			$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap2_name' ])."': custom_basemap2,";
		if ( (isset($lmm_options[ 'controlbox_custom_basemap3' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap3' ] == 1 ) )
			$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap3_name' ])."': custom_basemap3,";
		//info: needed for IE7 compatibility
		echo substr($basemaps_available, 0, -1);
	?>
	},
	
	//info: controlbox - define custom overlays 
	{
	<?php 
		$overlays_custom_available = "";
		if ( (isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 ) )
			$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom_name' ])."': overlays_custom,";
		if ( (isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 ) )
			$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom2_name' ])."': overlays_custom2,";
		if ( (isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 ) )
			$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom3_name' ])."': overlays_custom3,";
		if ( (isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 ) )
			$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom4_name' ])."': overlays_custom4,";
		//info: needed for IE7 compatibility
		echo substr($overlays_custom_available, 0, -1);
	?>
	},
	{ collapsed: false }); //info open layer control box by default on all devices on backend
  selectlayer.setView(new L.LatLng(<?php echo $lat . ', ' . $lon; ?>), <?php echo $zoom ?>);
  selectlayer.addLayer(<?php echo $basemap ?>)
	//info: controlbox - add active overlays on marker level
	<?php 
		if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
			echo ".addLayer(overlays_custom)";
		if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
			echo ".addLayer(overlays_custom2)";
		if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
			echo ".addLayer(overlays_custom3)";
		if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
			echo ".addLayer(overlays_custom4)";
	?>
	//info: controlbox - add active overlays on marker level
	<?php 
		if ( $wms == 1 )
			echo ".addLayer(wms)";
		if ( $wms2 == 1 )
			echo ".addLayer(wms2)";
		if ( $wms3 == 1 )
			echo ".addLayer(wms3)";
		if ( $wms4 == 1 )
			echo ".addLayer(wms4)";
		if ( $wms5 == 1 )
			echo ".addLayer(wms5)";
		if ( $wms6 == 1 )
			echo ".addLayer(wms6)";
		if ( $wms7 == 1 )
			echo ".addLayer(wms7)";
		if ( $wms8 == 1 )
			echo ".addLayer(wms8)";
		if ( $wms9 == 1 )
			echo ".addLayer(wms9)";
		if ( $wms10 == 1 )
			echo ".addLayer(wms10)";
	?>
  .addControl(layersControl); //.addLayer(wms)
  marker = new L.Marker(new L.LatLng(<?php echo $lat . ", " . $lon; ?>));
  <?php if ($icon != NULL) { ?>
  marker.options.icon = new L.Icon('<?php echo LEAFLET_PLUGIN_ICONS_URL . '/'.$icon ?>');
  <?php }?>
  selectlayer.addLayer(marker);
  
 <?php if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'googlemaps') ) { 
 $avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
 $avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
 $publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
 $walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
 $directions_settings_link = (current_user_can('activate_plugins')) ? ' (<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#directions">' . __('Settings','lmm') . '</a>)' : '';
 $popuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
 $popuptext = $popuptext . '<div style="' . $popuptext_css . '"><a href="http://maps.google.com/maps?daddr=' . $lat . ',' . $lon . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . '&hl=' . $lmm_options[ 'directions_googlemaps_host_language' ] . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>' . $directions_settings_link . '</div>'; 
 } else if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'yours') ) {
 $directions_settings_link = (current_user_can('activate_plugins')) ? ' (<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#directions">' . __('Settings','lmm') . '</a>)' : '';
 $popuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
 $popuptext = $popuptext . '<div style="' . $popuptext_css . '"><a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>' . $directions_settings_link . '</div>'; 
 } else if ( ($lmm_options['directions_popuptext_panel'] == 'yes') && ($lmm_options['directions_provider'] == 'ors') ) {
 $directions_settings_link = (current_user_can('activate_plugins')) ? ' (<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#directions">' . __('Settings','lmm') . '</a>)' : '';
 $popuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";
 $popuptext = $popuptext . '<div style="' . $popuptext_css . '"><a href="http://openrouteservice.org/index.php?end=' . $lon . ',' . $lat . '&pref=' . $lmm_options[ 'directions_ors_route_preferences' ] . '&lang=' . $lmm_options[ 'directions_ors_language' ] . '&noMotorways=' . $lmm_options[ 'directions_ors_no_motorways' ] . '&noTollways=' . $lmm_options[ 'directions_ors_no_tollways' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>' . $directions_settings_link . '</div>'; 
 }
 ?>
  
  marker.bindPopup('<?php echo preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$popuptext) ?>')<?php  if ($openpopup == 1) { echo '.openPopup()'; } ?>;
  <?php if ($lmm_options[ 'ogdvienna_selector' ] != 'disabled') { ?>
  //info: set OGD Vienna basemap if position between 48.321560/16.182175 and 48.116142/16.579056
  selectlayer.on('click', function(e) 
  {
		if( ('<?php echo $basemap ?>' != 'ogdwien_basemap') && ('<?php echo $basemap ?>' != 'ogdwien_satellite') && (e.latlng.lat.toFixed(6) <= 48.321560) && (e.latlng.lat.toFixed(6) >= 48.116142) && (e.latlng.lng.toFixed(6) >= 16.182175) && (e.latlng.lng.toFixed(6) <= 16.579056) ) 
		{
			selectlayer.attributionControl._attributions = [];
			selectlayer.removeLayer($('#basemap').val()).removeControl(layersControl).addLayer(<?php echo $lmm_options[ 'ogdvienna_selector' ] ?>);
				<?php if ( (isset($lmm_options[ 'ogdvienna_selector_addresses' ]) == TRUE) && ($lmm_options[ 'ogdvienna_selector_addresses' ] == 1) ) { ?>
				selectlayer.addLayer(overlays_custom);
				<?php }?>
			selectlayer.addControl(layersControl);
		}
  });
  //info: set basemap back to OSM if marker outside of Vienna boundaries
  selectlayer.on('click', function(e) 
  {
		if( (e.latlng.lat.toFixed(6) > 48.321560) || (e.latlng.lat.toFixed(6) < 48.116142) || (e.latlng.lng.toFixed(6) < 16.182175) || (e.latlng.lng.toFixed(6) > 16.579056) ) 
		{
			selectlayer.attributionControl._attributions = [];
			selectlayer.removeLayer(<?php echo $lmm_options[ 'ogdvienna_selector' ] ?>).removeControl(layersControl);
			if (('<?php echo $basemap ?>' == 'ogdwien_basemap') || ('<?php echo $basemap ?>' == 'ogdwien_satellite')) 
			{
				selectlayer.addLayer(osm_mapnik);
				selectlayer.removeLayer(overlays_custom);
			}
			selectlayer.addControl(layersControl);
			selectlayer.attributionControl.addAttribution("<?php echo $attrib_osm_mapnik ?>")
		}
  });
  <?php }?>
  //info: load wms layer when checkbox gets checked
	$('#wmscheckboxes input:checkbox').click(function(el) {
		if(el.target.checked) {
			selectlayer.addLayer(window[el.target.id]);
		} else {
			selectlayer.removeLayer(window[el.target.id]);
		}
		
	});
  //info: update basemap when chosing from control box
  selectlayer.on('layeradd', function(e) {
		if(e.layer.options.mmid) {
			$('#basemap').val(e.layer.options.mmid);
		}
  });
  //info: when custom overlay gets checked from control box update hidden field
  selectlayer.on('layeradd', function(e) {
		//info: when custom overlay gets checked from control box 
		if(e.layer.options.olid) {
			$('#'+e.layer.options.olid).attr('value', '1');
		}
  });
  //info: when custom overlay gets unchecked from control box update hidden field
  selectlayer.on('layerremove', function(e) {
		if(e.layer.options.olid) {
			$('#'+e.layer.options.olid).attr('value', '0');
		}
  });
  selectlayer.on('moveend', function(e) { document.getElementById('zoom').value = selectlayer.getZoom();});
  selectlayer.on('click', function(e) {
      selectlayer.setView(e.latlng,selectlayer.getZoom());
      document.getElementById('lat').value = e.latlng.lat.toFixed(6);
      document.getElementById('lon').value = e.latlng.lng.toFixed(6);
      marker.setLatLng(e.latlng);
      <?php if ($popuptext != NULL) { ?>
      marker.bindPopup('<?php echo preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$popuptext) ?>')<?php  if ($openpopup == 1) { echo '.openPopup()'; } ?>;
      <?php }?>
  });
  var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), popupText = $('#popuptext'), lat = $('#lat'), lon = $('#lon'), panel = $('#lmm-panel'), lmm = $('#lmm'), markername = $('#markername');
	markername.on('blur', function(e) { 
		document.getElementById('lmm-panel-text').innerHTML = markername.val();
	});
	mapWidth.blur(function() {
		if(!isNaN(mapWidth.val())) {
			lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
			selectlayer.invalidateSize();
		}
	});
	$('input:radio[name=mapwidthunit]').click(function() {
			lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
			selectlayer.invalidateSize();
	});
	mapHeight.blur(function() {
		if(!isNaN(mapHeight.val())) {
			mapElement.css("height",mapHeight.val()+"px"); 	 
			selectlayer.invalidateSize();
		}
	});
	//info: show/hide panel for markername & API URLs
	$('input:radio[name=panel]').click(function() {
		if($('input:radio[name=panel]:checked').val() == 1) {
			panel.css("display",'block');
		}
		if($('input:radio[name=panel]:checked').val() == 0) {
			panel.css("display",'none');
		}
	});
	$('input:radio[name=openpopup]').click(function() {
		if($('input:radio[name=openpopup]:checked').val() == 0) {
			marker.closePopup();
		} else {
			marker.openPopup();
		}
	});
	//info: check if lat is a number
	$('input:text[name=lat]').blur(function(e) {
		if(isNaN(lat.val())) {
                alert('<?php _e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: check if lon is a number
	$('input:text[name=lon]').blur(function(e) {
		if(isNaN(lon.val())) {
                alert('<?php _e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: sets map center to new marker position when entering lat/lon manually
	$('input:text[name=lat],input:text[name=lon]').blur(function(e) {
		var markerLocation = new L.LatLng(lat.val(),lon.val());
		marker.closePopup();
		marker.setLatLng(markerLocation);
		selectlayer.setView(markerLocation, selectlayer.getZoom());
		if($('input:radio[name=openpopup]:checked').val() == 1) {
			marker.openPopup();
		}
	});
})(jQuery)
function updateicon(newicon) {
  if(newicon) {
  marker.setIcon(new L.Icon('<?php echo LEAFLET_PLUGIN_ICONS_URL . '/' ?>' + newicon));
  }
  if(!newicon) {
  marker.setIcon(new L.Icon('<?php echo LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' ?>'));
  }
}
	gLoader = function(){
		var script;
		var init = false;
		var loaded = false;
		function check(){
			if(loaded) {
			} else {
				if(!init) {
					init = true;
					load();
				}
			}
		}
		function setup(){
			check(gLoader.loadMap);
		}
		function load(){
		script = document.createElement("script");
		script.type = "text/javascript";
			script.src = ('https:' == document.location.protocol ? 'https://www.google.com/jsapi?' : 'http://www.google.com/jsapi?') + 'callback=gLoader.loadMap';
		script.setAttribute("id", "googleloader");
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(script, s);
		}
		function loadMap() {
			google.load("maps", "3",  {callback: gLoader.autocomplete, other_params:"sensor=false&libraries=places&language=<?php if ( defined('WPLANG') ) { echo substr(WPLANG, 0, 2); } else { echo 'en'; } ?>"});  //info: get locale from wp-config with fallback if not set
		}
		function initAutocomplete() {
			var input = document.getElementById('placesearch');
			<?php if ($lmm_options[ 'google_places_bounds_status' ] == 'enabled') { ?>
			var defaultBounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(<?php echo floatval($lmm_options[ 'google_places_bounds_lat1' ]) ?>, <?php echo floatval($lmm_options[ 'google_places_bounds_lon1' ]) ?>),
				new google.maps.LatLng(<?php echo floatval($lmm_options[ 'google_places_bounds_lat2' ]) ?>, <?php echo floatval($lmm_options[ 'google_places_bounds_lon2' ]) ?>));
			<?php }?>
			var autocomplete = new google.maps.places.Autocomplete(input<?php if ($lmm_options[ 'google_places_bounds_status' ] == 'enabled') { echo ', {bounds: defaultBounds}'; } ?>);
			input.onfocus = function(){
			<?php if ($lmm_options[ 'google_places_search_prefix_status' ] == 'enabled' ) { ?>
			input.value = "<?php echo addslashes($lmm_options[ 'google_places_search_prefix' ]); ?>";
			<?php } ?>
			};
			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				var place = autocomplete.getPlace();
				var map = selectlayer;
				var markerLocation = new L.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
				marker.setLatLng(markerLocation);
				map.setView(markerLocation, selectlayer.getZoom());
				document.getElementById('lat').value = place.geometry.location.lat().toFixed(6);
				document.getElementById('lon').value = place.geometry.location.lng().toFixed(6);
				<?php if ($popuptext != NULL) { ?>
				marker.bindPopup('<?php echo preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$popuptext) ?>')<?php  if ($openpopup == 1) { echo '.openPopup()'; } ?>;
				<?php }?>
				<?php if ($lmm_options[ 'ogdvienna_selector' ] != 'disabled') { ?>
				//info: set OGD Vienna basemap if position between 48.321560/16.182175 and 48.116142/16.579056
				if ( ('<?php echo $basemap ?>' != 'ogdwien_basemap') && ('<?php echo $basemap ?>' != 'ogdwien_satellite') && (place.geometry.location.lat().toFixed(6) <= 48.321560) && (place.geometry.location.lat().toFixed(6) >= 48.116142) && (place.geometry.location.lng().toFixed(6) >= 16.182175) && (place.geometry.location.lng().toFixed(6) <= 16.579056) ) {
				selectlayer.attributionControl._attributions = [];
				selectlayer.removeControl(layersControl).addLayer(<?php echo $lmm_options[ 'ogdvienna_selector' ] ?>);
				<?php if ( (isset($lmm_options[ 'ogdvienna_selector_addresses' ]) == TRUE) && ($lmm_options[ 'ogdvienna_selector_addresses' ] == 1) ) { ?>
					selectlayer.addLayer(overlays_custom);
				<?php }?>
				selectlayer.addControl(layersControl);
				}
				//info: set basemap back to OSM if marker outside of Vienna boundaries
				if( (place.geometry.location.lat().toFixed(6) > 48.321560) || (place.geometry.location.lat().toFixed(6) < 48.116142) || (place.geometry.location.lng().toFixed(6) < 16.182175) || (place.geometry.location.lng().toFixed(6) > 16.579056) ) 
				{
					selectlayer.attributionControl._attributions = [];
					selectlayer.removeLayer(<?php echo $lmm_options[ 'ogdvienna_selector' ] ?>).removeControl(layersControl).addLayer(osm_mapnik);
					if (('<?php echo $basemap ?>' == 'ogdwien_basemap') || ('<?php echo $basemap ?>' == 'ogdwien_satellite'))
					{
					selectlayer.removeLayer(overlays_custom);
					}
					selectlayer.addControl(layersControl);					
					selectlayer.attributionControl.addAttribution("<?php echo $attrib_osm_mapnik ?>");
				}
				<?php }?>
			 });
			
			var input = document.getElementById('placesearch');
			google.maps.event.addDomListener(input, 'keydown', 
			function(e) {
							if (e.keyCode == 13)
							{
											if (e.preventDefault)
											{
															e.preventDefault();
											}
											else
											{
															//info:  Since the google event handler framework does not handle	early IE versions, we have to do it by our self. :-(
															e.cancelBubble = true;
															e.returnValue = false;
											}
							}
			});			
		}				
		return{
		setup:setup,
		check:check,
		loadMap:loadMap,
		autocomplete:initAutocomplete
		}
	}();
	gLoader.setup();
/* //]]> */
</script>
<?php //info: check if marker exists - part 2 
} ?>
<?php } ?>