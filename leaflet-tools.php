<?php
/*
    Tools - Leaflet Maps Marker Plugin
*/
?>
<div class="wrap">
	<?php include('leaflet-admin-header.php'); ?>
	<?php
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
if (!empty($action)) {
	$layernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($layernonce, 'layer-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').''); };
  if ($action == 'add') {

  }
  elseif ($action == 'edit') {
		$result = $wpdb->prepare( "UPDATE $table_name_layers SET name = %s, basemap = %s, layerzoom = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, layerviewlat = %s, layerviewlon = %s, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %d, overlays_custom2 = %d, overlays_custom3 = %d, overlays_custom4 = %d, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d WHERE id = %d", $layername_quotes, $_POST['basemap'], $_POST['layerzoom'], $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $_POST['panel'], floatval(str_replace(',', '.', $_POST['layerviewlat'])), floatval(str_replace(',', '.', $_POST['layerviewlon'])), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $oid );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_layers" );
  }
  elseif ($action == 'deleteboth') {

  }
elseif ($action == 'delete') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET layer = 0 WHERE layer = %d", $oid );
		$wpdb->query( $result );
		$result2 = $wpdb->prepare( "DELETE FROM $table_name_layers WHERE id = %d", $oid );
		$wpdb->query( $result2 );
		$wpdb->query( "OPTIMIZE TABLE $table_name_layers" );
		echo '<p><div class="updated" style="padding:10px;">' . __('Layer has been successfully deleted (assigned markers have not been deleted)','lmm') . '</div><a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers\'>' . __('show all layers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary\' href=\'' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer\'>' . __('add new layer','lmm') . '</a></p>';
		if ( $lmm_options[ 'misc_global_stats' ] == 'enabled' ) { 
		echo '<p><iframe src="http://www.mapsmarker.com/counter/go.php?id=layer_delete" frameborder="0" height="0" width="0" name="counter" scrolling="no"></iframe></p>';
		}	
  }  
}
else {
  global $current_user;
  get_currentuserinfo();		

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
?>

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
				<td><input style="width: 640px;" maxlenght="255" type="text" name="name" value="<?php echo stripslashes($name) ?>" /></td>
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
				<td>
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
	
</div>
<!--wrap--> 

<?php } ?>