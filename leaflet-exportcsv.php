<?php
/*
    Export all markers as CVS file - Leaflet Maps Marker Plugin
*/
//info: construct path to wp-load.php
while(!is_file('wp-load.php')){
  if(is_dir('../')) chdir('../');
  else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
function hide_email($email) { $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'; $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999); for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])]; $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";'; $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));'; $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"'; $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script; }
//info: check if plugin is active (didnt use is_plugin_active() due to problems reported by users)
function lmm_is_plugin_active( $plugin ) { return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ); }
if ( !lmm_is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php') ) {
echo 'The WordPress plugin <a href="http://www.mapsmarker.com" target="_blank">Leaflet Maps Marker</a> is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (' . hide_email(get_bloginfo('admin_email')) . ') who can activate this plugin again.';
} else {
   global $wpdb;
   $noncelink = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : ''; 
   if (! wp_verify_nonce($noncelink, 'exportcsv-nonce') ) die("".__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm')."");
   $lmm_options = get_option( 'leafletmapsmarker_options' );
   if (current_user_can($lmm_options[ 'capabilities_edit' ])) { 
   $rows = array();
        $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$objects = $wpdb->get_results('SELECT m.id as mid, l.name as layername,m.lat as lat, m.popuptext as popuptext, m.openpopup as openpopup, m.lon as lon,m.icon as icon, m.zoom as zoom, m.mapwidth as mapwidth, m.mapwidthunit as mapwidthunit, m.mapheight as mapheight, m.markername as markername, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon FROM '.$table_name_markers.' as m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id order by m.id',OBJECT_K);
	foreach ($objects as $row) {
		$columns = array();
		$columns['id'] = $row->mid;
		$columns['markername'] = stripslashes(str_replace(';', ',', $row->markername));
		$columns['layername'] = stripslashes(str_replace(';', ',', $row->layername));
		$columns['popuptext'] = preg_replace('/(\015\012)|(\015)|(\012)/',' ',strip_tags(stripslashes(str_replace(';', ',', $row->popuptext))));
		$columns['openpopup'] = $row->openpopup;
		$columns['lat'] = str_replace('.', ',', $row->lat);
		$columns['lon'] = str_replace('.', ',', $row->lon);
	    if ($row->icon == null) {
	        $columns['icon'] = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
	    } else {
	        $columns['icon'] = LEAFLET_PLUGIN_ICONS_URL . '/' . $row->icon; 
    	}
		
		$columns['zoom'] = $row->zoom;
		$columns['mapwidth'] = $row->mapwidth;
		$columns['mapwidthunit'] = $row->mapwidthunit;
		$columns['mapheight'] = $row->mapheight;
		$columns['mapheightunit'] = 'px';
		$columns['m.createdby'] = $row->mcreatedby;
		$columns['m.createdon'] = $row->mcreatedon;
		$columns['m.updatedby'] = $row->mupdatedby;
		$columns['m.updatedon'] = $row->mupdatedon;
		$columns['l.createdby'] = $row->lcreatedby;
		$columns['l.createdon'] = $row->lcreatedon;
		$columns['l.updatedby'] = $row->lupdatedby;
		$columns['l.updatedon'] = $row->lupdatedon;
		$rows[] = join(';',$columns); 
	}
        $header = "Markerid;Markername;Layername;PopupText;OpenPopup;Latitude;Longitude;Icon;Zoom;Mapwidth;MapwidthUnit;Mapheight;MapheightUnit;MarkerCreatedBy;MarkerCreatedOn;MarkerUpdatedBy;MarkerUpdatedOn;LayerCreatedBy;LayerCreatedOn;LayerUpdatedBy;LayerUpdatedOn";
	$file = $header."\n".join("\n",$rows); 
	header('Content-Type: text/plain; charset=UTF-8');
	echo $file;
exit;
	
} else {
	_e('Error - CSV export of all markers not allowed.','lmm');
}
} //info: end plugin active check
?>