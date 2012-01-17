<?php
/*
Plugin Name: Leaflet Maps Marker
Plugin URI: http://www.mapsmarker.com
Description: Pin, organize & show your favorite places through OpenStreetMap/WMTS, Google Maps/Earth (KML), GeoJSON, GeoRSS or Augmented-Reality browsers
Tags: map, maps, Leaflet, OpenStreetMap, geoJSON, OSM, travelblog, opendata, opengov, ogdwien, google maps, WMTS, geoRSS, location, geo, geocoding, geolocation, travel, mapnick, osmarender, cloudmade, mapquest, wms
Version: 1.3
Author: Robert Harm (with special support from Sindre Wimberger)
Author URI: http://www.harm.co.at
Donate link: http://www.mapsmarker.com/donations
Requires at least: 3.0
Tested up to: 3.4-alpha-19704
Requires at least PHP 5.2
Copyright 2011-2012 - @RobertHarm - All rights reserved
Parts of this plugin were originally based on the Leaflet Plugin by Hind (Copyright 2011)
	
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License v2 as published by
the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You have received a copy of the full GNU General Public License
along with this program (see file licence-gpl20.txt)
*/
//info: Compatibility checks
global $wp_version;
if (version_compare($wp_version,"3.0","<")){
  exit('[Leaflet Maps Marker Plugin - installation failed!]: WordPress Version 3.0 or higher is needed for this plugin (you are using version '.$wp_version.') - please upgrade your WordPress installation!');
}
if (version_compare(phpversion(),"5.2","<")){
  exit('[Leaflet Maps Marker Plugin - installation failed]: PHP 5.2 is needed for this plugin (you are using PHP '.phpversion().'; note: support for PHP 4 has been officially discontinued since 2007-12-31!) - please upgrade your PHP installation!');
}
//info: define necessary paths and urls
if ( ! defined( 'WP_ADMIN_URL' ) )
	define( 'WP_ADMIN_URL', get_admin_url() );
if ( ! defined( 'LEAFLET_PLUGIN_URL' ) )
define ("LEAFLET_PLUGIN_URL", plugin_dir_url(__FILE__));
$lmm_upload_dir = wp_upload_dir();
if ( ! defined( 'LEAFLET_PLUGIN_ICONS_URL' ) )
	define ("LEAFLET_PLUGIN_ICONS_URL", $lmm_upload_dir['baseurl'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons");
if ( ! defined( 'LEAFLET_PLUGIN_ICONS_DIR' ) )
	define ("LEAFLET_PLUGIN_ICONS_DIR", $lmm_upload_dir['basedir'] . DIRECTORY_SEPARATOR . "leaflet-maps-marker-icons");
//info: not in class Leafletmapsmarker as otherwise warnings on resetting defaults options
require_once( plugin_dir_path( __FILE__ ).'class-leaflet-options.php' );
class Leafletmapsmarker
{
function leafletmapsmarker() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	add_action('init', array(&$this, 'lmm_load_translation_files'),1);
	add_action('admin_init', array(&$this, 'lmm_install_and_updates'),2); //info: register_action_hook not used as otherwise Wordpress Network installs break
	add_action('wp_enqueue_scripts', array(&$this, 'lmm_frontend_enqueue_scripts'),3);
	add_action('wp_print_styles', array(&$this, 'lmm_frontend_enqueue_stylesheets'),4);
	add_action('admin_menu', array(&$this, 'lmm_admin_menu'),5);
	add_action('admin_init', array(&$this, 'lmm_plugin_meta_links'),6);
	add_action('admin_bar_menu', array(&$this, 'lmm_add_admin_bar_menu'),149);
	add_shortcode($lmm_options['shortcode'], array(&$this, 'lmm_showmap'));
   }
  function lmm_load_translation_files() {
	load_plugin_textdomain('lmm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
  }
  function lmm_help()
  {
    include('leaflet-help-credits.php');
  }
  function lmm_settings() {
    $lmm_options = new Leafletmapsmarker_options();
    $lmm_options->display_page();
  }
  function lmm_list_layers()
  {
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $columnsort = isset($_GET['orderby']) ? mysql_real_escape_string($_GET['orderby']) : 'id';
    $columnsortorder = isset($_GET['order']) ? mysql_real_escape_string($_GET['order']) : 'asc'; 
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $layerlist = $wpdb->get_results('SELECT * FROM '.$table_name_layers.' WHERE id>0 order by '.$columnsort.' '.$columnsortorder.'', ARRAY_A);
	$lcount = intval($wpdb->get_var('SELECT COUNT(*)-1 FROM '.$table_name_layers));
    include('leaflet-list-layers.php');
  }
  function lmm_list_markers()
  {
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    $radius = 1;
    $pagenum = isset($_POST['paged']) ? intval($_POST['paged']) : (isset($_GET['paged']) ? intval($_GET['paged']) : 1);
    $columnsort = isset($_GET['orderby']) ? mysql_real_escape_string($_GET['orderby']) : 'id'; 
    $columnsortorder = isset($_GET['order']) ? mysql_real_escape_string($_GET['order']) : 'asc'; 
    $start = ($pagenum - 1) * intval($lmm_options[ 'markers_per_page' ]);
    $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
    $searchtext = isset($_POST['searchtext']) ? $_POST['searchtext'] : (isset($_GET['searchtext']) ? mysql_real_escape_string($_GET['searchtext']) : '');
    if ($action == 'search') {
	$markersearchnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : '';
		if (! wp_verify_nonce($markersearchnonce, 'markersearch-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').'');
        $mcount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers.' WHERE markername like \'%'.$searchtext.'%'.'\' OR popuptext like \'%'.$searchtext.'%'.'\''));
		$marklist = $wpdb->get_results('SELECT m.id,CONCAT(m.lat,\',\',m.lon) AS coords,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,l.name AS layername,l.id as layerid FROM '.$table_name_markers.' AS m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id WHERE m.markername like \'%'.$searchtext.'%'.'\' OR m.popuptext like \'%'.$searchtext.'%'.'\' order by '.$columnsort.' '.$columnsortorder.' LIMIT '.intval($lmm_options[ 'markers_per_page' ]).' OFFSET '.$start, ARRAY_A);
	}
	  else
        {
        $mcount = intval($wpdb->get_var('SELECT COUNT(*) FROM '.$table_name_markers));
 	$marklist = $wpdb->get_results('SELECT m.id,CONCAT(m.lat,\',\',m.lon) AS coords,m.basemap,m.icon,m.popuptext,m.layer,m.zoom,m.openpopup as openpopup,m.lat,m.lon,m.mapwidth,m.mapheight,m.mapwidthunit,m.markername,m.panel,m.createdby,m.createdon,m.updatedby,m.updatedon,m.controlbox,m.overlays_custom,m.overlays_custom2,m.overlays_custom3,m.overlays_custom4,m.wms,m.wms2,m.wms3,m.wms4,m.wms5,m.wms6,m.wms7,m.wms8,m.wms9,m.wms10,l.name AS layername,l.id as layerid FROM '.$table_name_markers.' AS m LEFT OUTER JOIN '.$table_name_layers.' AS l ON m.layer=l.id order by '.$columnsort.' '.$columnsortorder.' LIMIT '.intval($lmm_options[ 'markers_per_page' ]).' OFFSET '.$start, ARRAY_A);
		}
    if ($start > $mcount or $start < 0)
       $start = 0;	
     include('leaflet-list-markers.php');
  }
  function lmm_layer()
  {
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    include('leaflet-layer.php');
  }
  function lmm_marker()
  {
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    include('leaflet-marker.php');
  }
  function lmm_tools()
  {
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
    $table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
    include('leaflet-tools.php');
  }
  function lmm_showmap($atts) {
    global $wpdb;
    $lmm_options = get_option( 'leafletmapsmarker_options' );
    $uid = substr(md5(''.rand()), 0, 8);
    extract(shortcode_atts(array(
    'lat' => '', 'lon' => '',
    'mlat' => '', 'mlon' => '',
    'basemap' => $lmm_options[ 'defaults_marker_shortcode_basemap'],
    'mpopuptext' => '',
    'micon' => '',
    'zoom' => intval($lmm_options[ 'defaults_marker_shortcode_zoom' ]),
    'openpopup' => '',
    'geojson' => '',
    'geojsonurl' => '',
    'layer' => '',
    'marker' => '',
    'markername' => '',
    'panel' => '0',
    'mapwidth' => intval($lmm_options[ 'defaults_marker_shortcode_mapwidth' ]),
    'mapwidthunit' => $lmm_options[ 'defaults_marker_shortcode_mapwidthunit' ],
    'mapheight' => intval($lmm_options[ 'defaults_marker_shortcode_mapheight' ]),
    'mapname' => 'lmm_map_'.$uid
    ), $atts));
    $pname = 'pa'.$uid;
	//info: prepare layers
	if (!empty($layer)) {
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
	}
	//info: prepare markers
    if (!empty($marker))  {
		    $table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
				$row = $wpdb->get_row('SELECT id,markername,basemap,layer,lat,lon,icon,popuptext,zoom,openpopup,mapwidth,mapwidthunit,mapheight,panel,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10 FROM '.$table_name_markers.' WHERE id='.$marker, ARRAY_A);
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
				}
    }
	//info: prepare markers only added by shortcode and not defined in backend
	if (empty($layer) and empty($marker)) {
		$lat = $mlat;
		$lon = $mlon;
		$controlbox = $lmm_options[ 'defaults_marker_shortcode_controlbox' ];
		$overlays_custom = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom_active' ] == 1 ) ? '1' : '0';
		$overlays_custom2 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom2_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom2_active' ] == 1 ) ? '1' : '0';
		$overlays_custom3 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom3_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom3_active' ] == 1 ) ? '1' : '0';
		$overlays_custom4 = isset($lmm_options[ 'defaults_marker_shortcode_overlays_custom4_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_overlays_custom4_active' ] == 1 ) ? '1' : '0';
		$wms = isset($lmm_options[ 'defaults_marker_shortcode_wms_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms_active' ] == 1 ) ? '1' : '0';
		$wms2 = isset($lmm_options[ 'defaults_marker_shortcode_wms2_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms2_active' ] == 1 ) ? '1' : '0';
		$wms3 = isset($lmm_options[ 'defaults_marker_shortcode_wms3_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms3_active' ] == 1 ) ? '1' : '0';
		$wms4 = isset($lmm_options[ 'defaults_marker_shortcode_wms4_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms4_active' ] == 1 ) ? '1' : '0';
		$wms5 = isset($lmm_options[ 'defaults_marker_shortcode_wms5_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms5_active' ] == 1 ) ? '1' : '0';
		$wms6 = isset($lmm_options[ 'defaults_marker_shortcode_wms6_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms6_active' ] == 1 ) ? '1' : '0';
		$wms7 = isset($lmm_options[ 'defaults_marker_shortcode_wms7_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms7_active' ] == 1 ) ? '1' : '0';
		$wms8 = isset($lmm_options[ 'defaults_marker_shortcode_wms8_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms8_active' ] == 1 ) ? '1' : '0';
		$wms9 = isset($lmm_options[ 'defaults_marker_shortcode_wms9_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms9_active' ] == 1 ) ? '1' : '0';
		$wms10 = isset($lmm_options[ 'defaults_marker_shortcode_wms10_active' ]) == TRUE && ($lmm_options[ 'defaults_marker_shortcode_wms10_active' ] == 1 ) ? '1' : '0';
	}
	
	//info: show static image with link in feeds
	if (is_feed()) {
		if ($lat != NULL) { //info: marker exists?
			if (empty($layer)) {
			$lmm_out = '<p>' . $paneltext . '<br/><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/map-rss-feed.png"/><br/>' . __('Show embedded map in full-screen mode','lmm') . '</a></p>';		
			}
			if (empty($marker)) {
			$lmm_out = '<p>' . $paneltext . '<br/><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/map-rss-feed.png"/><br/>' . __('Show embedded map in full-screen mode','lmm') . '</a></p>';		
			}
			return $lmm_out;
		}
	} else {
		
	//info: check if layer/marker ID exists
	if ($lat == NULL) {
	$error_layer_not_exists = sprintf( esc_attr__('Error: a layer with the ID %1$s does not exist!','lmm'), $layer); 
	$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), $marker); 
	$lmm_out = '<div id="lmm_error" style="margin:10px 0;">'.PHP_EOL;
		if (empty($layer)) {
			$lmm_out .= $error_marker_not_exists . '<br/>';
		}
		if (empty($marker)) {
			$lmm_out .= $error_layer_not_exists . '<br/>';
		}
	$lmm_out .= '<a href="http://www.mapsmarker.com" target="_blank" title="' . __('Go to plugin website','lmm') . '"><img style="border:1px solid #ccc;" src="' . LEAFLET_PLUGIN_URL . 'img/map-deleted-image.png"></a></div>';
	} else {	
	//info: starting output on frontend
	$lmm_out = ''; 
	$lmm_out .= '<div id="lmm_'.$uid.'" style="width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
	//info: panel for layer/marker name and API URLs
	if ($panel == 1) {
		$lmm_out .= '<div id="lmm_panel_'.$uid.'" class="lmm-panel" style="background: ' . ((!empty($marker)) ? addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]) : (!empty($layer)) ? addslashes($lmm_options[ 'defaults_layer_panel_background_color' ]) : '') . ';">'.PHP_EOL;
		if (!empty($marker)) 
		{
			$lmm_out .= '<div id="lmm_panel_api_'.$uid.'" class="lmm-panel-api">';
			if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as KML for Google Earth/Google Maps','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
				$lmm_out .= '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp" style="text-decoration:none;" title="' . __('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . __('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
			}
		$lmm_out .= '</div><div id="lmm_panel_text_'.$uid.'" class="lmm-panel-text" style="padding-left:5px;' . addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ]) . '">' . stripslashes($paneltext) . '</div>';
		}
		
		if (!empty($layer) && empty($marker)) //info: check if problems get reported - fix for marker name shown twice when layer+marker map on 1 page
		{
			$lmm_out .= '<div id="lmm_panel_api_'.$uid.'" class="lmm-panel-api">';
			if ( (isset($lmm_options[ 'defaults_layer_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_kml' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as KML for Google Earth/Google Maps','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_fullscreen' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_qr_code' ] == 1 ) ) {
				$lmm_out .= '<a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?layer=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_geojson' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?layer=' . $id . '&callback=jsonp" style="text-decoration:none;" title="' . __('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_georss' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
			}
			if ( (isset($lmm_options[ 'defaults_layer_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_layer_panel_wikitude' ] == 1 ) ) {
				$lmm_out .= '<a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?layer=' . $id . '" style="text-decoration:none;" title="' . __('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
			}
		$lmm_out .= '</div><div id="lmm_panel_text_'.$uid.'" class="lmm-panel-text" style="padding-left:5px;' . addslashes($lmm_options[ 'defaults_layer_panel_paneltext_css' ]) . '">' . stripslashes($paneltext) . '</div>'.PHP_EOL;			
		}
	$lmm_out .= '</div>'.PHP_EOL; //info: <!--end lmm-panel-->
	}
	$lmm_out .= PHP_EOL.'<div id="'.$mapname.'"  data-marker="'.$marker.'" style="height:'.$mapheight.'px; overflow:hidden;padding:0;"></div>'. PHP_EOL;	
	//info: add geo microformats for layer maps
	if (!empty($layer) && empty($marker))
	{
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$layermarklist = $wpdb->get_results('SELECT l.id as lid,l.name as lname, m.lon as mlon, m.lat as mlat, m.markername as markername,m.id as markerid FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$layer, ARRAY_A);
		if (count($layermarklist) < 1) {
			$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">' . $paneltext . ': <span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span></div>'.PHP_EOL;
		} else {
			foreach ($layermarklist as $row){
				$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">' . $row['markername'] . ': <span class="latitude">' . $row['mlat'] . '</span>, <span class="longitude">' . $row['mlon'] . '</span></div>'.PHP_EOL;
			}
		}
	}
	//info: add geo microformats for marker maps
	if (!empty($marker)) 
	{
	//info: add geo microformats
	$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">'.PHP_EOL;
	$lmm_out .= '<span class="paneltext">' . $paneltext . '</span>'.PHP_EOL;
	$lmm_out .= '<span class="latitude">' . $lat . '</span>, <span class="longitude">' . $lon . '</span>'.PHP_EOL;
	$lmm_out .= '<span class="popuptext">' . $popuptext .'</span>'.PHP_EOL;
	$lmm_out .= '</div>'.PHP_EOL;
	}
	//info: add geo microformats for marker maps added directly via shortcode
	if (empty($layer) && empty($marker)) 
	{
	//info: add geo microformats
	$lmm_out .= '<div id="lmm_geo_tags_'.$uid.'" class="lmm-geo-tags geo">'.PHP_EOL;
	$lmm_out .= '<span class="latitude">' . $mlat . '</span>, <span class="longitude">' . $mlon . '</span>'.PHP_EOL;
	$lmm_out .= '</div>'.PHP_EOL;
	}
	$plugin_version = get_option('leafletmapsmarker_version');
	$lmm_out .= '<script type="text/javascript">'.PHP_EOL;
	$lmm_out .= '/* <![CDATA[ */'.PHP_EOL;
	$lmm_out .= '/* Maps created with MapsMarker.com (WordPress Plugin powered by Leaflet from Cloudmade) - version '.$plugin_version.' */'.PHP_EOL;
	$lmm_out .= 'var layers = {};'.PHP_EOL;
	$lmm_out .= 'var markers = {};'.PHP_EOL;
	$lmm_out .= 'var lmm_map_'.$uid.' = {};'.PHP_EOL;
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
	$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms2_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms3_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms4_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms5_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms6_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms7_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms8_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms9_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
	$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? " (<a href=&quot;" . $lmm_options[ 'wms_wms10_legend' ] . "&quot; target='_blank'>" . __('Legend','lmm') . "</a>)" : '') .'';
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
	if (!empty($micon)) $lmm_out .= 'marker.options.icon = new L.Icon("' . LEAFLET_PLUGIN_ICONS_URL . '/'.$micon.'");'.PHP_EOL;
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
	//2do: check or delete 
	/*
	if (!empty($marker)) {
	$lmm_out .= 'geojsonObj = eval("(" + jQuery.ajax({url: "' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker='.$marker.'", async: false}).responseText + ")");'.PHP_EOL;
	$lmm_out .= 'geojson.addGeoJSON(geojsonObj);'.PHP_EOL;
	}
	*/
	$lmm_out .= $mapname.'.addLayer(geojson);'.PHP_EOL;
    }
  $lmm_out .= '})(jQuery);'.PHP_EOL;
  $lmm_out .= '/* ]] > */'.PHP_EOL;
  $lmm_out .= '</script>'.PHP_EOL;
  $lmm_out .= '</div>'; //info: end leaflet_maps_marker_$uid
  } //info: end (!is_feed())
  return $lmm_out;
  	} //info: end check if marker/layer exists
  } //info: end lmm_showmap()
  function lmm_admin_menu() {
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$page = add_object_page('Leaflet Maps Marker', 'Leaflet Maps Marker', $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_markers', array(&$this, 'lmm_list_markers'), LEAFLET_PLUGIN_URL . 'img/icon-menu-page.png' );
	$page2 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('List all markers', 'lmm'), __('List all markers', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_markers', array(&$this, 'lmm_list_markers') );
	$page3 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('add/edit marker', 'lmm'), __('Add new marker', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_marker', array(&$this, 'lmm_marker') );
	$page4 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('List all layers', 'lmm'), __('List all layers', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_layers', array(&$this, 'lmm_list_layers') );
	$page5 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('add/edit layer', 'lmm'), __('Add new layer', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_layer', array(&$this, 'lmm_layer') );
	$page6 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('Tools', 'lmm'), __('Tools', 'lmm'), 'activate_plugins','leafletmapsmarker_tools', array(&$this, 'lmm_tools') );
	$page7 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('Settings', 'lmm'), __('Settings', 'lmm'), 'activate_plugins','leafletmapsmarker_settings', array(&$this, 'lmm_settings') );
	$page8 = add_submenu_page('leafletmapsmarker_markers', 'Leaflet Maps Marker - ' . __('Help & Credits', 'lmm'), __('Help & Credits', 'lmm'), $lmm_options[ 'capabilities_edit' ], 'leafletmapsmarker_help', array(&$this, 'lmm_help') );
	//info: add javascript - leaflet.js - for admin area
	add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_admin_enqueue_scripts'),7);
	add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_admin_enqueue_scripts'),8);
	add_action('admin_print_scripts-'.$page7, array(&$this, 'lmm_admin_jquery_ui'),9); 
	//info: add css styles for admin area
	add_action('admin_print_styles-'.$page, array(&$this, 'lmm_admin_enqueue_stylesheets'),17);
	add_action('admin_print_styles-'.$page2, array(&$this, 'lmm_admin_enqueue_stylesheets'),18);
	add_action('admin_print_styles-'.$page3, array(&$this, 'lmm_admin_enqueue_stylesheets'),19);
	add_action('admin_print_styles-'.$page4, array(&$this, 'lmm_admin_enqueue_stylesheets'),20);
	add_action('admin_print_styles-'.$page5, array(&$this, 'lmm_admin_enqueue_stylesheets'),21);
	add_action('admin_print_styles-'.$page6, array(&$this, 'lmm_admin_enqueue_stylesheets'),22);
	add_action('admin_print_styles-'.$page7, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);
	add_action('admin_print_styles-'.$page8, array(&$this, 'lmm_admin_enqueue_stylesheets'),23);	
	//info: add contextual help on all pages
	add_action('admin_print_scripts-'.$page, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page2, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page3, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page4, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page5, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page6, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page7, array(&$this, 'lmm_add_contextual_help'));
	add_action('admin_print_scripts-'.$page8, array(&$this, 'lmm_add_contextual_help'));	
  }
  function lmm_add_admin_bar_menu() {
	global $wp_version;
	if ( version_compare( $wp_version, '3.1', '>=' ) ) 
	{
		$lmm_options = get_option( 'leafletmapsmarker_options' );
		if ( $lmm_options[ 'admin_bar_integration' ] == 'enabled' && current_user_can($lmm_options[ 'capabilities_edit' ]) ) 
		{
		global $wp_admin_bar;
			$menu_items = array(
				array(
					'id' => 'lmm',
					'title' => '<img style="float:left;margin:5px 5px 0 0;" src="' . LEAFLET_PLUGIN_URL . 'img/icon-menu-page.png"/></span> Leaflet Maps Marker',
					'href' => admin_url('admin.php?page=leafletmapsmarker_markers'),
					'meta' => array( 'title' => 'Wordpress-Plugin ' . __('by','lmm') . ' www.mapsmarker.com' )
				),
				array(
					'id' => 'lmm-markers',
					'parent' => 'lmm',
					'title' => __('List all markers','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_markers')
				),
				array(
					'id' => 'lmm-add-marker',
					'parent' => 'lmm',
					'title' => __('Add new marker','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_marker')
				),
				array(
					'id' => 'lmm-layers',
					'parent' => 'lmm',
					'title' => __('List all layers','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_layers')
				),
				array(
					'id' => 'lmm-add-layers',
					'parent' => 'lmm',
					'title' => __('Add new layer','lmm'),
					'href' => admin_url('admin.php?page=leafletmapsmarker_layer')
				)			
			);
			if ( current_user_can( 'activate_plugins' ) ) {
				$menu_items = array_merge($menu_items, array(
					array(
						'id' => 'lmm-tools',
						'parent' => 'lmm',
						'title' => __('Tools','lmm'),
						'href' => admin_url('admin.php?page=leafletmapsmarker_tools')
					),
					array(
						'id' => 'lmm-settings',
						'parent' => 'lmm',
						'title' => __('Settings','lmm'),
						'href' => admin_url('admin.php?page=leafletmapsmarker_settings')
					)
				));
			}
			$menu_items = array_merge($menu_items, array(
					array(
						'id' => 'lmm-help-credits',
						'parent' => 'lmm',
						'title' => __('Help & Credits','lmm'),
						'href' => admin_url('admin.php?page=leafletmapsmarker_help')
					),
					array(
						'id' => 'lmm-plugin-website',
						'parent' => 'lmm',
						'title' => 'mapsmarker.com',
						'href' => 'http://www.mapsmarker.com',
						'meta' => array( 'target' => '_blank', 'title' => __('Open plugin website','lmm') )
					)	
				));
			
			foreach ($menu_items as $menu_item) {
				$wp_admin_bar->add_menu($menu_item);
			}
		}
	}
  }
  function lmm_add_contextual_help() {
	global $wp_version;
	$helptext = '<h4>' . __('Do you have questions or issues with Leaflet Maps Marker? Please use the following support channels appropriately.','lmm') . '</h4>';
	$helptext .= '<ul>';
	$helptext .= '<li><a href="http://www.mapsmarker.com/faq/" target="_blank">' . __('FAQ','lmm') . '</a> (' . __('frequently asked questions','lmm') . ')</li>';
	$helptext .= '<li><a href="http://www.mapsmarker.com/docs/" target="_blank">' . __('Documentation','lmm') . '</a></li>';
	$helptext .= '<li><a href="http://www.mapsmarker.com/ideas/" target="_blank">' . __('Ideas','lmm') . '</a> (' . __('feature requests','lmm') . ')</li>';
	$helptext .= '<li><a href="http://wordpress.org/tags/leaflet-maps-marker?forum_id=10" target="_blank">WordPress Support Forum</a> (' . __('free community support','lmm') . ')</li>';
	$helptext .= '<li><a href="http://wpquestions.com/affiliates/register/name/robertharm" target="_blank">WP Questions</a> (' . __('paid community support','lmm') . ')</li>';
	$helptext .= '<li><a href="http://wphelpcenter.com/" target="_blank">WordPress HelpCenter</a> (' . __('paid professional support','lmm') . ')</li>';
	$helptext .= '</ul>';
	$helptext .= '<p>' . __('More information on support','lmm') . ': <a href="http://www.mapsmarker.com/support/" target="_blank">http://www.mapsmarker.com/support</a></p>';
	if ( version_compare( $wp_version, '3.3', '<' ) ) 
	{
		global $current_screen;
		add_contextual_help( $current_screen, $helptext );
	} 
	else if ( version_compare( $wp_version, '3.3', '>=' ) ) 
	{
        $screen = get_current_screen();
		$screen->add_help_tab( array( 'id' => 'lmm_help_tab', 'title' => __('Help & Support','lmm'), 'content' => $helptext ));
	}
  }
  function lmm_admin_jquery_ui() {
    wp_enqueue_script( array ( 'jquery', 'jquery-ui-tabs' ) );
  }
  function lmm_frontend_enqueue_scripts() {
	wp_enqueue_script( array ( 'jquery' ) );
	wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array(), NULL); 
	wp_localize_script ( 'leafletmapsmarker', 'leafletmapsmarker_L10n', array(
		'lmm_zoom_in' => __( 'Zoom in', 'lmm' ),
		'lmm_zoom_out' => __( 'Zoom out', 'lmm' )
		) );
  }
  function lmm_admin_enqueue_scripts() {
	wp_enqueue_script( array ( 'jquery' ) );
	wp_enqueue_script( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.js', array(), NULL); 
	wp_localize_script ( 'leafletmapsmarker', 'leafletmapsmarker_L10n', array(
		'lmm_zoom_in' => __( 'Zoom in', 'lmm' ),
		'lmm_zoom_out' => __( 'Zoom out', 'lmm' )
		) );
  }
  function lmm_frontend_enqueue_stylesheets() {
	global $wp_styles;
	wp_register_style('leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css', array(), NULL);
	wp_enqueue_style('leafletmapsmarker');
	wp_register_style('leafletmapsmarker-ie-only', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.ie.css', array(), NULL);
	wp_enqueue_style('leafletmapsmarker-ie-only');
	$wp_styles->add_data('leafletmapsmarker-ie-only', 'conditional', 'lt IE 9');
  }
  function lmm_admin_enqueue_stylesheets() {
	global $wp_styles;
	wp_register_style( 'leafletmapsmarker', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.css', array(), NULL );
	wp_enqueue_style( 'leafletmapsmarker' );
	wp_register_style( 'leafletmapsmarker-admin', LEAFLET_PLUGIN_URL . 'css/leafletmapsmarker-admin.css', array(), NULL );
	wp_enqueue_style('leafletmapsmarker-admin' );
	wp_register_style('leafletmapsmarker-ie-only', LEAFLET_PLUGIN_URL . 'leaflet-dist/leaflet.ie.css', array(), NULL);
	wp_enqueue_style('leafletmapsmarker-ie-only');
	$wp_styles->add_data('leafletmapsmarker-ie-only', 'conditional', 'lt IE 9');
   }
   function lmm_install_and_updates() {
	global $wpdb;
	//info: 2 options not managed by Settings API (class-leaflet-options.php) 
	add_option('leafletmapsmarker_version', 'init');
	add_option('leafletmapsmarker_redirect', 'true'); //redirect to plugin settings page after first activation only
	if (get_option('leafletmapsmarker_version') == 'init') {
		//info: copy map icons to wp-content/uploads
		WP_Filesystem();
		$target = LEAFLET_PLUGIN_ICONS_DIR;
		if (!is_dir($target)) //info: check for multisite installations not to extract files again if already installed on 1 site
		{
			wp_mkdir_p( $target );
			$source = plugin_dir_path(__FILE__) . 'img' . DIRECTORY_SEPARATOR . 'mapicons';
			copy_dir($source, $target, $skip_list = array() );
			$zipfile = LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'mapicons.zip';
			unzip_file( $zipfile, $target );
		}
		//info: create tables for markers & layers
		$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
		$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
		$sql_create_marker_table = "CREATE TABLE IF NOT EXISTS `" . $table_name_markers . "` (
			`id` int(6) unsigned NOT NULL auto_increment,
			`markername` varchar(255) CHARACTER SET utf8 NOT NULL,
			`basemap` varchar(20) NOT NULL,
			`layer` int(6) unsigned NOT NULL,
			`lat` decimal(9,6) NOT NULL,
			`lon` decimal(9,6) NOT NULL,
			`icon` varchar(255) CHARACTER SET utf8 NOT NULL,
			`popuptext` text CHARACTER SET utf8 NOT NULL,
			`zoom` int(2) NOT NULL,
			`openpopup` tinyint(1) NOT NULL,
			`mapwidth` int(4) NOT NULL,
			`mapwidthunit` varchar(2) NOT NULL,
			`mapheight` int(4) NOT NULL,
			`panel` tinyint(1) NOT NULL,
			`createdby` varchar(30) CHARACTER SET utf8 NOT NULL,
			`createdon` datetime NOT NULL,
			`updatedby` varchar(30) CHARACTER SET utf8 NOT NULL,
			`updatedon` datetime NOT NULL,
			`controlbox` int(1) NOT NULL,
			`overlays_custom` int(1) NOT NULL,
			`overlays_custom2` int(1) NOT NULL,
			`overlays_custom3` int(1) NOT NULL,
			`overlays_custom4` int(1) NOT NULL,
			`wms` int(1) NOT NULL,
			`wms2` int(1) NOT NULL,
			`wms3` int(1) NOT NULL,
			`wms4` int(1) NOT NULL,
			`wms5` int(1) NOT NULL,
			`wms6` int(1) NOT NULL,
			`wms7` int(1) NOT NULL,
			`wms8` int(1) NOT NULL,
			`wms9` int(1) NOT NULL,
			`wms10` int(1) NOT NULL,
			PRIMARY KEY  (`id`)
		)  ENGINE=MyISAM  DEFAULT CHARSET=utf8  ;";
		$wpdb->query($sql_create_marker_table);
		$sql_create_layer_table = "CREATE TABLE IF NOT EXISTS `".$table_name_layers."` (
			`id` int(6) unsigned NOT NULL auto_increment,
			`name` varchar(255) CHARACTER SET utf8 NOT NULL,
			`basemap` varchar(20) NOT NULL,
			`layerzoom` int(2) NOT NULL,
			`mapwidth` int(4) NOT NULL,
			`mapwidthunit` varchar(2) NOT NULL,
			`mapheight` int(4) NOT NULL,
			`panel` tinyint(1) NOT NULL,
			`layerviewlat` decimal(9,6) NOT NULL,
			`layerviewlon` decimal(9,6) NOT NULL,
			`createdby` varchar(30) CHARACTER SET utf8 NOT NULL,
			`createdon` datetime NOT NULL,
			`updatedby` varchar(30) CHARACTER SET utf8 NOT NULL,
			`updatedon` datetime NOT NULL,
			`controlbox` int(1) NOT NULL,
			`overlays_custom` int(1) NOT NULL,
			`overlays_custom2` int(1) NOT NULL,
			`overlays_custom3` int(1) NOT NULL,
			`overlays_custom4` int(1) NOT NULL,
			`wms` int(1) NOT NULL,
			`wms2` int(1) NOT NULL,
			`wms3` int(1) NOT NULL,
			`wms4` int(1) NOT NULL,
			`wms5` int(1) NOT NULL,
			`wms6` int(1) NOT NULL,
			`wms7` int(1) NOT NULL,
			`wms8` int(1) NOT NULL,
			`wms9` int(1) NOT NULL,
			`wms10` int(1) NOT NULL,
			PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$wpdb->query($sql_create_layer_table);
		//info: insert layer row 0 for markers without assigned layer
		$sql = "SET SESSION sql_mode=NO_AUTO_VALUE_ON_ZERO;";
		$wpdb->query($sql);
		$sql2 = "INSERT INTO `".$table_name_layers."` ( `id`, `name`, `basemap`, `layerzoom`, `mapwidth`, `mapwidthunit`, `mapheight`, `layerviewlat`, `layerviewlon` ) VALUES (0, 'markers not assigned to a layer', 'osm_mapnik', '11', '640', 'px', '480', '', '');";
		$wpdb->query($sql2);
		$sql3 = "SET SESSION sql_mode='';";
		$wpdb->query($sql3);
		update_option('leafletmapsmarker_version', '1.0');
	} 
	if (get_option('leafletmapsmarker_version') == '1.0' ) {
		$save_defaults_for_new_options = new Leafletmapsmarker_options();
		$save_defaults_for_new_options->save_defaults_for_new_options();
		update_option('leafletmapsmarker_version', '1.1');
	}
	if (get_option('leafletmapsmarker_version') == '1.1' ) {
		$save_defaults_for_new_options = new Leafletmapsmarker_options();
		$save_defaults_for_new_options->save_defaults_for_new_options();
		update_option('leafletmapsmarker_version', '1.2');
	}
	if (get_option('leafletmapsmarker_version') == '1.2' ) {
		update_option('leafletmapsmarker_version', '1.2.1');
	}
	if (get_option('leafletmapsmarker_version') == '1.2.1' ) {
		update_option('leafletmapsmarker_version', '1.2.2');
	}
	if (get_option('leafletmapsmarker_version') == '1.2.2' ) {
		update_option('leafletmapsmarker_version', '1.3');
		//info: redirect to settings page only on first plugin activation, otherwise redirect is also done on bulk plugin activations
		if (get_option('leafletmapsmarker_redirect') == 'true') 
		{
			update_option('leafletmapsmarker_redirect', 'false');
			wp_redirect(WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings&display=install_note');
		}
	}
	/* template for plugin updates 
	if (get_option('leafletmapsmarker_version') == '1.3' ) {
		//mandatory if new options in class-leaflet-options.php were added
		$save_defaults_for_new_options = new Leafletmapsmarker_options();
		$save_defaults_for_new_options->save_defaults_for_new_options();
		//optional: add code for sql ddl updates
		//mandatory
		update_option('leafletmapsmarker_version', '1.4');
		//mandatory: move code for redirect-on-first-activation-check to here
	}
	*/
  }//info: end install_and_updates()
  function lmm_plugin_meta_links() {
	define( 'FB_BASENAME', plugin_basename( __FILE__ ) );
	define( 'FB_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
	define( 'FB_FILENAME', str_replace( FB_BASEFOLDER.'/', '', plugin_basename(__FILE__) ) );
	function leafletmapsmarker_filter_plugin_meta($links, $file) {
    	if ( $file == FB_BASENAME ) {
        	array_unshift(
	            $links,
    	        '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">'.__('Markers','lmm').'</a>',
        	    '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layers">'.__('Layers','lmm').'</a>' ,
            	'<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings">'.__('Settings','lmm').'</a>'
	        );
    	}
	    return $links;
	}
	add_filter( 'plugin_action_links', 'leafletmapsmarker_filter_plugin_meta', 10, 2 );
  } //info: end plugin_meta_links()
}  //info: end class
$run_leafletmapsmarker = new Leafletmapsmarker();
unset($run_leafletmapsmarker);
?>