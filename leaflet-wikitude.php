<?php
/*
    Wikitude generator - Leaflet Maps Marker Plugin
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
$lmm_options = get_option( 'leafletmapsmarker_options' );
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
if (isset($_GET['layer'])) {
  $layer = mysql_real_escape_string($_GET['layer']); 
  
  $maxNumberOfPois = isset($_GET['maxNumberOfPois']) ? intval($_GET['maxNumberOfPois']) : $lmm_options[ 'ar_wikitude_maxnumberpois' ];
  
  if ($layer == '*' or $layer == 'all') {
	//info: no exact results, but better than getting no results on calling Wikitude ARML links which might confuse users
	$first_marker_lat = $wpdb->get_var('SELECT lat FROM '.$table_name_markers.' WHERE id = 1');
	$first_marker_lon = $wpdb->get_var('SELECT lon FROM '.$table_name_markers.' WHERE id = 1');
 	$latUser = isset($_GET['latitude']) ? floatval($_GET['latitude']) : $first_marker_lat;
	$lonUser = isset($_GET['longitude']) ? floatval($_GET['longitude']) : $first_marker_lon;
  } else {
	$layerviewlat = $wpdb->get_var('SELECT layerviewlat FROM '.$table_name_layers.' WHERE id='.$layer);
	$layerviewlon = $wpdb->get_var('SELECT layerviewlon FROM '.$table_name_layers.' WHERE id='.$layer);
 	$latUser = isset($_GET['latitude']) ? floatval($_GET['latitude']) : $layerviewlat;
	$lonUser = isset($_GET['longitude']) ? floatval($_GET['longitude']) : $layerviewlon;
  }
 
  $radius = $lmm_options[ 'ar_wikitude_radius' ];
  $distanceLLA = 0.01 * $radius / 1112;
  $boundingBoxLatitude1 = $latUser - $distanceLLA;
  $boundingBoxLatitude2 = $latUser + $distanceLLA;
  $boundingBoxLongitude1 = $lonUser - $distanceLLA;
  $boundingBoxLongitude2 = $lonUser + $distanceLLA;  
  
  isset($_GET['searchterm']) ? $searchterm = mysql_real_escape_string($_GET['searchterm']) : $searchterm = NULL;
  if ($searchterm != NULL)
  {
		  $q = 'LIMIT 0';
		  if ($layer == '*' or $layer == 'all')
			$q = "WHERE m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . " AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%') LIMIT 1000";
		  else {
			$layers = explode(',', $layer);
			$checkedlayers = array();
			foreach ($layers as $clayer) {
			  if (intval($clayer) > 0)
				$checkedlayers[] = intval($clayer);
			}
			if (count($checkedlayers) > 0)
			  $q = "WHERE layer IN (".implode(",", $checkedlayers).") and m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . " AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%')";
		  }
		  $sql = 'SELECT m.id as mid, m.layer as mlayer, m.markername as mmarkername, m.icon as micon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext FROM '.$table_name_markers.' AS m INNER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
		  $markers = $wpdb->get_results($sql, ARRAY_A);
		  header('Cache-Control: no-cache, must-revalidate');
		  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		  header('Content-type: text/xml; charset=utf-8');
		  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		  echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0">'.PHP_EOL;
		  echo '<Document>'.PHP_EOL;
		  $ar_wikitude_provider_name_sanitized = strtolower(preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $lmm_options[ 'ar_wikitude_provider_name' ]));
		  echo '<ar:provider id="' . $ar_wikitude_provider_name_sanitized . '">'.PHP_EOL;
		  if (($layer == '*' or $layer == 'all')  or (intval($clayer) > 0) )
		  { 
		  $layername = get_bloginfo('name'); 
		  } else {
		  $layername = $wpdb->get_var('SELECT l.name FROM '.$table_name_layers.' as l WHERE l.id='.$layer);
		  }
		  echo '<ar:name><![CDATA[' . $layername . ']]></ar:name>'.PHP_EOL;
		  echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
		  echo '<wikitude:providerUrl><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_url' ] . ']]></wikitude:providerUrl>'.PHP_EOL;
		  //echo '<wikitude:tags><![CDATA[]]></wikitude:tags>'.PHP_EOL;
		  echo '<wikitude:logo><![CDATA[' . $lmm_options[ 'ar_wikitude_logo' ] . ']]></wikitude:logo>'.PHP_EOL;
		  echo '<wikitude:icon><![CDATA[' . $lmm_options[ 'ar_wikitude_icon' ] . ']]></wikitude:icon>'.PHP_EOL;
		  echo '</ar:provider>'.PHP_EOL;
		
		  foreach ($markers as $marker) {
				//info: get icon urls for each marker	
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
				}
			
			  echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
			  echo '<ar:provider><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_name' ] . ']]></ar:provider>'.PHP_EOL;
			  echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
			  echo '<description><![CDATA[' . stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
			  echo '<wikitude:info>'.PHP_EOL;
			  echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
			  echo '<wikitude:phone><![CDATA[' . $lmm_options[ 'ar_wikitude_phone' ] . ']]></wikitude:phone>'.PHP_EOL;
			  //echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
			  echo '<wikitude:email><![CDATA[' . $lmm_options[ 'ar_wikitude_email' ] . ']]></wikitude:email>'.PHP_EOL;
			  //echo '<wikitude:address><![CDATA[]]></wikitude:address>'.PHP_EOL;
			  echo '<wikitude:attachment><![CDATA[' . $lmm_options[ 'ar_wikitude_attachment' ] . ']]></wikitude:attachment>'.PHP_EOL;
			  echo '</wikitude:info>'.PHP_EOL;
			  echo '<Point>'.PHP_EOL;
			  echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
			  echo '</Point>'.PHP_EOL;
			  echo '</Placemark>'.PHP_EOL;
		  }
		  echo '</Document>';
		  echo '</kml>';
  //info: if no searchterm
  }  else  {
		  $q = 'LIMIT 0';
		  if ($layer == '*' or $layer == 'all')
			$q = "WHERE m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . " LIMIT 1000";
		  else {
			$layers = explode(',', $layer);
			$checkedlayers = array();
			foreach ($layers as $clayer) {
			  if (intval($clayer) > 0)
				$checkedlayers[] = intval($clayer);
			}
			if (count($checkedlayers) > 0)
			  $q = "WHERE layer IN (".implode(",", $checkedlayers).") and m.lat BETWEEN " . $boundingBoxLatitude1 . " AND " . $boundingBoxLatitude2 . " AND m.lon BETWEEN " . $boundingBoxLongitude1 . " AND " . $boundingBoxLongitude2 . "";
		  }
		  $sql = 'SELECT m.id as mid, m.layer as mlayer, m.markername as mmarkername, m.icon as micon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext FROM '.$table_name_markers.' AS m INNER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
		  $markers = $wpdb->get_results($sql, ARRAY_A);
		  header('Cache-Control: no-cache, must-revalidate');
		  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		  header('Content-type: text/xml; charset=utf-8');
		  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		  echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0">'.PHP_EOL;
		  echo '<Document>'.PHP_EOL;
		  $ar_wikitude_provider_name_sanitized = strtolower(preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $lmm_options[ 'ar_wikitude_provider_name' ]));
		  echo '<ar:provider id="' . $ar_wikitude_provider_name_sanitized . '">'.PHP_EOL;
		  if (($layer == '*' or $layer == 'all')  or (intval($clayer) > 0) )
		  { 
		  $layername = get_bloginfo('name'); 
		  } else {
		  $layername = $wpdb->get_var('SELECT l.name FROM '.$table_name_layers.' as l WHERE l.id='.$layer);
		  }
		  echo '<ar:name><![CDATA[' . $layername . ']]></ar:name>'.PHP_EOL;
		  echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
		  echo '<wikitude:providerUrl><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_url' ] . ']]></wikitude:providerUrl>'.PHP_EOL;
		  //echo '<wikitude:tags><![CDATA[]]></wikitude:tags>'.PHP_EOL;
		  echo '<wikitude:logo><![CDATA[' . $lmm_options[ 'ar_wikitude_logo' ] . ']]></wikitude:logo>'.PHP_EOL;
		  echo '<wikitude:icon><![CDATA[' . $lmm_options[ 'ar_wikitude_icon' ] . ']]></wikitude:icon>'.PHP_EOL;
		  echo '</ar:provider>'.PHP_EOL;
		
		  foreach ($markers as $marker) {
				//info: get icon urls for each marker	
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
				}
			
			  echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
			  echo '<ar:provider><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_name' ] . ']]></ar:provider>'.PHP_EOL;
			  echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
			  echo '<description><![CDATA[' . stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
			  echo '<wikitude:info>'.PHP_EOL;
			  echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
			  echo '<wikitude:phone><![CDATA[' . $lmm_options[ 'ar_wikitude_phone' ] . ']]></wikitude:phone>'.PHP_EOL;
			  //echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
			  echo '<wikitude:email><![CDATA[' . $lmm_options[ 'ar_wikitude_email' ] . ']]></wikitude:email>'.PHP_EOL;
			  //echo '<wikitude:address><![CDATA[]]></wikitude:address>'.PHP_EOL;
			  echo '<wikitude:attachment><![CDATA[' . $lmm_options[ 'ar_wikitude_attachment' ] . ']]></wikitude:attachment>'.PHP_EOL;
			  echo '</wikitude:info>'.PHP_EOL;
			  echo '<Point>'.PHP_EOL;
			  echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
			  echo '</Point>'.PHP_EOL;
			  echo '</Placemark>'.PHP_EOL;
		  }
		  echo '</Document>';
		  echo '</kml>';
  }
}
elseif (isset($_GET['marker'])) {
  $markerid = mysql_real_escape_string($_GET['marker']);
  $markers = explode(',', $markerid);
  $maxNumberOfPois = isset($_GET['maxNumberOfPois']) ? intval($_GET['maxNumberOfPois']) : $lmm_options[ 'ar_wikitude_maxnumberpois' ];
  $markerlat = $wpdb->get_var('SELECT lat FROM '.$table_name_markers.' WHERE id='.$markerid);
  $markerlon = $wpdb->get_var('SELECT lon FROM '.$table_name_markers.' WHERE id='.$markerid);
 
  $latUser = isset($_GET['latitude']) ? floatval($_GET['latitude']) : $markerlat;
  $lonUser = isset($_GET['longitude']) ? floatval($_GET['longitude']) : $markerlon;
 
  $radius = $lmm_options[ 'ar_wikitude_radius' ];
  $distanceLLA = 0.01 * $radius / 1112;
  $boundingBoxLatitude1 = $latUser - $distanceLLA;
  $boundingBoxLatitude2 = $latUser + $distanceLLA;
  $boundingBoxLongitude1 = $lonUser - $distanceLLA;
  $boundingBoxLongitude2 = $lonUser + $distanceLLA;  
  isset($_GET['searchterm']) ? $searchterm = mysql_real_escape_string($_GET['searchterm']) : $searchterm = NULL;
  if ($searchterm != NULL)
  {
		  $checkedmarkers = array();
		  foreach ($markers as $cmarker) {
			if (intval($cmarker) > 0)
			  $checkedmarkers[] = intval($cmarker);
		  }
		  if (count($checkedmarkers) > 0)
			$q = "WHERE m.id IN (" . implode(",", $checkedmarkers) . ") AND (m.markername LIKE '%" . $searchterm . "%' OR m.popuptext LIKE '%" . $searchterm . "%')";
		  else
			die();
		  //info: added left outer join to also show markers without a layer
		  $sql = 'SELECT m.icon as micon, m.popuptext as mpopuptext, m.id as mid, m.markername as mmarkername, m.lat as mlat, m.lon as mlon FROM '.$table_name_markers.' AS m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
		  $markers = $wpdb->get_results($sql, ARRAY_A);
		  header('Cache-Control: no-cache, must-revalidate');
		  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		  header('Content-type: text/xml; charset=utf-8');
		  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		  echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0" xmlns:wikitudeInternal="http://www.openarml.org/wikitudeInternal/1.0">'.PHP_EOL;
		  echo '<Document>'.PHP_EOL;
		  echo '<ar:provider id="' . $lmm_options[ 'ar_wikitude_provider_name' ] . '">'.PHP_EOL;
		  foreach ($markers as $marker) {
		  echo '<ar:name><![CDATA[' . $marker[ 'mmarkername' ] . ']]></ar:name>'.PHP_EOL;
		  }
		  echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
		  echo '<wikitude:providerUrl><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_url' ] . ']]></wikitude:providerUrl>'.PHP_EOL;
		  //echo '<wikitude:tags><![CDATA[]]></wikitude:tags>'.PHP_EOL;
		  echo '<wikitude:logo><![CDATA[' . $lmm_options[ 'ar_wikitude_logo' ] . ']]></wikitude:logo>'.PHP_EOL;
		  echo '<wikitude:icon><![CDATA[' . $lmm_options[ 'ar_wikitude_icon' ] . ']]></wikitude:icon>'.PHP_EOL;
		  echo '</ar:provider>'.PHP_EOL;
		
		  foreach ($markers as $marker) {
				//info: get icon urls for each marker	
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
				}
			
			  echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
			  echo '<ar:provider><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_name' ] . ']]></ar:provider>'.PHP_EOL;
			  echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
			  echo '<description><![CDATA[' . stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
			  echo '<wikitude:info>'.PHP_EOL;
		
			  foreach ($markers as $marker) {
					//info: get icon urls for each marker	
					if ($marker['micon'] == null) {
						$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
					} else {
						$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
					}
			  echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
			  }
		
			  echo '<wikitude:phone><![CDATA[' . $lmm_options[ 'ar_wikitude_phone' ] . ']]></wikitude:phone>'.PHP_EOL;
			  //echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
			  echo '<wikitude:email><![CDATA[' . $lmm_options[ 'ar_wikitude_email' ] . ']]></wikitude:email>'.PHP_EOL;
			  //echo '<wikitude:address><![CDATA[]]></wikitude:address>'.PHP_EOL;
			  echo '<wikitude:attachment><![CDATA[' . $lmm_options[ 'ar_wikitude_attachment' ] . ']]></wikitude:attachment>'.PHP_EOL;
			  echo '</wikitude:info>'.PHP_EOL;
			  echo '<Point>'.PHP_EOL;
			  echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
			  echo '</Point>'.PHP_EOL;
			  echo '</Placemark>'.PHP_EOL;
		  }
		  echo '</Document>';
		  echo '</kml>';
		  
  //info: if no searchterm
  }  else  {		  
		  $checkedmarkers = array();
		  foreach ($markers as $cmarker) {
			if (intval($cmarker) > 0)
			  $checkedmarkers[] = intval($cmarker);
		  }
		  if (count($checkedmarkers) > 0)
			$q = "WHERE m.id IN (" . implode(",", $checkedmarkers) . ")";
		  else
			die();
		  //info: added left outer join to also show markers without a layer
		  $sql = 'SELECT m.icon as micon, m.popuptext as mpopuptext, m.id as mid, m.markername as mmarkername, m.lat as mlat, m.lon as mlon FROM '.$table_name_markers.' AS m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
		  $markers = $wpdb->get_results($sql, ARRAY_A);
		  header('Cache-Control: no-cache, must-revalidate');
		  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		  header('Content-type: text/xml; charset=utf-8');
		  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
		  echo '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:ar="http://www.openarml.org/arml/1.0" xmlns:wikitude="http://www.openarml.org/wikitude/1.0" xmlns:wikitudeInternal="http://www.openarml.org/wikitudeInternal/1.0">'.PHP_EOL;
		  echo '<Document>'.PHP_EOL;
		  echo '<ar:provider id="' . $lmm_options[ 'ar_wikitude_provider_name' ] . '">'.PHP_EOL;
		  foreach ($markers as $marker) {
		  echo '<ar:name><![CDATA[' . $marker[ 'mmarkername' ] . ']]></ar:name>'.PHP_EOL;
		  }
		  echo '<ar:description>' . __('Wikitude API powered by www.mapsmarker.com','lmm') . '</ar:description>'.PHP_EOL;
		  echo '<wikitude:providerUrl><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_url' ] . ']]></wikitude:providerUrl>'.PHP_EOL;
		  //echo '<wikitude:tags><![CDATA[]]></wikitude:tags>'.PHP_EOL;
		  echo '<wikitude:logo><![CDATA[' . $lmm_options[ 'ar_wikitude_logo' ] . ']]></wikitude:logo>'.PHP_EOL;
		  echo '<wikitude:icon><![CDATA[' . $lmm_options[ 'ar_wikitude_icon' ] . ']]></wikitude:icon>'.PHP_EOL;
		  echo '</ar:provider>'.PHP_EOL;
		
		  foreach ($markers as $marker) {
				//info: get icon urls for each marker	
				if ($marker['micon'] == null) {
					$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
				} else {
					$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
				}
			
			  echo '<Placemark id=\'' . $marker['mid'] . '\'>'.PHP_EOL;
			  echo '<ar:provider><![CDATA[' . $lmm_options[ 'ar_wikitude_provider_name' ] . ']]></ar:provider>'.PHP_EOL;
			  echo '<name><![CDATA[' . stripslashes($marker['mmarkername']) . ']]></name>'.PHP_EOL;
			  echo '<description><![CDATA[' . stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
			  echo '<wikitude:info>'.PHP_EOL;
		
			  foreach ($markers as $marker) {
					//info: get icon urls for each marker	
					if ($marker['micon'] == null) {
						$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
					} else {
						$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
					}
			  echo '<wikitude:thumbnail><![CDATA[' . $micon_url . ']]></wikitude:thumbnail>'.PHP_EOL;
			  }
		
			  echo '<wikitude:phone><![CDATA[' . $lmm_options[ 'ar_wikitude_phone' ] . ']]></wikitude:phone>'.PHP_EOL;
			  //echo '<wikitude:url><![CDATA[]]></wikitude:url>'.PHP_EOL;
			  echo '<wikitude:email><![CDATA[' . $lmm_options[ 'ar_wikitude_email' ] . ']]></wikitude:email>'.PHP_EOL;
			  //echo '<wikitude:address><![CDATA[]]></wikitude:address>'.PHP_EOL;
			  echo '<wikitude:attachment><![CDATA[' . $lmm_options[ 'ar_wikitude_attachment' ] . ']]></wikitude:attachment>'.PHP_EOL;
			  echo '</wikitude:info>'.PHP_EOL;
			  echo '<Point>'.PHP_EOL;
			  echo '<coordinates><![CDATA[' . $marker['mlon'] . ',' . $marker['mlat'] . ']]></coordinates>'.PHP_EOL;
			  echo '</Point>'.PHP_EOL;
			  echo '</Placemark>'.PHP_EOL;
		  }
		  echo '</Document>';
		  echo '</kml>';
  }
}
} //info: end plugin active check
?>