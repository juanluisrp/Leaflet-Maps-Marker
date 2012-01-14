<?php
/*
    GeoJSON generator - Leaflet Maps Marker Plugin
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
//info: get callback parameters for JSONP 
$callback = (isset($_GET['callback']) == TRUE ) ? $_GET['callback'] : '';
function hide_email($email) { $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'; $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999); for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])]; $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";'; $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));'; $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"'; $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script; }
if (!is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php')) {
echo 'The WordPress plugin <a href="http://www.mapsmarker.com" target="_blank">Leaflet Maps Marker</a> is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (' . hide_email(get_bloginfo('admin_email')) . ') who can activate this plugin again.';
} else {
global $wpdb;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
if (isset($_GET['layer'])) {
  $layer = mysql_real_escape_string($_GET['layer']); //info: not intval() cause otherwise $layer=0 when creating new layer and showing all markers with layer id = 0
  
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
  $sql = 'SELECT m.id as mid, m.markername as mmarkername, m.layer as mlayer, CONCAT(m.lon,\',\',m.lat) AS mcoords, m.icon as micon, m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon,m.zoom as mzoom, m.basemap as mbasemap, m.lat as mlat, m.lon as mlon, m.openpopup as mopenpopup, m.popuptext as mpopuptext, m.mapwidth as mmapwidth, m.mapwidthunit as mmapwidthunit, m.mapheight as mmapheight, m.controlbox as mcontrolbox, m.overlays_custom as moverlays_custom, m.overlays_custom2 as moverlays_custom2, m.overlays_custom3 as moverlays_custom3, m.overlays_custom4 as moverlays_custom4, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon, l.name AS lname FROM '.$table_name_markers.' AS m INNER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  $first = true;
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json; charset=utf-8');
  //info: callback for JSONP - part 1
  if ($callback != NULL) { 
	echo $callback . '('; 
  }
  echo '{"type":"FeatureCollection",'.PHP_EOL;
  echo '"features":['.PHP_EOL;
  foreach ($markers as $marker) {
    //info: get icon urls for each marker	
    if ($marker['micon'] == null) {
        $micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
    } else {
        $micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
    }
    if ($first) $first = false;
    else echo ','.PHP_EOL;
    echo '{'.PHP_EOL;
	echo '"type":"Feature",'.PHP_EOL;
	echo '"geometry":'.PHP_EOL;
	echo '{'.PHP_EOL;
	echo '"type":"Point",'.PHP_EOL;
	echo '"coordinates":[' . $marker['mcoords'] . ']'.PHP_EOL;
	echo '},'.PHP_EOL;
	echo '"properties":'.PHP_EOL;
	echo '{'.PHP_EOL;
	echo '"markerid":"'.$marker['mid'].'",'.PHP_EOL;
	echo '"markername":"' . stripslashes($marker['mmarkername']) . '",'.PHP_EOL;
	echo '"basemap":"'.$marker['mbasemap'].'",'.PHP_EOL;
	echo '"lat":"'.$marker['mlat'].'",'.PHP_EOL;
	echo '"lon":"'.$marker['mlon'].'",'.PHP_EOL;
	echo '"icon":"'.$micon_url.'",'.PHP_EOL;
	echo '"text":"' . stripslashes(str_replace('"', '\"', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext']))) . '",'.PHP_EOL;
	echo '"zoom":"' . $marker['mzoom'] . '",'.PHP_EOL;
	echo '"openpopup":"' . $marker['mopenpopup'] . '",'.PHP_EOL;
	echo '"mapwidth":"' . $marker['mmapwidth'] . '",'.PHP_EOL;
	echo '"mapwidthunit":"' . $marker['mmapwidthunit'] . '",'.PHP_EOL;
	echo '"mapheight":"' . $marker['mmapheight'] . '",'.PHP_EOL;
	echo '"marker-createdby":"' . stripslashes($marker['mcreatedby']) . '",'.PHP_EOL;
	echo '"marker-createdon":"' . $marker['mcreatedon'] . '",'.PHP_EOL;
	echo '"marker-updatedby":"' . stripslashes($marker['mupdatedby']) . '",'.PHP_EOL;
	echo '"marker-updatedon":"' . stripslashes($marker['mupdatedon']) . '",'.PHP_EOL;
	echo '"layerid":"'.$marker['mlayer'].'",'.PHP_EOL;
	echo '"layername":"' . stripslashes($marker['lname']) . '",'.PHP_EOL;
	echo '"layer-createdby":"' . $marker['lcreatedby'] . '",'.PHP_EOL;
	echo '"layer-createdon":"' . $marker['lcreatedon'] . '",'.PHP_EOL;
	echo '"layer-updatedby":"' . stripslashes($marker['lupdatedby']) . '",'.PHP_EOL;
	echo '"layer-updatedon":"' . stripslashes($marker['lupdatedon']) . '",'.PHP_EOL;
	echo '"controlbox":"'.$marker['mcontrolbox'].'",'.PHP_EOL;
	echo '"overlays_custom":"'.$marker['moverlays_custom'].'",'.PHP_EOL;
	echo '"overlays_custom2":"'.$marker['moverlays_custom2'].'",'.PHP_EOL;
	echo '"overlays_custom3":"'.$marker['moverlays_custom3'].'",'.PHP_EOL;
	echo '"overlays_custom4":"'.$marker['moverlays_custom4'].'"'.PHP_EOL;
	echo '}}';
  }
  echo ']}';
  //info: callback for JSONP - part 2
  if ($callback != NULL) { echo ');'; }
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
  $sql = 'SELECT CONCAT(m.lon,\',\',m.lat) AS mcoords,m.layer as mlayer,l.name AS lname,m.icon as micon,m.popuptext as mpopuptext,m.id as mid,m.markername as mmarkername,m.createdby as mcreatedby, m.createdon as mcreatedon, m.updatedby as mupdatedby, m.updatedon as mupdatedon,m.zoom as mzoom, m.basemap as mbasemap, m.lat as mlat, m.lon as mlon, m.openpopup as mopenpopup, m.mapwidth as mmapwidth, m.mapwidthunit as mmapwidthunit, m.mapheight as mmapheight, m.controlbox as mcontrolbox, m.overlays_custom as moverlays_custom, m.overlays_custom2 as moverlays_custom2, m.overlays_custom3 as moverlays_custom3, m.overlays_custom4 as moverlays_custom4, l.createdby as lcreatedby, l.createdon as lcreatedon, l.updatedby as lupdatedby, l.updatedon as lupdatedon FROM '.$table_name_markers.' AS m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id '.$q;
  $markers = $wpdb->get_results($sql, ARRAY_A);
  $first = true;
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json; charset=utf-8');
  //info: callback for JSONP - part 1
  if ($callback != NULL) { 
	echo $callback . '('; 
  }
  echo '{"type":"FeatureCollection",'.PHP_EOL;
  echo '"features":['.PHP_EOL;
  foreach ($markers as $marker) {
    //info: get icon urls for each marker	
    if ($marker['micon'] == null) {
        $micon_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png';  
    } else {
        $micon_url = LEAFLET_PLUGIN_ICONS_URL . '/' . $marker['micon']; 
    }
    if ($first) $first = false;
    else echo ','.PHP_EOL;
    echo '{'.PHP_EOL;
	echo '"type":"Feature",'.PHP_EOL;
	echo '"geometry":'.PHP_EOL;
	echo '{'.PHP_EOL;
	echo '"type":"Point",'.PHP_EOL;
	echo '"coordinates":[' . $marker['mcoords'] . ']'.PHP_EOL;
	echo '},'.PHP_EOL;
	echo '"properties":'.PHP_EOL;
	echo '{'.PHP_EOL;
	echo '"markerid":"'.$marker['mid'].'",'.PHP_EOL;
	echo '"markername":"' . stripslashes($marker['mmarkername']) . '",'.PHP_EOL;
	echo '"basemap":"'.$marker['mbasemap'].'",'.PHP_EOL;
	echo '"lat":"'.$marker['mlat'].'",'.PHP_EOL;
	echo '"lon":"'.$marker['mlon'].'",'.PHP_EOL;
	echo '"icon":"'.$micon_url.'",'.PHP_EOL;
	echo '"text":"' . stripslashes(str_replace('"', '\"', preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$marker['mpopuptext']))) . '",'.PHP_EOL;
	echo '"zoom":"' . $marker['mzoom'] . '",'.PHP_EOL;
	echo '"openpopup":"' . $marker['mopenpopup'] . '",'.PHP_EOL;
	echo '"mapwidth":"' . $marker['mmapwidth'] . '",'.PHP_EOL;
	echo '"mapwidthunit":"' . $marker['mmapwidthunit'] . '",'.PHP_EOL;
	echo '"mapheight":"' . $marker['mmapheight'] . '",'.PHP_EOL;
	echo '"marker-createdby":"' . stripslashes($marker['mcreatedby']) . '",'.PHP_EOL;
	echo '"marker-createdon":"' . $marker['mcreatedon'] . '",'.PHP_EOL;
	echo '"marker-updatedby":"' . stripslashes($marker['mupdatedby']) . '",'.PHP_EOL;
	echo '"marker-updatedon":"' . stripslashes($marker['mupdatedon']) . '",'.PHP_EOL;
	echo '"layerid":"'.$marker['mlayer'].'",'.PHP_EOL;
	echo '"layername":"' . stripslashes($marker['lname']) . '",'.PHP_EOL;
	echo '"layer-createdby":"' . $marker['lcreatedby'] . '",'.PHP_EOL;
	echo '"layer-createdon":"' . $marker['lcreatedon'] . '",'.PHP_EOL;
	echo '"layer-updatedby":"' . stripslashes($marker['lupdatedby']) . '",'.PHP_EOL;
	echo '"layer-updatedon":"' . stripslashes($marker['lupdatedon']) . '",'.PHP_EOL;
	echo '"controlbox":"'.$marker['mcontrolbox'].'",'.PHP_EOL;
	echo '"overlays_custom":"'.$marker['moverlays_custom'].'",'.PHP_EOL;
	echo '"overlays_custom2":"'.$marker['moverlays_custom2'].'",'.PHP_EOL;
	echo '"overlays_custom3":"'.$marker['moverlays_custom3'].'",'.PHP_EOL;
	echo '"overlays_custom4":"'.$marker['moverlays_custom4'].'"'.PHP_EOL;
	echo '}}';
  }
  echo ']}';
  //info: callback for JSONP - part 2
  if ($callback != NULL) { echo ');'; }
}
} //info: end plugin active check
?>