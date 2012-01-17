<?php
/*
    Edit layer - Leaflet Maps Marker Plugin
*/
?>
<div class="wrap">
<?php include('leaflet-admin-header.php'); ?>
<?php
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$oid = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? intval($_GET['id']) : '');
$lat_check = isset($_POST['layerviewlat']) ? $_POST['layerviewlat'] : (isset($_GET['layerviewlat']) ? $_GET['layerviewlat'] : '');
$lon_check = isset($_POST['layerviewlon']) ? $_POST['layerviewlon'] : (isset($_GET['layerviewlon']) ? $_GET['layerviewlon'] : '');
if (!empty($action)) {
	$layernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($layernonce, 'layer-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').''); };
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
		$layername_quotes = str_replace("\"", "'", $_POST['name']);	
				
		$result = $wpdb->prepare( "INSERT INTO $table_name_layers (name, basemap, layerzoom, mapwidth, mapwidthunit, mapheight, panel, layerviewlat, layerviewlon, createdby, createdon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10 ) VALUES (%s, %s, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)", $layername_quotes, $_POST['basemap'], $_POST['layerzoom'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $_POST['panel'], floatval(str_replace(',', '.', $_POST['layerviewlat'])), floatval(str_replace(',', '.', $_POST['layerviewlon'])), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_layers" );
        echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully added','lmm') . '</div>' . __('Shortcode and API URLs', 'lmm') . ': <input style=\'width:185px;background:#f3efef;\' type=\'text\' value=\'['.$lmm_options[ 'shortcode' ].' layer="'.$wpdb->insert_id.'"]\'>&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $wpdb->insert_id . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" />  KML</a> <a href=\'http://www.mapsmarker.com/kml\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $wpdb->insert_id . '\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> ' . __('Fullscreen','lmm') . '</a> <span title=\'' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></span>&nbsp;&nbsp;&nbsp;&nbsp;<a style=\'text-decoration:none;\' href=\'https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $wpdb->insert_id . '\' target=\'_blank\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> ' . __('QR code','lmm') . '</a> <span title=\'' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $wpdb->insert_id . '&callback=jsonp' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a href=\'http://www.mapsmarker.com/geojson\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $wpdb->insert_id . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" /> GeoRSS</a> <a href=\'http://www.mapsmarker.com/georss\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $wpdb->insert_id . '\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a href=\'http://www.mapsmarker.com/wikitude\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a><br/><br/>' . __('Please copy the shortcode above and paste it into the post or page where you want the map to appear or use one of the API URLs for embedding in external websites or apps','lmm') . '.<br/><br/><a class=\'button-primary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $wpdb->insert_id . '&Layername=' . urlencode($_POST['name']) . '\'>' . __('add new marker to this layer','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id='.$wpdb->insert_id.'\'>' . __('edit layer', 'lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('show all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
			if ( $lmm_options[ 'misc_global_stats' ] == 'enabled' ) { 
			echo '<p><iframe src="http://www.mapsmarker.com/counter/go.php?id=layer_add" frameborder="0" height="0" width="0" name="counter" scrolling="no"></iframe></p>';
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
		$layername_quotes = str_replace("\"", "'", $_POST['name']);
				
		$result = $wpdb->prepare( "UPDATE $table_name_layers SET name = %s, basemap = %s, layerzoom = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, layerviewlat = %s, layerviewlon = %s, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %d, overlays_custom2 = %d, overlays_custom3 = %d, overlays_custom4 = %d, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d WHERE id = %d", $layername_quotes, $_POST['basemap'], $_POST['layerzoom'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $_POST['panel'], floatval(str_replace(',', '.', $_POST['layerviewlat'])), floatval(str_replace(',', '.', $_POST['layerviewlon'])), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $oid );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_layers" );
        echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully updated','lmm') . '</div>' . __('Shortcode and API URLs','lmm') . ': <input style=\'width:185px;background:#f3efef;\' type=\'text\' value=\'['.$lmm_options[ 'shortcode' ].' layer="'.$oid.'"]\'>&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $_POST['id'] . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a href=\'http://www.mapsmarker.com/kml\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $_POST['id'] . '\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> ' . __('Fullscreen','lmm') . '</a> <span title=\'' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></span>&nbsp;&nbsp;&nbsp;&nbsp;<a style=\'text-decoration:none;\' href=\'https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $_POST['id'] . '\' target=\'_blank\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> ' . __('QR code','lmm') . '</a> <span title=\'' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></span>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $_POST['id'] . '&callback=jsonp' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a href=\'http://www.mapsmarker.com/geojson\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $_POST['id'] . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" /> GeoRSS</a> <a href=\'http://www.mapsmarker.com/georss\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'/></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $_POST['id'] . '' .'\' target=\'_blank\' style=\'text-decoration:none;\'><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a href=\'http://www.mapsmarker.com/wikitude\' target=\'_blank\' title=\'' . esc_attr__('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') . '\'> <img src=\'' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png\' width=\'12\' height=\'12\' border=\'0\'></a><br/><br/>' . __('Please copy the shortcode above and paste it into the post or page where you want the map to appear or use one of the API URLs for embedding in external websites or apps','lmm') . '.<br/><br/><a class=\'button-primary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&addtoLayer=' . $_POST['id'] . '&Layername=' . urlencode($_POST['name']) . '\'>' . __('add new marker to this layer','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer&id='.$_POST['id'].'\'>' . __('edit layer','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('show all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
  }
  else 
	{
		echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
    }
  }
  elseif ($action == 'deleteboth') {
		$result = $wpdb->prepare( "DELETE FROM $table_name_markers WHERE layer = %d", $oid );
		$wpdb->query( $result );
		$result2 = $wpdb->prepare( "DELETE FROM $table_name_layers WHERE id = %d", $oid );
		$wpdb->query( $result2 );
		$wpdb->query( "OPTIMIZE TABLE $table_name_layers" );
        echo '<p><div class="updated" style="padding:10px;">' . __('Layer and assigned markers have been successfully deleted','lmm') . '</div><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('show all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
  }
elseif ($action == 'delete') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET layer = 0 WHERE layer = %d", $oid );
		$wpdb->query( $result );
		$result2 = $wpdb->prepare( "DELETE FROM $table_name_layers WHERE id = %d", $oid );
		$wpdb->query( $result2 );
		$wpdb->query( "OPTIMIZE TABLE $table_name_layers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully deleted (assigned markers have not been deleted)','lmm') . '</div><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('show all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
  }  
}
else {
  global $current_user;
  get_currentuserinfo();		
  $id = '';
  $name = '';
  $basemap = $lmm_options[ 'standard_basemap' ];
  $layerviewlat = floatval($lmm_options[ 'defaults_layer_lat' ]);
  $layerviewlon = floatval($lmm_options[ 'defaults_layer_lon' ]);
  $layerzoom = intval($lmm_options[ 'defaults_layer_zoom' ]);
  $mapwidth = intval($lmm_options[ 'defaults_layer_mapwidth' ]);
  $mapwidthunit = $lmm_options[ 'defaults_layer_mapwidthunit' ];
  $mapheight = intval($lmm_options[ 'defaults_layer_mapheight' ]);
  $panel = $lmm_options[ 'defaults_layer_panel' ];
  $lcreatedby = '';
  $lcreatedon = '';
  $lupdatedby = '';
  $lupdatedon = '';  
  $lcontrolbox = $lmm_options[ 'defaults_layer_controlbox' ];
  $loverlays_custom = ( (isset($lmm_options[ 'defaults_layer_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
  $loverlays_custom2 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
  $loverlays_custom3 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
  $loverlays_custom4 = ( (isset($lmm_options[ 'defaults_layer_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
  $wms = ( (isset($lmm_options[ 'defaults_layer_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms_active' ] == 1 ) ) ? '1' : '0';
  $wms2 = ( (isset($lmm_options[ 'defaults_layer_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms2_active' ] == 1 ) ) ? '1' : '0';
  $wms3 = ( (isset($lmm_options[ 'defaults_layer_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms3_active' ] == 1 ) ) ? '1' : '0';
  $wms4 = ( (isset($lmm_options[ 'defaults_layer_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms4_active' ] == 1 ) ) ? '1' : '0';
  $wms5 = ( (isset($lmm_options[ 'defaults_layer_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms5_active' ] == 1 ) ) ? '1' : '0';
  $wms6 = ( (isset($lmm_options[ 'defaults_layer_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms6_active' ] == 1 ) ) ? '1' : '0';
  $wms7 = ( (isset($lmm_options[ 'defaults_layer_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms7_active' ] == 1 ) ) ? '1' : '0';
  $wms8 = ( (isset($lmm_options[ 'defaults_layer_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms8_active' ] == 1 ) ) ? '1' : '0';
  $wms9 = ( (isset($lmm_options[ 'defaults_layer_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms9_active' ] == 1 ) ) ? '1' : '0';
  $wms10 = ( (isset($lmm_options[ 'defaults_layer_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_wms10_active' ] == 1 ) ) ? '1' : '0';
  $isedit = isset($_GET['id']);
  if ($isedit) {
    $id = intval($_GET['id']);
    $row = $wpdb->get_row('SELECT l.id as lid, l.name as lname, l.basemap as lbasemap, l.layerzoom as llayerzoom, l.mapwidth as lmapwidth, l.mapwidthunit as lmapwidthunit, l.mapheight as lmapheight, l.panel as lpanel, l.layerviewlat as llayerviewlat, l.layerviewlon as llayerviewlon, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon, l.controlbox as lcontrolbox, l.overlays_custom as loverlays_custom, l.overlays_custom2 as loverlays_custom2, l.overlays_custom3 as loverlays_custom3, l.overlays_custom4 as loverlays_custom4,l.wms as lwms, l.wms2 as lwms2, l.wms3 as lwms3, l.wms4 as lwms4, l.wms5 as lwms5, l.wms6 as lwms6, l.wms7 as lwms7, l.wms8 as lwms8, l.wms9 as lwms9, l.wms10 as lwms10, m.id as markerid, m.markername as markername, m.lat as mlat, m.lon as mlon, m.icon as micon, m.popuptext as mpopuptext, m.zoom as mzoom, m.mapwidth as mmapwidth, m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight FROM '.$table_name_layers.' as l LEFT OUTER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$id, ARRAY_A); 
    $name = $row['lname'];
    $basemap = $row['lbasemap'];
    $layerzoom = $row['llayerzoom'];	
    $mapwidth = $row['lmapwidth'];
    $mapwidthunit = $row['lmapwidthunit'];
    $mapheight = $row['lmapheight'];
    $layerviewlat = $row['llayerviewlat'];	
    $layerviewlon = $row['llayerviewlon'];		
    $markerid = $row['markerid'];
    $markername = $row['markername'];
    $mlat = $row['mlat'];
    $mlon = $row['mlon'];
    $coords = $mlat.', '.$mlon;
    $micon = $row['micon'];
    $popuptext = $row['mpopuptext'];
    $markerzoom = $row['mzoom'];
    $markermapwidth = $row['mmapwidth'];
    $markermapwidthunit = $row['mmapwidthunit'];
    $markermapheight = $row['mmapheight'];
    $panel = $row['lpanel'];
    $markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$id);
    $lcreatedby = $row['lcreatedby'];
    $lcreatedon = $row['lcreatedon'];
    $lupdatedby = $row['lupdatedby'];
    $lupdatedon = $row['lupdatedon'];
    $lcontrolbox = $row['lcontrolbox'];
    $loverlays_custom = $row['loverlays_custom'];
    $loverlays_custom2 = $row['loverlays_custom2'];
    $loverlays_custom3 = $row['loverlays_custom3'];
    $loverlays_custom4 = $row['loverlays_custom4'];	
    $wms = $row['lwms'];
    $wms2 = $row['lwms2'];
    $wms3 = $row['lwms3'];
    $wms4 = $row['lwms4'];
    $wms5 = $row['lwms5'];
    $wms6 = $row['lwms6'];
    $wms7 = $row['lwms7'];
    $wms8 = $row['lwms8'];
    $wms9 = $row['lwms9'];
    $wms10 = $row['lwms10'];	
  }
?>
<?php //info: check if layer exists - part 1
if ($layerviewlat == NULL) {
$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), $_GET['id']); 
echo '<p><div class="error" style="padding:10px;">' . $error_layer_not_exists . '</div></p>';
echo '<p><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('show all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
} else { ?>
	<?php $nonce= wp_create_nonce('layer-nonce'); ?>
	<form method="post">
		<?php wp_nonce_field('layer-nonce'); ?>
		<input type="hidden" name="id" value="<?php echo $id ?>" />
		<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
		<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
		<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $loverlays_custom ?>" />
		<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $loverlays_custom2 ?>" />
		<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $loverlays_custom3 ?>" />
		<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $loverlays_custom4 ?>" />	
		<h3><?php ($isedit === true) ? _e('Edit layer','lmm') : _e('Add new layer','lmm') ?>
			<?php echo ($isedit === true) ? '(ID '.$id.')' : '' ?></h3>
		<table class="widefat fixed">
			<tr style="background-color:#efefef;">
				<th class="column-parameter"><strong><?php _e('Parameter','lmm') ?></strong></th>
				<th class="column-value"><strong><?php _e('Value','lmm') ?></strong></th>
			</tr>
			<?php if ($isedit === true) { ?>
			<tr>
				<td><label for="shortcode"><strong><?php _e('Shortcode and API URLs','lmm') ?>:</strong></label></td>
				<td><input style="width:185px;background:#f3efef;" type="text" value="[<?php echo $lmm_options[ 'shortcode' ]; ?> layer=&quot;<?php echo $id?>&quot;]" readonly> <a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a href="http://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> <?php _e('Fullscreen','lmm'); ?></a> <span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://chart.googleapis.com/chart?chs=<?php echo $lmm_options[ 'misc_qrcode_size' ]; ?>x<?php echo $lmm_options[ 'misc_qrcode_size' ]; ?>&cht=qr&chl=<?php echo LEAFLET_PLUGIN_URL ?>'/leaflet-fullscreen.php?layer=<?php echo $id ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> <?php _e('QR code','lmm'); ?></a> <span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '&callback=jsonp' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a href="http://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" /> GeoRSS</a> <a href="http://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a href="http://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/><small><?php _e('Use this shortcode in posts or pages on your website or one of the API URLs for embedding in external websites or apps','lmm') ?></small>
					</td>
			</tr>
			<?php } ?>
			<tr>
				<td><label for="name"><strong><?php _e('Layer name', 'lmm') ?>:</strong></label></td>
				<td><input style="width: 640px;" maxlenght="255" type="text" id="layername" name="name" value="<?php echo stripslashes($name) ?>" /></td>
			</tr>
			<tr>
				<td><label for="coords"><strong><?php _e('Layer center','lmm') ?>:</strong></label></td>
				<td><p><label for="placesearch"><?php _e('Please select a place or an address','lmm') ?></label> <?php if (current_user_can('activate_plugins')) { echo '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#google_places">' . __('(Settings)','lmm') . '</a>'; } ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/powered-by-google.png" /></a><br/>
					<input style="width: 640px;" type="text" id="placesearch" name="placesearch" value="<?php $placesearch = ''; echo $placesearch ?>" />
					<br>
					<!--RH delete 					 <input style="width: 150px;" type="text" id="layerviewcoords" name="layerviewcoords" value="<x?php echo $layerviewcoords ?>" />-->
					<?php _e('or paste coordinates here','lmm') ?> -  
					<?php _e('latitude','lmm') ?>: <input style="width: 100px;" type="text" id="layerviewlat" name="layerviewlat" value="<?php echo $layerviewlat; ?>" />
					<?php _e('longitude','lmm') ?>: <input style="width: 100px;" type="text" id="layerviewlon" name="layerviewlon" value="<?php echo $layerviewlon; ?>" />
					<br>
					<?php _e('or set layer center by clicking on the preview map','lmm') ?>:</small></p></td>
			</tr>
			<tr>
				<td><p>
				<label for="mapsize"><strong><?php _e('Map size','lmm') ?>:</strong></label><br/>
				<?php _e('Width','lmm') ?>:
				<input size="2" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo $mapwidth ?>" />
				<input type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>>px&nbsp;&nbsp;&nbsp;
				<input type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>>%<br/>
				<?php _e('Height','lmm') ?>:
				<input size="2" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" />px
				<br/><br/>
				<label for="layerzoom"><strong><?php _e('Zoom','lmm') ?>:</strong></label><br/>
				<input style="width: 30px;" type="text" id="layerzoom" name="layerzoom" value="<?php echo $layerzoom ?>" readonly /><br/>
				<small>
				<?php _e('Please change zoom level by clicking on + or - symbols or using your mouse wheel on preview map','lmm') ?>
				</small>				
				<br/><br/>
				<label for="controlbox"><strong><?php _e('Basemap/overlay controlbox on frontend','lmm') ?>:</strong></label><br/>
				<input type="radio" name="controlbox" value="0" <?php checked($lcontrolbox, 0); ?>><?php _e('hidden','lmm') ?><br/>
				<input type="radio" name="controlbox" value="1" <?php checked($lcontrolbox, 1); ?>><?php _e('collapsed (except on mobiles)','lmm') ?><br/>
				<input type="radio" name="controlbox" value="2" <?php checked($lcontrolbox, 2); ?>><?php _e('expanded','lmm') ?><br/>
				<small><?php _e('Controlbox on backend is always expanded','lmm') ?></small>
				<br/><br/>
				<label for="panel"><strong><?php _e('Panel for displaying layer name and API URLs on top of map','lmm') ?>:</strong></label><br/>
				<input type="radio" name="panel" value="1" <?php checked($panel, 1 ); ?>>
				<?php _e('show','lmm') ?><br/>
				<input type="radio" name="panel" value="0" <?php checked($panel, 0 ); ?>>
				<?php _e('hide','lmm') ?></p>
				</td>
				<td id="wmscheckboxes">
					<?php 
					echo '<div id="lmm" style="float:left;width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
					//info: panel for layer name and API URLs
					$panel_state = ($panel == 1) ? 'block' : 'none';
					echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . addslashes($lmm_options[ 'defaults_layer_panel_background_color' ]) . ';">'.PHP_EOL;
					echo '<div class="lmm-panel-api">';
						if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as KML for Google Earth/Google Maps','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Open standalone map in fullscreen mode','lmm') . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
						echo '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '&callback=jsonp" style="text-decoration:none;" title="' . __('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
						}
						if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
						echo '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
						}
					echo '</div>'.PHP_EOL;
					echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ]) . '">' . (($name == NULL) ? __('if set, layername will be inserted here','lmm') : stripslashes($name)) . '</div>'.PHP_EOL;
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
			<?php if ($lcreatedby != NULL) {?>
			<tr>
				<td><small><strong><?php _e('Audit','lmm') ?>:</strong></small></td>
				<td><small>
					<?php _e('Layer added by','lmm') ?>
					<?php echo $lcreatedby ; ?> - <?php echo $lcreatedon ; ?>
					<?php if ($lupdatedby != NULL) { ?>,
					<?php _e('last update by','lmm') ?>
					<?php echo $lupdatedby ; ?> - <?php echo $lupdatedon ; ?>
					<?php }; ?>
					</small></td>
			</tr>
			<?php }; ?>
		</table>
		<div style="margin:20px 0 0 0;"><input style="font-weight:bold;" class="submit button-primary" type="submit" name="layer" value="<?php ($isedit === true) ? _e('update layer','lmm') : _e('add layer','lmm') ?> &raquo;" /></div>
	</form>
	</td>
	
	<?php if ( ($isedit) && (current_user_can( $lmm_options[ 'capabilities_delete' ]) )) { ?>
		<form method="post">
			<?php wp_nonce_field('layer-nonce'); ?>
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="action" value="delete" />
			<div class="submit" style="margin:15px 0 0 0;">
				<?php $confirm = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s)?','lmm'), $row['lname'], $id) ?>
				<input style="color:#FF0000;" type="submit" name="layer" value="<?php _e('delete layer', 'lmm') ?> &raquo;" onclick="return confirm('<?php echo $confirm ?>')"/>
			</div>
		</form>
		<form method="post">
			<?php wp_nonce_field('layer-nonce'); ?>
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="action" value="deleteboth" />
			<div class="submit">
				<?php $confirm2 = sprintf( esc_attr__('Do you really want to delete layer %1$s (ID %2$s) and all %3$s assigned markers?','lmm'), $row['lname'], $id, $markercount) ?>
				<input style="color:#FF0000;" type="submit" name="layer" value="<?php _e('delete layer AND assigned markers', 'lmm') ?> &raquo;" onclick="return confirm('<?php echo $confirm2 ?>')" />
			</div>
		</form>
	<?php } ?>
	
	<?php if ($isedit) { ?>
	<h3 id="assigned_markers">
		<?php _e('Marker assigned to this layer','lmm') ?>
	</h3>
	<p>
		<?php _e('Total','lmm') ?>: <?php echo $markercount; ?> <?php _e('marker','lmm') ?>
	</p>
	<p> <?php echo "<a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-add.png\" /></a> <a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id&Layername=" . urlencode(stripslashes($name)) . "\" style=\"text-decoration:none;\">" . __('add new marker to this layer','lmm') . "</a>"; ?> </p>
	<table cellspacing="0" class="wp-list-table widefat fixed bookmarks" style="width:auto;">
		<thead>
			<tr> 
					<!--<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>-->
					<th class="manage-column column-id" scope="col"><span>ID</span></span></th>
					<th class="manage-column column-icon" scope="col"><span><?php _e('Icon', 'lmm') ?></span></span></th>
					<th class="manage-column column-markername" scope="col"><span><?php _e('Marker name','lmm') ?></span></span></a></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext" scope="col"><span><?php _e('Popup text','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><span><?php _e('Popup status', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><span><?php _e('Zoom', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><span><?php _e('Basemap', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby" scope="col"><span><?php _e('Created by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon" scope="col"><span><?php _e('Created on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby" scope="col"><span><?php _e('Updated by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon" scope="col"><span><?php _e('Updated on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><span><?php _e('Controlbox status','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) { ?>
					<th class="manage-column column-kml" scope="col">KML<a href="http://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) { ?>
					<th class="manage-column column-fullscreen" scope="col"><?php _e('Fullscreen', 'lmm') ?><span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) { ?>
					<th class="manage-column column-qr-code" scope="col"><?php _e('QR code', 'lmm') ?><span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) { ?>
					<th class="manage-column column-geojson" scope="col">GeoJSON<a href="http://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) { ?>
					<th class="manage-column column-georss" scope="col">GeoRSS<a href="http://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) { ?>
					<th class="manage-column column-wikitude" scope="col">Wikitude<a href="http://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				</tr>
		</thead>
		<tfoot>
			<tr> 
					<!--<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>-->
					<th class="manage-column column-id" scope="col"><span>ID</span></span></th>
					<th class="manage-column column-icon" scope="col"><span><?php _e('Icon', 'lmm') ?></span></span></th>
					<th class="manage-column column-markername" scope="col"><span><?php _e('Marker name','lmm') ?></span></span></a></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext" scope="col"><span><?php _e('Popup text','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><span><?php _e('Popup status', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><span><?php _e('Zoom', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><span><?php _e('Basemap', 'lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby" scope="col"><span><?php _e('Created by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon" scope="col"><span><?php _e('Created on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby" scope="col"><span><?php _e('Updated by','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon" scope="col"><span><?php _e('Updated on','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><span><?php _e('Controlbox status','lmm') ?></span></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><?php _e('Shortcode', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) { ?>
					<th class="manage-column column-kml" scope="col">KML<a href="http://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) { ?>
					<th class="manage-column column-fullscreen" scope="col"><?php _e('Fullscreen', 'lmm') ?><span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) { ?>
					<th class="manage-column column-qr-code" scope="col"><?php _e('QR code', 'lmm') ?><span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></span></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) { ?>
					<th class="manage-column column-geojson" scope="col">GeoJSON<a href="http://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) { ?>
					<th class="manage-column column-georss" scope="col">GeoRSS<a href="http://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) { ?>
					<th class="manage-column column-wikitude" scope="col">Wikitude<a href="http://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>">&nbsp;<img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a></th><?php } ?>
				</tr>
		</tfoot>
		<tbody id="the-list">
			<?php
$markernonce = wp_create_nonce('marker-nonce'); //info: for delete-links
	//info: delete link
	if (current_user_can( $lmm_options[ 'capabilities_delete' ])) {
	 $confirm3 = sprintf( esc_attr__('Do you really want to delete marker %1$s (ID %2$s)?','lmm'), stripslashes($row['markername']), $row['markerid']);
		$delete_link_marker = ' | </span><span class="delete"><a onclick="if ( confirm( \'' . $confirm3 . '\' ) ) { return true;}return false;" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=delete&id='.$row['markerid'].'&_wpnonce=' . $markernonce . '" class="submitdelete">' . __('delete','lmm') . '</a></span>';
	} else {
		$delete_link_marker = '';
	}
$layermarklist = $wpdb->get_results('SELECT l.id as lid,l.name as lname,l.mapwidth as lmapwidth,l.mapheight as lmapheight,l.mapwidthunit as lmapwidthunit,l.layerzoom as llayerzoom,l.layerviewlat as llayerviewlat,l.layerviewlon as llayerviewlon, m.lon as mlon, m.lat as mlat, m.icon as micon, m.popuptext as mpopuptext,m.markername as markername,m.id as markerid,m.mapwidth as mmapwidth,m.mapwidthunit as mmapwidthunit,m.mapheight as mmapheight,m.zoom as mzoom,m.openpopup as mopenpopup, m.basemap as mbasemap, m.controlbox as mcontrolbox, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$id, ARRAY_A);
  if (count($layermarklist) < 1)
    echo '<tr><td colspan="8">'.__('No marker assigned to this layer', 'lmm').'</td></tr>';
  else
    foreach ($layermarklist as $row){
	//info: set column display variables - need for for-each
	$column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ?
'<td>' . $row['mopenpopup'] . '</td>' : '';
	$column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td>Lat: ' . $row['mlat'] . '<br/>Lon: ' . $row['mlon'] . '</td>' : '';
	$column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td>' . __('Width','lmm') . ': '.$row['mmapwidth'].$row['mmapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mmapheight'].'px</td>' : '';
	$column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;">' . $row['mzoom'] . '</td>' : '';
	$column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;">'.$row['mcontrolbox'].'</td>' : '';
	$column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td><input style="width:185px;background:#f3efef;" type="text" value="[' . $lmm_options[ 'shortcode' ] . ' marker=&quot;' . $row['markerid'] . '&quot;]" readonly></td>' : '';
	$column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" /><br/>KML</a></td>' : '';
    $column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
    $column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;"><a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo"><br/>' . __('QR code','lmm') . '</a></td>' : '';
	$column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $row['markerid'] . '&callback=jsonp" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-logo"><br/>GeoJSON</a></td>' : '';
     $column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo"><br/>GeoRSS</a></td>' : '';
	$column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $row['markerid'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-logo"><br/>Wikitude</a></td>' : '';
	$column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td >' . $row['mbasemap'] . '</td>' : '';
	$column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td >' . $row['mcreatedby'] . '</td>' : '';
	$column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td >' . $row['mcreatedon'] . '</td>' : '';
	$column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td >' . $row['mupdatedby'] . '</td>' : '';
	$column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td >' . $row['mupdatedon'] . '</td>' : '';
	$openpopupstatus = ($row['mopenpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
	$popuptextabstract = (strlen($row['mpopuptext']) >= 90) ? "...": "";
	$column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ?
'<td><a title="' . __('Edit marker ', 'lmm') . ' ' . $row['markerid'] . '" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'] . '" >' . mb_substr(strip_tags(stripslashes($row['mpopuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
	echo '<tr valign="middle" class="alternate" id="link-'.$row['markerid'].'">
      <td>'.$row['markerid'].'</td>
      <td>';
      if ($row['micon'] != null) { 
         echo '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/'.$row['micon'].'" title="'.$row['micon'].'" />'; 
         } else { 
         echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';};
      echo '</td>
      <td><strong><a title="' . esc_attr__('Edit marker','lmm') . ' (ID ' . $row['markerid'].')" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['markerid'].'" class="row-title">' . stripslashes($row['markername']) . '</a></strong><br/><div class="row-actions"><span class="edit"><a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$row['markerid'].'">' . __('edit','lmm') . '</a>' . $delete_link_marker . '</div></td>	  
	' . $column_popuptext . '
	' . $column_openpopup . '
	' . $column_coordinates . '
	' . $column_mapsize . '
	' . $column_zoom . '
	' . $column_basemap . '
	' . $column_createdby . '
	' . $column_createdon . '
	' . $column_updatedby . '
	' . $column_updatedon . '
	' . $column_controlbox . '
	' . $column_shortcode . '
	' . $column_kml . '
	' . $column_fullscreen . '
	' . $column_qr_code . '
	' . $column_geojson . '
	' . $column_georss . '
	' . $column_wikitude . '
      </tr>';
}//info: end foreach
?>
		</tbody>
	</table>
	<p> <?php echo "<a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-add.png\" /></a> <a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker&addtoLayer=$id&Layername=" . urlencode(stripslashes($name)) . "\" style=\"text-decoration:none;\">" . __('add new marker to this layer','lmm') . "</a>"; ?> </p>
	<?php } ?>
	<!--isedit--> 
</div>
<!--wrap--> 
<script type="text/javascript">
/* //<![CDATA[ */
var marker,selectlayer,osm_mapnik,osm_osmarender,mapquest_osm,mapquest_aerial,ogdwien_basemap,ogdwien_satellite,custom_basemap,custom_basemap2,custom_basemap3,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl;
var markers = {};
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
	
	osm_mapnik = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {mmid: 'osm_mapnik', maxZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_osm_mapnik; ?>"});
	osm_osmarender = new L.TileLayer("http://{s}.tah.openstreetmap.org/Tiles/tile/{z}/{x}/{y}.png", {mmid: 'osm_osmarender', maxZoom: 17, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_osm_osmarender; ?>"});
	mapquest_osm = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {mmid: 'mapquest_osm', maxZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_mapquest_osm; ?>", subdomains: ['otile1','otile2','otile3','otile4']});
	mapquest_aerial = new L.TileLayer("http://{s}.mqcdn.com/naip/{z}/{x}/{y}.png", {mmid: 'mapquest_aerial', maxZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_mapquest_aerial; ?>", subdomains: ['oatile1','oatile2','oatile3','oatile4']});
	ogdwien_basemap = new L.TileLayer("http://{s}.wien.gv.at/wmts/fmzk/pastell/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_basemap', maxZoom: 19, minZoom: 11, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_ogdwien_basemap; ?>", subdomains: ['maps','maps1', 'maps2', 'maps3']});
	ogdwien_satellite = new L.TileLayer("http://{s}.wien.gv.at/wmts/lb/farbe/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_satellite', maxZoom: 19, minZoom: 11, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_ogdwien_satellite; ?>", subdomains: ['maps','maps1', 'maps2', 'maps3']});
	//info: check if subdomains are set for custom basemaps
	<?php 
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	?>
	custom_basemap = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap_tileurl' ] ?>", {mmid: 'custom_basemap', maxZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>});
	custom_basemap2 = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap2_tileurl' ] ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>});
	custom_basemap3 = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap3_tileurl' ] ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>});
	//info: check if subdomains are set for custom overlays
	<?php 
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	?>
	overlays_custom = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom_tileurl' ] ?>", {olid: 'overlays_custom', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom_minzoom' ]) ?><?php echo $overlays_custom_subdomains ?>});
	overlays_custom2 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom2_tileurl' ] ?>", {olid: 'overlays_custom2', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom2_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom2_minzoom' ]) ?><?php echo $overlays_custom2_subdomains ?>});
	overlays_custom3 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom3_tileurl' ] ?>", {olid: 'overlays_custom3', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom3_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom3_minzoom' ]) ?><?php echo $overlays_custom3_subdomains ?>});
	overlays_custom4 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom4_tileurl' ] ?>", {olid: 'overlays_custom4', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom4_attribution' ]) ?>", maxZoom: <?php echo intval($lmm_options[ 'overlays_custom4_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom4_minzoom' ]) ?><?php echo $overlays_custom4_subdomains ?>});
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
	$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms2_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms3_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms4_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms5_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms6_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms7_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms8_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms9_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? ' (<a href=&quot;' . $lmm_options[ 'wms_wms10_legend' ] . '&quot; target=&quot;_blank&quot; >' . __('Legend','lmm') . '</a>)' : '') . ''; 
	?>
	
	//info: define wms layers
	wms = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms_baseurl' ] ?>", {wmsid: 'wms', layers: '<?php echo addslashes($lmm_options[ 'wms_wms_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms_format' ])?>', attribution: '<?php echo $wms_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms_version' ])?>'<?php echo $wms_subdomains ?>});
	wms2 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms2_baseurl' ] ?>", {wmsid: 'wms2', layers: '<?php echo addslashes($lmm_options[ 'wms_wms2_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms2_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms2_format' ])?>', attribution: '<?php echo $wms2_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms2_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms2_version' ])?>'<?php echo $wms2_subdomains ?>});
	wms3 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms3_baseurl' ] ?>", {wmsid: 'wms3', layers: '<?php echo addslashes($lmm_options[ 'wms_wms3_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms3_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms3_format' ])?>', attribution: '<?php echo $wms3_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms3_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms3_version' ])?>'<?php echo $wms3_subdomains ?>});
	wms4 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms4_baseurl' ] ?>", {wmsid: 'wms4', layers: '<?php echo addslashes($lmm_options[ 'wms_wms4_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms4_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms4_format' ])?>', attribution: '<?php echo $wms4_attribution ?>', transparent: '<?php echo $lmm_options[ 'wms_wms4_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms4_version' ])?>'<?php echo $wms4_subdomains ?>});
	wms5 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms5_baseurl' ] ?>", {wmsid: 'wms5', layers: '<?php echo addslashes($lmm_options[ 'wms_wms5_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms5_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms5_format' ])?>', attribution: '<?php echo $wms5_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms5_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms5_version' ])?>'<?php echo $wms5_subdomains ?>});
	wms6 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms6_baseurl' ] ?>", {wmsid: 'wms6', layers: '<?php echo addslashes($lmm_options[ 'wms_wms6_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms6_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms6_format' ])?>', attribution: '<?php echo $wms6_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms6_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms6_version' ])?>'<?php echo $wms6_subdomains ?>});
	wms7 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms7_baseurl' ] ?>", {wmsid: 'wms7', layers: '<?php echo addslashes($lmm_options[ 'wms_wms7_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms7_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms7_format' ])?>', attribution: '<?php echo $wms7_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms7_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms7_version' ])?>'<?php echo $wms7_subdomains ?>});
	wms8 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms8_baseurl' ] ?>", {wmsid: 'wms8', layers: '<?php echo addslashes($lmm_options[ 'wms_wms8_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms8_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms8_format' ])?>', attribution: '<?php echo $wms8_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms8_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms8_version' ])?>'<?php echo $wms8_subdomains ?>});
	wms9 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms9_baseurl' ] ?>", {wmsid: 'wms9', layers: '<?php echo addslashes($lmm_options[ 'wms_wms9_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms9_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms9_format' ])?>', attribution: '<?php echo $wms9_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms9_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms9_version' ])?>'<?php echo $wms9_subdomains ?>});
	wms10 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms10_baseurl' ] ?>", {wmsid: 'wms10', layers: '<?php echo addslashes($lmm_options[ 'wms_wms10_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms10_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms10_format' ])?>', attribution: '<?php echo $wms10_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms10_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms10_version' ])?>'<?php echo $wms10_subdomains ?>});
	
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
	{ collapsed: false }); //info: open layer control box by default on all devices on backend
	
  selectlayer.setView(new L.LatLng(<?php echo $layerviewlat . ', ' . $layerviewlon; ?>), <?php echo $layerzoom ?>);
  selectlayer.addLayer(<?php echo $basemap ?>)
  
	//info: controlbox - check active overlays on layer level
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
	//info: controlbox - add active overlays on layer level
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
		
  .addControl(layersControl);
  mapcentermarker = new L.Marker(new L.LatLng(<?php echo $layerviewlat . ', ' . $layerviewlon; ?>));
  mapcentermarker.options.icon = new L.Icon('<?php echo LEAFLET_PLUGIN_URL . 'img/icon-layer-center.png' ?>');
  
  var layers = {};
  var geojson = new L.GeoJSON();
  geojson.on("featureparse",  function(e) {
  		if (typeof e.properties.text != 'undefined') e.layer.bindPopup(e.properties.text);
  		e.layer.options.icon = new L.Icon(e.properties.icon);
  layers[e.properties.layer] = e.properties.layername;
  if (typeof markers[e.properties.layer] == 'undefined') markers[e.properties.layer] = [];
  markers[e.properties.layer].push(e.layer);
  });
  var geojsonObj;
  geojsonObj = eval("(" + jQuery.ajax({url: "<?php echo LEAFLET_PLUGIN_URL ?>/leaflet-geojson.php?layer=<?php echo $id ?>", async: false}).responseText + ")");
  geojson.addGeoJSON(geojsonObj);
  selectlayer.addLayer(mapcentermarker).addLayer(geojson);
  
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
  selectlayer.on('moveend', function(e) { document.getElementById('layerzoom').value = selectlayer.getZoom();});	  
  selectlayer.on('click', function(e) {
      document.getElementById('layerviewlat').value = e.latlng.lat.toFixed(6);
      document.getElementById('layerviewlon').value = e.latlng.lng.toFixed(6);
      selectlayer.setView(e.latlng,selectlayer.getZoom());
      mapcentermarker.setLatLng(e.latlng);
  });
  var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), layerviewlat = $('#layerviewlat'), layerviewlon = $('#layerviewlon'), panel = $('#lmm-panel'), lmm = $('#lmm'), layername = $('#layername');
	layername.on('blur', function(e) { 
		document.getElementById('lmm-panel-text').innerHTML = layername.val();
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
	//info: check if layerviewlat is a number
	$('input:text[name=layerviewlat]').blur(function(e) {
		if(isNaN(layerviewlat.val())) {
                alert('<?php _e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: check if layerviewlon is a number
	$('input:text[name=layerviewlon]').blur(function(e) {
		if(isNaN(layerviewlon.val())) {
                alert('<?php _e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
		}
	});
	//info: sets map center to new layer center position when entering lat/lon manually
	$('input:text[name=layerviewlat],input:text[name=layerviewlon]').blur(function(e) {
		var mapcentermarker_new = new L.LatLng(layerviewlat.val(),layerviewlon.val());
		mapcentermarker.setLatLng(mapcentermarker_new);
		selectlayer.setView(mapcentermarker_new, selectlayer.getZoom());
	});
})(jQuery)
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
				mapcentermarker.setLatLng(markerLocation);
				map.setView(markerLocation, selectlayer.getZoom());
				document.getElementById('layerviewlat').value = place.geometry.location.lat().toFixed(6);
				document.getElementById('layerviewlon').value = place.geometry.location.lng().toFixed(6);
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
															//info: Since the google event handler framework does not handle	early IE versions, we have to do it by our self. :-(
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