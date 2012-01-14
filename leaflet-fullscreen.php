<?php
/*
    Fullscreen standalone maps - Leaflet Maps Marker Plugin
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
function hide_email($email) { $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'; $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999); for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])]; $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";'; $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));'; $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"'; $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script; }
if (!is_plugin_active('leaflet-maps-marker/leaflet-maps-marker.php')) {
echo 'The WordPress plugin <a href="http://www.mapsmarker.com" target="_blank">Leaflet Maps Marker</a> is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (' . hide_email(get_bloginfo('admin_email')) . ') who can activate this plugin again.';
} else {
global $wpdb;
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$lmm_options = get_option( 'leafletmapsmarker_options' );
if (isset($_GET['layer'])) {
	$layer = mysql_real_escape_string($_GET['layer']); //info: not intval() cause otherwise $layer=0 when creating new layer and showing all markers with layer id = 0
	$uid = substr(md5(''.rand()), 0, 8);
	$pname = 'pa'.$uid;
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$row = $wpdb->get_row('SELECT id,name,basemap,mapwidth,mapheight,mapwidthunit,panel,layerzoom,layerviewlat,layerviewlon,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10 FROM '.$table_name_layers.' WHERE id='.$layer, ARRAY_A);
	$id = $row['id'];
	$basemap = $row['basemap'];
	$lat = $row['layerviewlat'];
	$lon = $row['layerviewlon'];
	$zoom = $row['layerzoom'];
	$mapwidth = $row['mapwidth'];
	$mapheight = $row['mapheight'];
	$mapwidthunit = $row['mapwidthunit'];
	$panel = $row['panel'];
	$paneltext = ($row['name'] == NULL) ? '&nbsp;' : $row['name'];
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
	$mapname = 'mapsmarker_'.$uid;
	//info: check if layer/marker ID exists
	if ($row == NULL) {
	$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), $layer); 
	echo $error_layer_not_exists . '<br/>';
	echo '<a href="http://www.mapsmarker.com" target="_blank" title="' . __('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'img/map-deleted-image.png"></a><br/>';
	} else {	
	
	//info: starting output on frontend
	$lmm_out = '<!DOCTYPE html>'.PHP_EOL;
	$lmm_out .= '<!--[if IE 6]>'.PHP_EOL;
	$lmm_out .= '<html id="ie6" dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if IE 7]>'.PHP_EOL;
	$lmm_out .= '<html id="ie7" dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if IE 8]>'.PHP_EOL;
	$lmm_out .= '<html id="ie8" dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->'.PHP_EOL;
	$lmm_out .= '<html dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<!--<![endif]-->'.PHP_EOL;
	$lmm_out .= '<head>'.PHP_EOL;
	$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css" type="text/css" media="all">'.PHP_EOL;
	$lmm_out .= '<!--[if lt IE 9]>'.PHP_EOL;
	$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-ie-only-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.ie.css" type="text/css" media="all" / >'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-includes/js/jquery/jquery.js"></script>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= 'var leafletmapsmarker_L10n = {"lmm_zoom_in":"' . __('Zoom in','lmm') . '","lmm_zoom_out":"' . __('Zoom out','lmm') . '"};'.PHP_EOL;
	$lmm_out .= '/* ]]> */'.PHP_EOL;
	$lmm_out .= '</script>'.PHP_EOL;
	$lmm_out .= '<style>form { margin: 0 ; } </style>'.PHP_EOL; //info: for layer controlbox
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js" type="text/css" media="all"></script>'.PHP_EOL;
	$lmm_out .= '<meta name="geo.position" content="' . $lat . ';' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="ICBM" content="' . $lat . ', ' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="page-type" content="' . __('map','lmm') . '" />'.PHP_EOL;
	$lmm_out .= '</head>'.PHP_EOL;
	$lmm_out .= '<body style="margin:0;padding:0;height:100%;background: ' . addslashes($lmm_options[ 'defaults_layer_panel_background_color' ]) . ';overflow:hidden;">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		$lmm_out .= '<div id="panel_top_' . $uid . '" style="background: ' . addslashes($lmm_options[ 'defaults_layer_panel_background_color' ]) . '; width:99%; padding:5px;">'.PHP_EOL;
		$lmm_out .= '<span style="' . addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ]) . '">' . $paneltext . '</span><span style="float:right;width:120px;text-align:right;">';
		if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as KML for Google Earth/Google Maps','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
			$lmm_out .= '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '&callback=jsonp" style="text-decoration:none;" title="' . __('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>';
		}
		$lmm_out .= '</span></div>'.PHP_EOL;
	}
	
	//info: if panel enabled, only 93% height as otherwise attribution won´t be visible
	if ($panel == 1) {
	$lmm_out .= PHP_EOL.'<div id="'.$mapname.'"  data-marker="'.$layer.'" style="width:100%; height:93%; height:auto !important; min-height: 93%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"></div>'. PHP_EOL;	
	} else {
	$lmm_out .= PHP_EOL.'<div id="'.$mapname.'"  data-marker="'.$layer.'" style="width:100%; height:100%; height:auto !important; min-height: 100%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"></div>'. PHP_EOL;	
	}
	//info: add geo microformats
	$layermarklist = $wpdb->get_results('SELECT l.id as lid,l.name as lname, m.lon as mlon, m.lat as mlat, m.markername as markername,m.id as markerid FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$layer, ARRAY_A);
	if (count($layermarklist) < 1) {
		$lmm_out .= '<div class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
	} else {
		foreach ($layermarklist as $row){
			$lmm_out .= '<div class="lmm-geo-tags geo">' . $row['markername'] . ': <span class="latitude">' . $row['mlat'] . '</span>, <span class="longitude">' . $row['mlon'] . '</span></div>'.PHP_EOL;
		}
	}
	$plugin_version = get_option('leafletmapsmarker_version');
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= '/* Maps created with MapsMarker.com (WordPress Plugin powered by Leaflet from Cloudmade) - version '.$plugin_version.' */'.PHP_EOL;
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var mapsmarker_'.$uid.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	$attrib_prefix = __("Plugin","lmm").': <a href=\"http://mapsmarker.com/go\" target=\"_blank\" title=\"powered by \'Leaflet Maps Marker\'-Plugin for WordPress\">MapsMarker.com</a> (<a href=\"http://leaflet.cloudmade.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses the JavaScript library \'Leaflet\' for interactive maps by CloudMade\">Leaflet</a>, <a href=\"http://mapicons.nicolasmollet.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses icons from the \'Maps Icons Collection\'\">Icons</a>)'; 
	//difference osm mapnik/osmarender + ogdwien basemap/satellite: style=\"\" -> if exactly the same, attribution link doesnt work
	$attrib_osm_mapnik = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\" style=\"\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
	$attrib_osm_osmarender = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
	$attrib_mapquest_osm = __("Map",'lmm').': Tiles Courtesy of <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'img/logo-mapquest.png\" style=\"\" />';
	$attrib_mapquest_aerial = __("Map",'lmm').': <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'img/logo-mapquest.png\" />, Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency';
	$attrib_ogdwien_basemap = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\" style=\"\">data.wien.gv.at</a>)';
	$attrib_ogdwien_satellite = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\">data.wien.gv.at</a>)';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap_attribution' ]);
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap2_attribution' ]);
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap3_attribution' ]);
	$lmm_out .= '(function($) {'.PHP_EOL;
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { crs: ' . $lmm_options['misc_projections'] . ' });'.PHP_EOL;
	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;
	//info: define basemaps
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_osm_mapnik . '"});'.PHP_EOL;
	$lmm_out .= 'var osm_osmarender = new L.TileLayer("http://{s}.tah.openstreetmap.org/Tiles/tile/{z}/{x}/{y}.png", {maxZoom: 17, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_osm_osmarender . '"});'.PHP_EOL;
	$lmm_out .= 'var mapquest_osm = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_mapquest_osm . '", subdomains: ["otile1","otile2","otile3","otile4"]});'.PHP_EOL;
	$lmm_out .= 'var mapquest_aerial = new L.TileLayer("http://{s}.mqcdn.com/naip/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_mapquest_aerial . '", subdomains: ["oatile1","oatile2","oatile3","oatile4"]});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("http://{s}.wien.gv.at/wmts/fmzk/pastell/google3857/{z}/{y}/{x}.jpeg", {maxZoom: 19, minZoom: 11, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_ogdwien_basemap . '", subdomains: ["maps","maps1", "maps2", "maps3"]});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("http://{s}.wien.gv.at/wmts/lb/farbe/google3857/{z}/{y}/{x}.jpeg", {maxZoom: 19, minZoom: 11, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_ogdwien_satellite . '", subdomains: ["maps","maps1", "maps2", "maps3"]});'.PHP_EOL;
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$lmm_out .= 'var custom_basemap = new L.TileLayer("' . $lmm_options[ 'custom_basemap_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . $lmm_options[ 'custom_basemap2_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . $lmm_options[ 'custom_basemap3_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . '});'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	
	//info: define overlays
	$lmm_out .= 'var overlays_custom = new L.TileLayer("' . $lmm_options[ 'overlays_custom_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . $lmm_options[ 'overlays_custom2_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom2_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . $lmm_options[ 'overlays_custom3_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom3_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . $lmm_options[ 'overlays_custom4_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom4_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . '});'.PHP_EOL;
	//info: check if subdomains are set for wms layers
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
	$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms2_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms3_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms4_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms5_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms6_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms7_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms8_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms9_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms10_legend' ] . "&quot; target=&quot;_blank&quot;>" . __('Legend','lmm') . "</a>)" : '') .'';
	//info: define wms layers
	$lmm_out .= 'wms = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms_baseurl' ] . '", {wmsid: "wms", layers: "' . addslashes($lmm_options[ 'wms_wms_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms_format' ]) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms_version' ]) . '"' . $wms_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms2 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms2_baseurl' ] . '", {wmsid: "wms2", layers: "' . addslashes($lmm_options[ 'wms_wms2_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms2_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms2_format' ]) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms2_version' ]) . '"' . $wms2_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms3 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms3_baseurl' ] . '", {wmsid: "wms3", layers: "' . addslashes($lmm_options[ 'wms_wms3_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms3_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms3_format' ]) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms3_version' ]) . '"' . $wms3_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms4 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms4_baseurl' ] . '", {wmsid: "wms4", layers: "' . addslashes($lmm_options[ 'wms_wms4_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms4_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms4_format' ]) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms4_version' ]) . '"' . $wms4_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms5 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms5_baseurl' ] . '", {wmsid: "wms5", layers: "' . addslashes($lmm_options[ 'wms_wms5_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms5_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms5_format' ]) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms5_version' ]) . '"' . $wms5_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms6 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms6_baseurl' ] . '", {wmsid: "wms6", layers: "' . addslashes($lmm_options[ 'wms_wms6_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms6_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms6_format' ]) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms6_version' ]) . '"' . $wms6_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms7 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms7_baseurl' ] . '", {wmsid: "wms7", layers: "' . addslashes($lmm_options[ 'wms_wms7_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms7_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms7_format' ]) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms7_version' ]) . '"' . $wms7_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms8 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms8_baseurl' ] . '", {wmsid: "wms8", layers: "' . addslashes($lmm_options[ 'wms_wms8_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms8_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms8_format' ]) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms8_version' ]) . '"' . $wms8_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms9 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms9_baseurl' ] . '", {wmsid: "wms9", layers: "' . addslashes($lmm_options[ 'wms_wms9_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms9_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms9_format' ]) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms9_version' ]) . '"' . $wms9_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms10 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms10_baseurl' ] . '", {wmsid: "wms10", layers: "' . addslashes($lmm_options[ 'wms_wms10_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms10_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms10_format' ]) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms10_version' ]) . '"' . $wms10_subdomains  . '});'.PHP_EOL;
	//info: controlbox - basemaps
	$lmm_out .= 'var layersControl = new L.Control.Layers('.PHP_EOL;
	$lmm_out .= '{';
	$basemaps_available = '';
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
	$lmm_out .= substr($basemaps_available, 0, -1);
	$lmm_out .= '},'.PHP_EOL;
	
	//info: controlbox - add available overlays
	$lmm_out .= '{';
	$overlays_custom_available = '';
	if ( (isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom_name' ])."': overlays_custom,";
	if ( (isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom2_name' ])."': overlays_custom2,";
	if ( (isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom3_name' ])."': overlays_custom3,";
	if ( (isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 ) ) 
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom4_name' ])."': overlays_custom4,"; 
	//info: needed for IE7 compatibility
	$lmm_out .= substr($overlays_custom_available, 0, -1);
	$lmm_out .= '},'.PHP_EOL;
	
	//info: controlbox - hidden / collapsed / expanded status
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 0 ) )
		$lmm_out .= '{ } );'.PHP_EOL;
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 1 ) )
		$lmm_out .= '{ collapsed: !L.Browser.touch } );'.PHP_EOL;
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 2 ) )
		$lmm_out .= '{ collapsed: false } );'.PHP_EOL;
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ')';
	//info: controlbox - check active overlays on marker/layer level
	//2do - remove isset-check - not necessary anymore, as sql result check is now global
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= ".addLayer(overlays_custom)";
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= ".addLayer(overlays_custom2)";
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= ".addLayer(overlays_custom3)";
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= ".addLayer(overlays_custom4)";
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= ".addLayer(wms)";
	if ( $wms2 == 1 )
		$lmm_out .= ".addLayer(wms2)";
	if ( $wms3 == 1 )
		$lmm_out .= ".addLayer(wms3)";
	if ( $wms4 == 1 )
		$lmm_out .= ".addLayer(wms4)";
	if ( $wms5 == 1 )
		$lmm_out .= ".addLayer(wms5)";
	if ( $wms6 == 1 )
		$lmm_out .= ".addLayer(wms6)";
	if ( $wms7 == 1 )
		$lmm_out .= ".addLayer(wms7)";
	if ( $wms8 == 1 )
		$lmm_out .= ".addLayer(wms8)";
	if ( $wms9 == 1 )
		$lmm_out .= ".addLayer(wms9)";
	if ( $wms10 == 1 )
		$lmm_out .= ".addLayer(wms10)";
	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? ".addControl(layersControl);" : ";".PHP_EOL;
	
	if (!(empty($mlat) or empty($mlon)) ) {
	$lmm_out .= 'var marker = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'));'.PHP_EOL;
	if (!empty($micon)) $lmm_out .= 'marker.options.icon = new L.Icon("'.LEAFLET_PLUGIN_ICONS_URL . '/'.$micon.'");'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(marker);'.PHP_EOL;
	if (!empty($mpopuptext)) $lmm_out .= 'marker.bindPopup("' . preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$mpopuptext) . '")'.$mopenpopup.';'.PHP_EOL;
	} else if (!empty($geojson) or !empty($geojsonurl) or !empty($layer) ) {
		$lmm_out .= 'var geojson = new L.GeoJSON();'.PHP_EOL;
		$lmm_out .= 'geojson.on("featureparse",  function(e) {'.PHP_EOL;
		$lmm_out .= 'if (typeof e.properties.text != \'undefined\') e.layer.bindPopup(e.properties.text);'.PHP_EOL;
		$lmm_out .= 'e.layer.options.icon = new L.Icon(e.properties.icon);'.PHP_EOL;
		$lmm_out .= 'layers[e.properties.layer] = e.properties.layername;'.PHP_EOL;
		$lmm_out .= 'if (typeof markers[e.properties.layer] == \'undefined\') markers[e.properties.layer] = [];'.PHP_EOL;
		$lmm_out .= 'markers[e.properties.layer].push(e.layer);'.PHP_EOL;
		$lmm_out .= '});'.PHP_EOL;
		$lmm_out .= 'var geojsonObj;'.PHP_EOL;
	if (!empty($geojson)) {
	$lmm_out .= 'geojsonObj = eval("'.$geojson.'");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	if (!empty($geojsonurl)) {
	$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "'.$geojsonurl.'", async: false}).responseText + ")");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	if (!empty($layer)) {
	$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer='.$layer.'", async: false}).responseText + ")");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	$lmm_out .= $mapname.'.addLayer(geojson);'.PHP_EOL;
    }
  $lmm_out .= '})(jQuery);'.PHP_EOL;
  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>';
  $lmm_out .= '</body>';
  $lmm_out .= '</html>';
  echo $lmm_out;
  	} //info: end check if marker/layer exists	 	
} //info: end isset($_GET['layer'])
elseif (isset($_GET['marker'])) {
	$markerid = mysql_real_escape_string($_GET['marker']);
    $uid = substr(md5(''.rand()), 0, 8);
    $pname = 'pa'.$uid;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
		$row = $wpdb->get_row('SELECT id,markername,basemap,layer,lat,lon,icon,popuptext,zoom,openpopup,mapwidth,mapwidthunit,mapheight,panel,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10 FROM '.$table_name_markers.' WHERE id='.$markerid, ARRAY_A);
		if(!empty($row)) {
			$id = $row['id'];
			$basemap = $row['basemap'];
			$lon = $row['lon'];
			$lat = $row['lat'];
			$coords = $lat.', '.$lon;
			$icon = $row['icon'];
			$popuptext = $row['popuptext'];
			$zoom = $row['zoom'];
			$openpopup = ($row['openpopup'] == 1) ? '.openPopup()' : '';
			$mopenpopup = $openpopup;
			$layer = $row['layer'];
			$mlat = $lat;
			$mlon = $lon;
			$mpopuptext = $popuptext;
			$micon = $icon;
			$mapwidth = $row['mapwidth'];
			$mapwidthunit = $row['mapwidthunit'];
			$mapheight = $row['mapheight'];
			$panel = $row['panel'];
			$paneltext = ($row['markername'] == NULL) ? '&nbsp;' : $row['markername'];
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
			$mapname = 'mapsmarker_'.$uid;
		}
	//info: check if layer/marker ID exists
	if ($row == NULL) {
	$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $markerid); 
	echo $error_marker_not_exists . '<br/>';
	echo '<a href="http://www.mapsmarker.com" target="_blank" title="' . __('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'img/map-deleted-image.png"></a><br/>';
	} else {	
	
	//info: starting output on frontend
	$lmm_out = '<!DOCTYPE html>'.PHP_EOL;
	$lmm_out .= '<!--[if IE 6]>'.PHP_EOL;
	$lmm_out .= '<html id="ie6" dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if IE 7]>'.PHP_EOL;
	$lmm_out .= '<html id="ie7" dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if IE 8]>'.PHP_EOL;
	$lmm_out .= '<html id="ie8" dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->'.PHP_EOL;
	$lmm_out .= '<html dir="ltr" lang="de-DE">'.PHP_EOL;
	$lmm_out .= '<!--<![endif]-->'.PHP_EOL;
	$lmm_out .= '<head>'.PHP_EOL;
	$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css" type="text/css" media="all">'.PHP_EOL;
	$lmm_out .= '<!--[if lt IE 9]>'.PHP_EOL;
	$lmm_out .= '<link rel="stylesheet" id="leafletmapsmarker-ie-only-css" href="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.ie.css" type="text/css" media="all" / >'.PHP_EOL;
	$lmm_out .= '<![endif]-->'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-includes/js/jquery/jquery.js"></script>'.PHP_EOL;
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= 'var leafletmapsmarker_L10n = {"lmm_zoom_in":"' . __('Zoom in','lmm') . '","lmm_zoom_out":"' . __('Zoom out','lmm') . '"};'.PHP_EOL;
	$lmm_out .= '/* ]]> */'.PHP_EOL;
	$lmm_out .= '</script>'.PHP_EOL;
	$lmm_out .= '<style>form { margin: 0 ; } </style>'.PHP_EOL; //info: for layer controlbox
	$lmm_out .= '<script type="text/javascript" src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js" type="text/css" media="all"></script>'.PHP_EOL;
	$lmm_out .= '<meta name="geo.position" content="' . $lat . ';' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="ICBM" content="' . $lat . ', ' . $lon . '" />'.PHP_EOL;
	$lmm_out .= '<meta name="page-type" content="' . __('map','lmm') . '" />'.PHP_EOL;
	$lmm_out .= '</head>'.PHP_EOL;
	$lmm_out .= '<body style="margin:0;padding:0;height:100%;background: ' . addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]) . ';overflow:hidden;">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		$lmm_out .= '<div id="panel_top_' . $uid . '" style="background: ' . addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]) . '; width:99%; padding:5px;">'.PHP_EOL;
		$lmm_out .= '<span style="' . addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ]) . '">' . $paneltext . '</span><span style="float:right;width:120px;text-align:right;">';
		if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as KML for Google Earth/Google Maps','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
			$lmm_out .= '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp" style="text-decoration:none;" title="' . __('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>&nbsp;';
		}
		if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
			$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" style="background:no-repeat;margin:0px;padding:0px;border:none;" /></a>';
		}
		$lmm_out .= '</span></div>'.PHP_EOL;
	}
	
	//info: if panel enabled, only 93% height as otherwise attribution won´t be visible
	if ($panel == 1) {
	$lmm_out .= PHP_EOL.'<div id="'.$mapname.'"  data-marker="'.$markerid.'" style="width:100%; height:93%; height:auto !important; min-height: 93%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"></div>'. PHP_EOL;	
	} else {
	$lmm_out .= PHP_EOL.'<div id="'.$mapname.'"  data-marker="'.$markerid.'" style="width:100%; height:100%; height:auto !important; min-height: 100%; overflow: hidden !important; background:#ccc; padding:0; border:none; position:absolute;"></div>'. PHP_EOL;	
	}
	//info: add geo microformats
	$lmm_out .= '<div class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
	$plugin_version = get_option('leafletmapsmarker_version');
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= '/* Maps created with MapsMarker.com (WordPress Plugin powered by Leaflet from Cloudmade) - version '.$plugin_version.' */'.PHP_EOL;
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var mapsmarker_'.$uid.' = {};'.PHP_EOL;
	//info: define attribution links as variables to allow dynamic change through layer control box
	$attrib_prefix = __("Plugin","lmm").': <a href=\"http://mapsmarker.com/go\" target=\"_blank\" title=\"powered by \'Leaflet Maps Marker\'-Plugin for WordPress\">MapsMarker.com</a> (<a href=\"http://leaflet.cloudmade.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses the JavaScript library \'Leaflet\' for interactive maps by CloudMade\">Leaflet</a>, <a href=\"http://mapicons.nicolasmollet.com\" target=\"_blank\" title=\"\'Leaflet Maps Marker\' uses icons from the \'Maps Icons Collection\'\">Icons</a>)'; 
	//difference osm mapnik/osmarender + ogdwien basemap/satellite: style=\"\" -> if exactly the same, attribution link doesnt work
	$attrib_osm_mapnik = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\" style=\"\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
	$attrib_osm_osmarender = __("Map",'lmm').': &copy; ' . date("Y") . ' <a href=\"http://www.openstreetmap.org\" target=\"_blank\">OpenStreetMap contributors</a>, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\" target=\"_blank\">CC-BY-SA</a>';
	$attrib_mapquest_osm = __("Map",'lmm').': Tiles Courtesy of <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'img/logo-mapquest.png\" style=\"\" />';
	$attrib_mapquest_aerial = __("Map",'lmm').': <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'img/logo-mapquest.png\" />, Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency';
	$attrib_ogdwien_basemap = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\" style=\"\">data.wien.gv.at</a>)';
	$attrib_ogdwien_satellite = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\">data.wien.gv.at</a>)';
	$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap_attribution' ]);
	$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap2_attribution' ]);
	$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap3_attribution' ]);
	$lmm_out .= '(function($) {'.PHP_EOL;
	$lmm_out .= $mapname.' = new L.Map("'.$mapname.'", { crs: ' . $lmm_options['misc_projections'] . ' });'.PHP_EOL;
	$lmm_out .= $mapname.'.attributionControl.setPrefix("' . $attrib_prefix . '");'.PHP_EOL;
	//info: define basemaps
	$lmm_out .= 'var osm_mapnik = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_osm_mapnik . '"});'.PHP_EOL;
	$lmm_out .= 'var osm_osmarender = new L.TileLayer("http://{s}.tah.openstreetmap.org/Tiles/tile/{z}/{x}/{y}.png", {maxZoom: 17, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_osm_osmarender . '"});'.PHP_EOL;
	$lmm_out .= 'var mapquest_osm = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_mapquest_osm . '", subdomains: ["otile1","otile2","otile3","otile4"]});'.PHP_EOL;
	$lmm_out .= 'var mapquest_aerial = new L.TileLayer("http://{s}.mqcdn.com/naip/{z}/{x}/{y}.png", {maxZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_mapquest_aerial . '", subdomains: ["oatile1","oatile2","oatile3","oatile4"]});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_basemap = new L.TileLayer("http://{s}.wien.gv.at/wmts/fmzk/pastell/google3857/{z}/{y}/{x}.jpeg", {maxZoom: 19, minZoom: 11, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_ogdwien_basemap . '", subdomains: ["maps","maps1", "maps2", "maps3"]});'.PHP_EOL;
	$lmm_out .= 'var ogdwien_satellite = new L.TileLayer("http://{s}.wien.gv.at/wmts/lb/farbe/google3857/{z}/{y}/{x}.jpeg", {maxZoom: 19, minZoom: 11, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_ogdwien_satellite . '", subdomains: ["maps","maps1", "maps2", "maps3"]});'.PHP_EOL;
	//info: check if subdomains are set for custom basemaps
	$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	//info: define custom basemaps
	$lmm_out .= 'var custom_basemap = new L.TileLayer("' . $lmm_options[ 'custom_basemap_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap_minzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_custom_basemap . '"' . $custom_basemap_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap2 = new L.TileLayer("' . $lmm_options[ 'custom_basemap2_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap2_minzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_custom_basemap2 . '"' . $custom_basemap2_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var custom_basemap3 = new L.TileLayer("' . $lmm_options[ 'custom_basemap3_tileurl' ] . '", {maxZoom: ' . intval($lmm_options[ 'custom_basemap3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'custom_basemap3_minzoom' ]) . ', errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . $attrib_custom_basemap3 . '"' . $custom_basemap3_subdomains . '});'.PHP_EOL;
	//info: check if subdomains are set for custom overlays
	$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
	
	//info: define overlays
	$lmm_out .= 'var overlays_custom = new L.TileLayer("' . $lmm_options[ 'overlays_custom_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom_minzoom' ]) . $overlays_custom_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var overlays_custom2 = new L.TileLayer("' . $lmm_options[ 'overlays_custom2_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom2_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom2_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom2_minzoom' ]) . $overlays_custom2_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var overlays_custom3 = new L.TileLayer("' . $lmm_options[ 'overlays_custom3_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom3_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom3_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom3_minzoom' ]) . $overlays_custom3_subdomains . '});'.PHP_EOL;
	$lmm_out .= 'var overlays_custom4 = new L.TileLayer("' . $lmm_options[ 'overlays_custom4_tileurl' ] . '", {errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'img/error-tile-image.png", attribution: "' . addslashes($lmm_options[ 'overlays_custom4_attribution' ]) . '", maxZoom: ' . intval($lmm_options[ 'overlays_custom4_maxzoom' ]) . ', minZoom: ' . intval($lmm_options[ 'overlays_custom4_minzoom' ]) . $overlays_custom_subdomains . '});'.PHP_EOL;
	//info: check if subdomains are set for wms layers
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
	$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms2_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms3_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms4_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms5_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms6_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms7_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms8_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms9_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? " (<a href='" . $lmm_options[ 'wms_wms10_legend' ] . "' target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	//info: define wms layers
	$lmm_out .= 'wms = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms_baseurl' ] . '", {wmsid: "wms", layers: "' . addslashes($lmm_options[ 'wms_wms_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms_format' ]) . '", attribution: "' . $wms_attribution . '", transparent: "' . $lmm_options[ 'wms_wms_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms_version' ]) . '"' . $wms_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms2 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms2_baseurl' ] . '", {wmsid: "wms2", layers: "' . addslashes($lmm_options[ 'wms_wms2_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms2_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms2_format' ]) . '", attribution: "' . $wms2_attribution . '", transparent: "' . $lmm_options[ 'wms_wms2_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms2_version' ]) . '"' . $wms2_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms3 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms3_baseurl' ] . '", {wmsid: "wms3", layers: "' . addslashes($lmm_options[ 'wms_wms3_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms3_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms3_format' ]) . '", attribution: "' . $wms3_attribution . '", transparent: "' . $lmm_options[ 'wms_wms3_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms3_version' ]) . '"' . $wms3_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms4 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms4_baseurl' ] . '", {wmsid: "wms4", layers: "' . addslashes($lmm_options[ 'wms_wms4_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms4_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms4_format' ]) . '", attribution: "' . $wms4_attribution . '", transparent: "' . $lmm_options[ 'wms_wms4_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms4_version' ]) . '"' . $wms4_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms5 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms5_baseurl' ] . '", {wmsid: "wms5", layers: "' . addslashes($lmm_options[ 'wms_wms5_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms5_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms5_format' ]) . '", attribution: "' . $wms5_attribution . '", transparent: "' . $lmm_options[ 'wms_wms5_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms5_version' ]) . '"' . $wms5_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms6 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms6_baseurl' ] . '", {wmsid: "wms6", layers: "' . addslashes($lmm_options[ 'wms_wms6_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms6_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms6_format' ]) . '", attribution: "' . $wms6_attribution . '", transparent: "' . $lmm_options[ 'wms_wms6_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms6_version' ]) . '"' . $wms6_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms7 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms7_baseurl' ] . '", {wmsid: "wms7", layers: "' . addslashes($lmm_options[ 'wms_wms7_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms7_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms7_format' ]) . '", attribution: "' . $wms7_attribution . '", transparent: "' . $lmm_options[ 'wms_wms7_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms7_version' ]) . '"' . $wms7_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms8 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms8_baseurl' ] . '", {wmsid: "wms8", layers: "' . addslashes($lmm_options[ 'wms_wms8_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms8_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms8_format' ]) . '", attribution: "' . $wms8_attribution . '", transparent: "' . $lmm_options[ 'wms_wms8_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms8_version' ]) . '"' . $wms8_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms9 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms9_baseurl' ] . '", {wmsid: "wms9", layers: "' . addslashes($lmm_options[ 'wms_wms9_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms9_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms9_format' ]) . '", attribution: "' . $wms9_attribution . '", transparent: "' . $lmm_options[ 'wms_wms9_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms9_version' ]) . '"' . $wms9_subdomains  . '});'.PHP_EOL;
	$lmm_out .= 'wms10 = new L.TileLayer.WMS("' . $lmm_options[ 'wms_wms10_baseurl' ] . '", {wmsid: "wms10", layers: "' . addslashes($lmm_options[ 'wms_wms10_layers' ]) . '", styles: "' . addslashes($lmm_options[ 'wms_wms10_styles' ]) . '", format: "' . addslashes($lmm_options[ 'wms_wms10_format' ]) . '", attribution: "' . $wms10_attribution . '", transparent: "' . $lmm_options[ 'wms_wms10_transparent' ] . '", errorTileUrl: "' . LEAFLET_PLUGIN_URL  . 'img/error-tile-image.png", version: "' . addslashes($lmm_options[ 'wms_wms10_version' ]) . '"' . $wms10_subdomains  . '});'.PHP_EOL;
	//info: controlbox - basemaps
	$lmm_out .= 'var layersControl = new L.Control.Layers('.PHP_EOL;
	$lmm_out .= '{';
	$basemaps_available = '';
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
	$lmm_out .= substr($basemaps_available, 0, -1);
	$lmm_out .= '},'.PHP_EOL;
	
	//info: controlbox - add available overlays
	$lmm_out .= '{';
	$overlays_custom_available = '';
	if ( (isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom_name' ])."': overlays_custom,";
	if ( (isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom2_name' ])."': overlays_custom2,";
	if ( (isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 ) )
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom3_name' ])."': overlays_custom3,";
	if ( (isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 ) ) 
		$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom4_name' ])."': overlays_custom4,"; 
	//info: needed for IE7 compatibility
	$lmm_out .= substr($overlays_custom_available, 0, -1);
	$lmm_out .= '},'.PHP_EOL;
	
	//info: controlbox - hidden / collapsed / expanded status
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 0 ) )
		$lmm_out .= '{ } );';
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 1 ) )
		$lmm_out .= '{ collapsed: !L.Browser.touch } );';
	if ( (isset($controlbox) == TRUE ) && ( $controlbox == 2 ) )
		$lmm_out .= '{ collapsed: false } );';
	$lmm_out .= $mapname.'.setView(new L.LatLng('.$lat.', '.$lon.'), '.$zoom.');'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(' . $basemap . ')';
	//info: controlbox - check active overlays on marker/layer level
	//2do - remove isset-check - not necessary anymore, as sql result check is now global
	if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
		$lmm_out .= ".addLayer(overlays_custom)";
	if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
		$lmm_out .= ".addLayer(overlays_custom2)";
	if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
		$lmm_out .= ".addLayer(overlays_custom3)";
	if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
		$lmm_out .= ".addLayer(overlays_custom4)";
	//info: controlbox - add active overlays on marker level
	if ( $wms == 1 )
		$lmm_out .= ".addLayer(wms)";
	if ( $wms2 == 1 )
		$lmm_out .= ".addLayer(wms2)";
	if ( $wms3 == 1 )
		$lmm_out .= ".addLayer(wms3)";
	if ( $wms4 == 1 )
		$lmm_out .= ".addLayer(wms4)";
	if ( $wms5 == 1 )
		$lmm_out .= ".addLayer(wms5)";
	if ( $wms6 == 1 )
		$lmm_out .= ".addLayer(wms6)";
	if ( $wms7 == 1 )
		$lmm_out .= ".addLayer(wms7)";
	if ( $wms8 == 1 )
		$lmm_out .= ".addLayer(wms8)";
	if ( $wms9 == 1 )
		$lmm_out .= ".addLayer(wms9)";
	if ( $wms10 == 1 )
		$lmm_out .= ".addLayer(wms10)";
	$lmm_out .= ( (isset($controlbox) == TRUE) && ($controlbox != 0) ) ? ".addControl(layersControl);" : ";".PHP_EOL;
	
	if (!(empty($mlat) or empty($mlon)) ) {
	$lmm_out .= 'var marker = new L.Marker(new L.LatLng('.$mlat.', '.$mlon.'));'.PHP_EOL;
	if (!empty($micon)) $lmm_out .= 'marker.options.icon = new L.Icon("'.LEAFLET_PLUGIN_ICONS_URL . '/'.$micon.'");'.PHP_EOL;
	$lmm_out .= $mapname.'.addLayer(marker);'.PHP_EOL;
	if (!empty($mpopuptext)) $lmm_out .= 'marker.bindPopup("' . preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$mpopuptext) . '")'.$mopenpopup.';'.PHP_EOL;
	} else if (!empty($geojson) or !empty($geojsonurl) or !empty($layer) ) {
		$lmm_out .= 'var geojson = new L.GeoJSON();'.PHP_EOL;
		$lmm_out .= 'geojson.on("featureparse",  function(e) {'.PHP_EOL;
		$lmm_out .= 'if (typeof e.properties.text != \'undefined\') e.layer.bindPopup(e.properties.text);'.PHP_EOL;
		$lmm_out .= 'e.layer.options.icon = new L.Icon(e.properties.icon);'.PHP_EOL;
		$lmm_out .= 'layers[e.properties.layer] = e.properties.layername;'.PHP_EOL;
		$lmm_out .= 'if (typeof markers[e.properties.layer] == \'undefined\') markers[e.properties.layer] = [];'.PHP_EOL;
		$lmm_out .= 'markers[e.properties.layer].push(e.layer);'.PHP_EOL;
		$lmm_out .= '});'.PHP_EOL;
		$lmm_out .= 'var geojsonObj;'.PHP_EOL;
	if (!empty($geojson)) {
	$lmm_out .= 'geojsonObj = eval("'.$geojson.'");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	if (!empty($geojsonurl)) {
	$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "'.$geojsonurl.'", async: false}).responseText + ")");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	if (!empty($layer)) {
	$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer='.$layer.'", async: false}).responseText + ")");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	$lmm_out .= $mapname.'.addLayer(geojson);'.PHP_EOL;
    }
  $lmm_out .= '})(jQuery);'.PHP_EOL;
  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>';
  $lmm_out .= '</body>';
  $lmm_out .= '</html>';
  echo $lmm_out;
  	} //info: end check if marker/layer exists
} //info: end isset($_GET['marker'])
} //info: end plugin active check
?>