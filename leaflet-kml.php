<?php
/*
    KML generator - Leaflet Maps Marker Plugin
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
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
if (isset($_GET['layer'])) {
  $layer = mysql_real_escape_string($_GET['layer']);
  
  $q = 'LIMIT 0';
  if ($layer == '*' or $layer == 'all')
    $q = 'LIMIT 1000';
  else {
    $layers = explode(',', $layer);
    $checkedlayers = array();
    foreach ($layers as $clayer) {
      if (intval($clayer) > 0)
        $checkedlayers[] = intval($clayer);
    }
    if (count($checkedlayers) > 0)
      $q = 'WHERE layer IN ('.implode(',', $checkedlayers).')';
  }
  $sql = 'SELECT m.id as mid, m.markername as mmarkername, m.layer as mlayer, m.icon as micon, m.createdby as mcreatedby, m.createdon as mcreatedon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext, l.createdby as lcreatedby, l.createdon as lcreatedon, l.name AS lname FROM '.$table_name_markers.' AS m INNER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  //info: check if layer result is not null
  if (empty($markers)) {
  $error_layers_not_exists = sprintf( esc_attr__('Warning: no markers are assigned to the layer with the ID %1$s or the layer does not exist!','lmm'), $layer); 
  echo $error_layers_not_exists;
  } else {
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/vnd.google-earth.kml+xml; charset=utf-8'); 
  header('Content-Disposition: attachment; filename="' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-layer-' . intval($_GET['layer']) . '.kml"');
  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
  echo '<kml xmlns="http://www.opengis.net/kml/2.2">'.PHP_EOL;
  
  echo '<Document>'.PHP_EOL;
  echo '<description><![CDATA[powered by <a href="http://www.wordpress.org">WordPress</a> &amp; <a href="http://www.mapsmarker.com">MapsMarker.com</a>]]></description>'.PHP_EOL;    
  echo '<open>1</open>'.PHP_EOL;  
  foreach ($markers as $marker_icon) {
    if ($marker_icon['micon'] == null) {
        $micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
		$micon_name = 'default';
    } else {
        $micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker_icon['micon']; 
		$micon_name = substr($marker_icon['micon'],0,-4);		
    }
	echo '<Style id="' . $micon_name . '"><IconStyle><Icon><href>' . $micon_url . '</href></Icon></IconStyle></Style>'.PHP_EOL;
  }
  
  $layername = $wpdb->get_var('SELECT name FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	if ($_GET['layer'] != 'all') {
	  echo '<Folder>'.PHP_EOL;
	  echo '<name>' . $layername . '</name>'.PHP_EOL;
	}
	
  foreach ($markers as $marker) {
    if ($marker['micon'] == null) {
		$micon_name = 'default';
    } else {
		$micon_name = substr($marker['micon'],0,-4);		
    }
	$date_kml =  strtotime($marker['mcreatedon']);
	$time_kml =  strtotime($marker['mcreatedon']);
	$offset_kml = date('H:i',get_option('gmt_offset')*3600);
	if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
	echo '<Placemark id="marker-' . $marker['mid'] . '">'.PHP_EOL;
	//info: google maps has problems displaying custom icons in ff - get parameter default_icons displays standard icons
	if (!isset($_GET['default_icons'])) {
	echo '<styleUrl>#' . $micon_name . '</styleUrl>'.PHP_EOL;
	}
	
	echo '<name>' . stripslashes($marker['mmarkername']) . '</name>'.PHP_EOL;
	echo '<TimeStamp><when>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</when></TimeStamp>'.PHP_EOL;
	echo '<atom:author>' . $marker['mcreatedby'] . '</atom:author>'.PHP_EOL;
	echo '<description><![CDATA[' .  stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
	echo '<Point>'.PHP_EOL;
	echo '<coordinates>' . $marker['mlon'] . ',' . $marker['mlat'] . '</coordinates>'.PHP_EOL;
	echo '</Point>'.PHP_EOL;
	echo '</Placemark>'.PHP_EOL;
  }
  
  	if ($_GET['layer'] != 'all') {
	  echo '</Folder>'.PHP_EOL;
	}
	   
  echo '<ScreenOverlay>'.PHP_EOL;
  echo '<name><![CDATA[powered by WordPress & MapsMarker.com]]></name>'.PHP_EOL;
  echo '<Icon>'.PHP_EOL;
  echo '<href>' . LEAFLET_PLUGIN_URL . 'img/kml-overlay-powered-by.png</href>'.PHP_EOL;
  echo '</Icon>'.PHP_EOL;
  echo '<overlayXY x="0" y="1" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '<screenXY x="0" y="1" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '<rotationXY x="0" y="0" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '<size x="0" y="0" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '</ScreenOverlay>'.PHP_EOL;
  echo '</Document>'.PHP_EOL;
  echo '</kml>';
  } //info: check if layer exists end
  }
elseif (isset($_GET['marker'])) {
  $markerid = mysql_real_escape_string($_GET['marker']);
  $markers = explode(',', $markerid);
  $checkedmarkers = array();
  foreach ($markers as $cmarker) {
    if (intval($cmarker) > 0)
      $checkedmarkers[] = intval($cmarker);
  }
  if (count($checkedmarkers) > 0)
    $q = 'WHERE m.id IN ('.implode(',', $checkedmarkers).')';
  else
    die();
  //info: added left outer join to also show markers without a layer
  $sql = 'SELECT m.layer as mlayer,m.icon as micon,m.popuptext as mpopuptext,m.id as mid,m.markername as mmarkername,m.createdby as mcreatedby, m.createdon as mcreatedon, m.lat as mlat, m.lon as mlon FROM '.$table_name_markers.' AS m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  //info: check if marker result is not null
  if ($markers == NULL) {
  $error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $markerid); 
  echo $error_marker_not_exists;
  } else {
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/vnd.google-earth.kml+xml; charset=utf-8'); 
  header('Content-Disposition: attachment; filename="' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-marker-' . intval($_GET['marker']) . '.kml"');
  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
  echo '<kml xmlns="http://www.opengis.net/kml/2.2">'.PHP_EOL;
  echo '<Document>'.PHP_EOL;
  echo '<description><![CDATA[powered by <a href="http://www.wordpress.org">WordPress</a> &amp; <a href="http://www.mapsmarker.com">MapsMarker.com</a>]]></description>'.PHP_EOL;    
  echo '<open>0</open>'.PHP_EOL;  
  foreach ($markers as $marker_icon) {
    if ($marker_icon['micon'] == null) {
        $micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
		$micon_name = 'default';
    } else {
        $micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker_icon['micon']; 
		$micon_name = substr($marker_icon['micon'],0,-4);		
    }
	echo '<Style id="' . $micon_name . '"><IconStyle><Icon><href>' . $micon_url . '</href></Icon></IconStyle></Style>'.PHP_EOL;
  }
  
  echo '<name>' . get_bloginfo('name') . '</name>'.PHP_EOL;  
  foreach ($markers as $marker) {
	if ($marker['micon'] == null) {
		$micon_name = 'default';
	} else {
		$micon_name = substr($marker['micon'],0,-4);		
	}
	$date_kml =  strtotime($marker['mcreatedon']);
	$time_kml =  strtotime($marker['mcreatedon']);
	$offset_kml = date('H:i',get_option('gmt_offset')*3600);
	if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
	echo '<Placemark id="marker-' . $marker['mid'] . '">'.PHP_EOL;
	//info: google maps has problems displaying custom icons in ff - get parameter default_icons displays standard icons
	if (!isset($_GET['default_icons'])) {
	echo '<styleUrl>#' . $micon_name . '</styleUrl>'.PHP_EOL;
	}
	echo '<name>' . stripslashes($marker['mmarkername']) . '</name>'.PHP_EOL;
	echo '<TimeStamp><when>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</when></TimeStamp>'.PHP_EOL;
	echo '<atom:author>' . $marker['mcreatedby'] . '</atom:author>'.PHP_EOL;
	echo '<description><![CDATA[' .  stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
	echo '<Point>'.PHP_EOL;
	echo '<coordinates>' . $marker['mlon'] . ',' . $marker['mlat'] . '</coordinates>'.PHP_EOL;
	echo '</Point>'.PHP_EOL;
	echo '</Placemark>'.PHP_EOL;
  }
  echo '<ScreenOverlay>'.PHP_EOL;
  echo '<name><![CDATA[powered by WordPress & MapsMarker.com]]></name>'.PHP_EOL;
  echo '<Icon>'.PHP_EOL;
  echo '<href>' . LEAFLET_PLUGIN_URL . 'img/kml-overlay-powered-by.png</href>'.PHP_EOL;
  echo '</Icon>'.PHP_EOL;
  echo '<overlayXY x="0" y="1" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '<screenXY x="0" y="1" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '<rotationXY x="0" y="0" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '<size x="0" y="0" xunits="fraction" yunits="fraction"/>'.PHP_EOL;
  echo '</ScreenOverlay>'.PHP_EOL;
  echo '</Document>'.PHP_EOL;
  echo '</kml>';
  } //info: check if marker exists end
}
} //info: end plugin active check
?>