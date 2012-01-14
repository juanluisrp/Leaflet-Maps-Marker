<?php
/*
    GeoRSS generator - Leaflet Maps Marker Plugin
*/
//info: construct path to wp-load.php and get $wp_path
while(!is_file('wp-load.php')){
  if(is_dir('../')) chdir('../');
  else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
$wp_path_file = split('wp-content', __FILE__);
$wp_path = $wp_path_file[0];
//info: is plugin active?
include_once( $wp_path.'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php' );
$format = (isset($_GET['format']) == TRUE ) ? $_GET['format'] : '';
function hide_email($email) { $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'; $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999); for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])]; $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";'; $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));'; $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"'; $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script; }
if (!is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php')) {
echo 'The WordPress plugin <a href="http://www.mapsmarker.com" target="_blank">Leaflet Maps Marker</a> is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (' . hide_email(get_bloginfo('admin_email')) . ') who can activate this plugin again.';
} else {
global $wpdb;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$lmm_options = get_option( 'leafletmapsmarker_options' );
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
  $sql = 'SELECT m.id as mid, m.markername as mmarkername, m.layer as mlayer, CONCAT(m.lon,\',\',m.lat) AS mcoords, m.icon as micon, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon, m.lat as mlat, m.lon as mlon, m.popuptext as mpopuptext, l.id as lid, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon, l.name AS lname FROM '.$table_name_markers.' AS m INNER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  //info: output as atom - part 1
  if ($format == 'atom') { 
	  $offset_kml = date('H:i',get_option('gmt_offset')*3600);
	  if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
	  /*info: not used yet, as don´t know which are right srsnames
	  if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; } 
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; } 
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) { $srsname = 'EPSG3395'; }
	  */
	  $layername = $wpdb->get_var('SELECT name FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	  $layercreatedby = $wpdb->get_var('SELECT createdby FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	  $layercreatedon = $wpdb->get_var('SELECT createdon FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	  $date_kml =  strtotime($layercreatedon);
	  $time_kml =  strtotime($layercreatedon);
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/atom+xml; charset=utf-8');
	  echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
	  echo '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml">'.PHP_EOL;
		if ($layer == '*' or $layer == 'all') {
			echo '<title>' . get_bloginfo('name') . ' - ' . __('maps','lmm') . '</title>'.PHP_EOL;
			echo '<link href="' . get_bloginfo('url') . '"/>'.PHP_EOL;
			echo '<id>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-all-layers</id>'.PHP_EOL;
			} else {
			echo '<title>' . get_bloginfo('name') . ' - ' . $layername . '</title>'.PHP_EOL;
			echo '<author>'.PHP_EOL;
			echo '<name>' . stripslashes($layercreatedby) . '</name>'.PHP_EOL;
			echo '</author>'.PHP_EOL;
			echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
			echo '<link href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . intval($_GET['layer']) . '"/>'.PHP_EOL;
			echo '<id>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-layer-' . intval($_GET['layer']) . '</id>'.PHP_EOL;
			}
	  echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
	  echo '<subtitle>GeoRSS-feed created with MapsMarker.com WordPress Plugin</subtitle>'.PHP_EOL;
	  
	  foreach ($markers as $marker) {
		//info: get icon urls for each marker	
		if ($marker['micon'] == null) {
			$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
		} else {
			$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
		}
		echo '<entry>'.PHP_EOL;
		echo '<title>' . stripslashes($marker['mmarkername']) . '</title>'.PHP_EOL;
		echo '<link href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $marker['mid'] . '"/>'.PHP_EOL;
		echo '<id>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-layer-' . intval($_GET['layer']) . '-marker-' . $marker['mid'] . '</id>'.PHP_EOL;
		echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
		echo '<contributor>' . stripslashes($marker['mcreatedby']) . '</contributor>'.PHP_EOL;
		echo '<content><![CDATA[' . stripslashes($marker['mpopuptext']) . ']]></content>'.PHP_EOL;
		echo '<logo>' . $micon_url . '</logo>'.PHP_EOL;
		echo '<icon>' . $micon_url . '</icon>'.PHP_EOL;
		echo '<source>' . get_bloginfo('url') . '</source>'.PHP_EOL;
		echo '<georss:where>'.PHP_EOL;
		//info: add if srsnames are verified - <gml:Point srsName="' . $srsname . '">
		echo '<gml:Point>'.PHP_EOL;
		echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
		echo '</gml:Point>'.PHP_EOL;
		echo '</georss:where>'.PHP_EOL;
		echo '</entry>'.PHP_EOL;
	  }
	  echo '</feed>';
  } //info: end output as atom
  //info: output as RSS 2.0
  if ($format != 'atom') { 
	  $offset_kml = date('H:i',get_option('gmt_offset')*3600);
	  if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
	  /*info: not used yet, as don´t know which are right srsnames
	  if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; } 
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; } 
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) { $srsname = 'EPSG3395'; }
	  */
	  $layername = $wpdb->get_var('SELECT name FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	  $layercreatedby = $wpdb->get_var('SELECT createdby FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	  $layercreatedon = $wpdb->get_var('SELECT createdon FROM '.$table_name_layers.' WHERE id = '.intval($_GET['layer']).'');
	  $date_kml_layer =  strtotime($layercreatedon);
	  $time_kml_layer =  strtotime($layercreatedon);
	  $newest_marker_createdon = strtotime($wpdb->get_var('SELECT max(createdon) FROM '.$table_name_markers.''));
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/rss+xml; charset=utf-8');
	  echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
	  echo '<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml">'.PHP_EOL;
	  echo '<channel>'.PHP_EOL;
	  echo '<link>' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . intval($_GET['layer']) . '</link>'.PHP_EOL;
	  if ($layer == '*' or $layer == 'all') {
		echo '<title>' . get_bloginfo('name') . ' - ' . __('maps','lmm') . '</title>'.PHP_EOL;
		echo '<guid>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-all-layers</guid>'.PHP_EOL;
		echo '<lastBuildDate>' . date("Y-m-d", $newest_marker_createdon) . 'T' . date("h:m:s", $newest_marker_createdon) . $plus_minus . $offset_kml . '</lastBuildDate>'.PHP_EOL;
		} else {
		echo '<title>' . get_bloginfo('name') . ' - ' . $layername . '</title>'.PHP_EOL;
	    echo '<managingEditor>' . stripslashes($layercreatedby) . '</managingEditor>'.PHP_EOL;
		echo '<guid>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-layer-' . intval($_GET['layer']) . '</guid>'.PHP_EOL;
		echo '<lastBuildDate>' . date("Y-m-d", $date_kml_layer) . 'T' . date("h:m:s", $time_kml_layer) . $plus_minus . $offset_kml . '</lastBuildDate>'.PHP_EOL;
		}
	  echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
	  echo '<description>GeoRSS-feed created with MapsMarker.com WordPress Plugin</description>'.PHP_EOL;
	  foreach ($markers as $marker) {
		$date_kml_marker =  strtotime($marker['mcreatedon']);
		$time_kml_marker =  strtotime($marker['mcreatedon']);
	    //info: get icon urls for each marker	
		if ($marker['micon'] == null) {
			$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
		} else {
			$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
		}
		echo '<item>'.PHP_EOL;
		echo '<title>' . stripslashes($marker['mmarkername']) . '</title>'.PHP_EOL;
		echo '<link>' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $marker['mid'] . '</link>'.PHP_EOL;
		echo '<guid>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-layer-' . $marker['lid'] . '-marker-' . $marker['mid'] . '</guid>'.PHP_EOL;
		echo '<pubdate>' . date("Y-m-d", $date_kml_marker) . 'T' . date("h:m:s", $time_kml_marker) . $plus_minus . $offset_kml . '</pubdate>'.PHP_EOL;
		echo '<author>' . $marker['mcreatedby'] . '</author>'.PHP_EOL;
		echo '<description><![CDATA[' . stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
		echo '<image>' . $micon_url . '</image>'.PHP_EOL;
		echo '<source>' . get_bloginfo('url') . '</source>'.PHP_EOL;
		echo '<georss:where>'.PHP_EOL;
		echo '<gml:Point>'.PHP_EOL;
		echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
		echo '</gml:Point>'.PHP_EOL;
		echo '</georss:where>'.PHP_EOL;
		echo '</item>'.PHP_EOL;
	  }
	  echo '</channel>'.PHP_EOL;
	  echo '</rss>';
  } //info: end output as RSS 2.0
} //info: end isset($_GET['layer'])
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
  //info: output as atom - part 1
  if ($format == 'atom') { 
	  $offset_kml = date('H:i',get_option('gmt_offset')*3600);
	  if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
	  /*info: not used yet, as don´t know which are right srsnames
	  if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; } 
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; }
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) {	$srsname = 'EPSG3395';
	  }*/
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/atom+xml; charset=utf-8');
	  echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
	  echo '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml">'.PHP_EOL;
	  foreach ($markers as $marker) {
	  $date_kml =  strtotime($marker['mcreatedon']);
	  $time_kml =  strtotime($marker['mcreatedon']);
	  echo '<title>' . get_bloginfo('name') . ' - ' . stripslashes($marker['mmarkername']) . '</title>'.PHP_EOL;
	  echo '<author>'.PHP_EOL;
	  echo '<name>' . stripslashes($marker['mcreatedby']) . '</name>'.PHP_EOL;
	  echo '</author>'.PHP_EOL;
	  echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
	  }
	  echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
	  echo '<subtitle>GeoRSS-feed created with MapsMarker.com WordPress Plugin</subtitle>'.PHP_EOL;
	  echo '<link href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . intval($_GET['marker']) . '"/>'.PHP_EOL;
	  echo '<id>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-marker-' . intval($_GET['marker']) . '</id>'.PHP_EOL;
	  
	  foreach ($markers as $marker) {
		//info: get icon urls for each marker	
		if ($marker['micon'] == null) {
			$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
		} else {
			$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
		}
		echo '<entry>'.PHP_EOL;
		echo '<title>' . stripslashes($marker['mmarkername']) . '</title>'.PHP_EOL;
		echo '<link href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . intval($_GET['marker']) . '"/>'.PHP_EOL;
		echo '<id>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-marker-' . intval($_GET['marker']) . '-detail</id>'.PHP_EOL;
		echo '<updated>' . date("Y-m-d", $date_kml) . 'T' . date("h:m:s", $time_kml) . $plus_minus . $offset_kml . '</updated>'.PHP_EOL;
		echo '<content><![CDATA[' . stripslashes($marker['mpopuptext']) . ']]></content>'.PHP_EOL;
		echo '<logo>' . $micon_url . '</logo>'.PHP_EOL;
		echo '<icon>' . $micon_url . '</icon>'.PHP_EOL;
		echo '<source>' . get_bloginfo('url') . '</source>'.PHP_EOL;
		echo '<georss:where>'.PHP_EOL;
		//info: add if srsnames are verified - <gml:Point srsName="' . $srsname . '">
		echo '<gml:Point>'.PHP_EOL;
		echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
		echo '</gml:Point>'.PHP_EOL;
		echo '</georss:where>'.PHP_EOL;
		echo '</entry>'.PHP_EOL;
	  }
	  echo '</feed>';
  } //info: end output as atom
  //info: output as RSS 2.0
  if ($format != 'atom') { 
	  $offset_kml = date('H:i',get_option('gmt_offset')*3600);
	  if ($offset_kml >= 0) { $plus_minus = '+'; } else { $plus_minus = '-'; };
	  /*info: not used yet, as don´t know which are right srsnames
	  if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3857' ) { $srsname = 'EPSG3857'; } 
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG4326' ) { $srsname = 'EPSG4326'; }
		else if ($lmm_options[ 'misc_projections' ] == 'L.CRS.EPSG3395' ) {	$srsname = 'EPSG3395';
	  }*/
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/rss+xml; charset=utf-8');
	  echo '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
	  echo '<rss version="2.0" xmlns:georss="http://www.georss.org/georss" xmlns:gml="http://www.opengis.net/gml">'.PHP_EOL;
	  echo '<channel>'.PHP_EOL;
	  echo '<link>' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . intval($_GET['marker']) . '</link>'.PHP_EOL;
	  echo '<title>' . get_bloginfo('name') . ' - ';
	  foreach ($markers as $marker) {
		echo stripslashes($marker['mmarkername']) . ' ';
	  }
	  echo '</title>'.PHP_EOL;
	  echo '<generator>www.mapsmarker.com</generator>'.PHP_EOL;
	  echo '<description>GeoRSS-feed created with MapsMarker.com WordPress Plugin</description>'.PHP_EOL;
	  echo '<guid>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-marker-' . intval($_GET['marker']) . '-channel</guid>'.PHP_EOL;
	  
	  foreach ($markers as $marker) {
		$date_kml_marker =  strtotime($marker['mcreatedon']);
		$time_kml_marker =  strtotime($marker['mcreatedon']);
	    //info: get icon urls for each marker	
		if ($marker['micon'] == null) {
			$micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
		} else {
			$micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
		}
		echo '<item>'.PHP_EOL;
		echo '<title>' . stripslashes($marker['mmarkername']) . '</title>'.PHP_EOL;
		echo '<link>' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $marker['mid'] . '</link>'.PHP_EOL;
		echo '<guid>' .   preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), get_bloginfo('name')) . '-marker-' . $marker['mid'] . '</guid>'.PHP_EOL;
		echo '<pubdate>' . date("Y-m-d", $date_kml_marker) . 'T' . date("h:m:s", $time_kml_marker) . $plus_minus . $offset_kml . '</pubdate>'.PHP_EOL;
		echo '<author>' . stripslashes($marker['mcreatedby']) . '</author>'.PHP_EOL;
		echo '<description><![CDATA[' . stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext'])) . ']]></description>'.PHP_EOL;
		echo '<image>' . $micon_url . '</image>'.PHP_EOL;
		echo '<source>' . get_bloginfo('url') . '</source>'.PHP_EOL;
		echo '<georss:where>'.PHP_EOL;
		echo '<gml:Point>'.PHP_EOL;
		echo '<gml:pos>'.$marker['mlat'].' '.$marker['mlon'].'</gml:pos>'.PHP_EOL;
		echo '</gml:Point>'.PHP_EOL;
		echo '</georss:where>'.PHP_EOL;
		echo '</item>'.PHP_EOL;
	  }
	  echo '</channel>'.PHP_EOL;
	  echo '</rss>';
  } //info: end output as RSS 2.0
 } //info: end isset($_GET['marker'])
} //info: end plugin active check
?>