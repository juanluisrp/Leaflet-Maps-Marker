<?php
/**
 * Leaflet Maps Marker Plugin - settings class
 * based on class by Alison Barrett, http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
 */
class Leafletmapsmarker_options {
	private $sections;
	private $checkboxes;
	private $settings;
	
	 /**
	 *
	 * Construct
	 *
	 */
	public function __construct() {
		//info:  This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();
		$this->sections['basemaps']      = 'Basemaps';
		$this->sections['overlays']      = 'Overlays';
		$this->sections['wms']      = 'WMS';
		$this->sections['defaults_marker']   = 'Marker defaults';
		$this->sections['defaults_layer']   = 'Layer defaults';
		$this->sections['google_places']   = 'Google Places';
		$this->sections['ar']   = 'Augmented-Reality';
		$this->sections['misc']   = 'Misc';
		$this->sections['reset']        = 'Reset to Defaults';
	/* info: localized tab texts break jQuery (jQuery UI Tabs: Mismatching fragment identifier) - no fix yet, help appreciated :-/
		$this->sections['basemaps']      = __( 'Basemaps', 'lmm' );
		$this->sections['overlays']      = __( 'Overlays', 'lmm' );
		$this->sections['wms']      = __( 'WMS', 'lmm' );
		$this->sections['defaults_marker']   = __( 'Marker defaults', 'lmm' );
		$this->sections['defaults_layer']   = __( 'Layer defaults', 'lmm' );
		$this->sections['google_places']   = __( 'Google Places', 'lmm' );
		$this->sections['ar']   = __( 'Augmented-Reality', 'lmm' );
		$this->sections['misc']   = __( 'Misc', 'lmm' );
		$this->sections['reset']        = __( 'Reset to Defaults', 'lmm' );
	*/
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		if ( ! get_option( 'leafletmapsmarker_options' ) )
			$this->initialize_settings();
	}
	
	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		
		$defaults = array(
			'id'      => 'default_field',
			'version' => '',
			'title'   => __( 'Default Field','lmm' ),
			'desc'    => __( 'This is a default description.','lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'basemaps',
			'choices' => array(),
			'class'   => ''
		);
			
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'leafletmapsmarker_settings', $section, $field_args );
	}
	
	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {
		echo '<div style="float:left;" class="icon32" id="icon-options-general"></div><h3>'.__('Settings','lmm').'</h3><div class="wrap lmmsettings">';
		$install_note = (isset($_GET['display']) ? $_GET['display'] : '');
		$settings_updated = isset($_GET['settings-updated']) ? $_GET['settings-updated'] : (isset($_GET['updated']) ? $_GET['updated'] : '');
		if ( ( $install_note != NULL ) && ( $settings_updated == NULL ) ) {
			$install_success_message = sprintf( __('You just successfully installed the "Leaflet Maps Marker" plugin. You can now optionally change the default settings below or <a href="%1$sadmin.php?page=leafletmapsmarker_marker">add your first marker</a>.<br/>For tutorials and help, please check the <a href="%1$sadmin.php?page=leafletmapsmarker_help">Help &amp; Credits page</a>!','lmm'), WP_ADMIN_URL); 
			echo '<div class="updated" style="padding:10px;"><p>' . $install_success_message . '<iframe src="http://www.mapsmarker.com/counter/go.php?id=plugin_installs" frameborder="0" height="0" width="0" name="counter" scrolling="no"></iframe></p></div>';
		//info: check if custom icons could be unzipped
		if ( ! file_exists(LEAFLET_PLUGIN_ICONS_DIR . DIRECTORY_SEPARATOR . 'information.png') ) {
				echo '<div class="error" style="padding:10px;">'.__('Warning: the custom map icon directory at <code>/wp-contents/uploads/leaflet-maps-marker-icons</code> could not be created due to file permission settings on your webserver. Leaflet Maps Marker will work as designed, but only with one map icon available.<br/>You can add the included map icons manually by following the steps at <a href="http://www.mapsmarker.com/incomplete-installation" target="_blank">http://www.mapsmarker.com/incomplete-installation</a>', 'lmm').'</div>';
			}
		}
	
		if ( isset( $_GET['settings-updated'] ) )
			echo '<div class="updated fade"><p>' . __( 'Plugin options updated.','lmm' ) . '</p></div>';
		include('leaflet-admin-header.php');
		echo '<form action="options.php" method="post">';
		settings_fields( 'leafletmapsmarker_options' );
		echo '<div class="ui-tabs">
			<ul class="ui-tabs-nav">';
		
		foreach ( $this->sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		
		echo '</ul>';
		do_settings_sections( $_GET['page'] );
		
		echo '</div>
		<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes','lmm' ) . '" /></p>
		
	</form>';
	
	echo '<script type="text/javascript">
		jQuery(document).ready(function($) {
			var sections = [];';
			
			foreach ( $this->sections as $section_slug => $section )
				echo "sections['$section'] = '$section_slug';";
			echo 'var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
			wrapped.each(function() {
				$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
			});
			$(".ui-tabs-panel").each(function(index) {
				$(this).attr("id", sections[$(this).children("h3").text()]);
				if (index > 0)
					$(this).addClass("ui-tabs-hide");
			});
			$(".ui-tabs").tabs({
				fx: { opacity: "toggle", duration: "fast" }
			});
			
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});
			
			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					//$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});
			
			$(".lmmsettings h3, .lmmsettings table, .leafletmapsmarker-listings").show();
			
			//info:  This will make the "warning" checkbox class really stand out when checked.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
			
			//info:  Browser compatibility
			if ($.browser.mozilla) 
			         $("form").attr("autocomplete", "off");
		});
	</script>
</div>';
		
	}
	
	/**
	 * Description for section
	 */
	public function display_section() {
		//Standard - nothing to add
	}
	
	/**
	 * Listing for basemaps section
	 */
	public function display_basemaps_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>Index</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li>' . __('Default basemap for new markers/layers','lmm') . '</li>
			<li>' . __('Names for default basemaps','lmm') . '</li>
			<li>' . __('Available basemaps in control box','lmm') . '</li>
			<li>' . __('OGD Vienna Selector','lmm') . '</li>
			<li>' . __('Custom basemap 1 settings','lmm') . '</li>
			<li>' . __('Custom basemap 2 settings','lmm') . '</li>
			<li>' . __('Custom basemap 3 settings','lmm') . '</li></ul></span>';
	}
	/**
	 * Listing for overlays section
	 */
	public function display_overlays_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>Index</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li>' . __('Available overlays for new markers/layers','lmm') . '</li>
			<li>' . __('Custom overlay settings','lmm') . '</li>
			<li>' . __('Custom overlay 2 settings','lmm') . '</li>			
			<li>' . __('Custom overlay 3 settings','lmm') . '</li>
			<li>' . __('Custom overlay 4 settings','lmm') . '</li></ul></span>';
	}	
	/**
	 * Listing for wms section
	 */
	public function display_wms_section() {
		
		echo '<span class="leafletmapsmarker-listings"><p>' . __( 'WMS stands for <a href="http://www.opengeospatial.org/standards/wms" target="_blank">Web Map Service</a> and is a standard protocol for serving georeferenced map images over the Internet that are generated by a map server using data from a GIS database.<br/>With Leaflet Maps Marker you can configure up to 10 WMS layers which can be enabled for each map. As default, 10 WMS layers from <a href="http://data.wien.gv.at" target="_blank">OGD Vienna</a> and from the <a href="http://www.eea.europa.eu/code/gis" target="_blank">European Environment Agency</a> have been predefined for you.<br/>A selection of further possible WMS layers can be found at <a href="http://www.mapsmarker.com/wms" target="_blank">http://www.mapsmarker.com/wms</a>', 'lmm') . '</p><p><strong>Index</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li>' . __('Available WMS layers for new markers/layers','lmm') . '</li>
			<li>' . __('WMS layer 1 settings','lmm') . '</li>
			<li>' . __('WMS layer 2 settings','lmm') . '</li>
			<li>' . __('WMS layer 3 settings','lmm') . '</li>
			<li>' . __('WMS layer 4 settings','lmm') . '</li>
			<li>' . __('WMS layer 5 settings','lmm') . '</li>
			<li>' . __('WMS layer 6 settings','lmm') . '</li>
			<li>' . __('WMS layer 7 settings','lmm') . '</li>
			<li>' . __('WMS layer 8 settings','lmm') . '</li>
			<li>' . __('WMS layer 9 settings','lmm') . '</li>
			<li>' . __('WMS layer 10 settings','lmm') . '</li></ul></span>';
	}
	/**
	 * Listing for marker defaults section
	 */
	public function display_defaults_marker_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>Index</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li>' . __('Default values for new markers','lmm') . '</li>
			<li>' . __('Default values for markers added directly','lmm') . '</li></ul></span>';
	}	
	/**
	 * Listing for google places section
	 */
	public function display_google_places_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>Index</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li>' . __('Google Places bounds','lmm') . '</li>
			<li>' . __('Google Places search prefix','lmm') . '</li></ul></span>';
	}	
	/**
	 * Listing for misc section
	 */
	public function display_misc_section() {
		echo '<span class="leafletmapsmarker-listings"><p><strong>Index</strong></p><ul style="list-style-type:disc;margin-left:24px;">
			<li>' . __('General settings','lmm') . '</li>
			<li>' . __('CRS (Coordinate Reference System)','lmm') . '</li>
			<li>' . __('Available columns for marker listing page','lmm') . '</li>
			<li>' . __('Available columns for layer listing page','lmm') . '</li></ul></span>';
	}	
	/**
	 * HTML output for text field
	 */
	public function display_setting( $args = array() ) {
		
		extract( $args );
		
		$options = get_option( 'leafletmapsmarker_options' );
		
		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
			
			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2" rowspan="2"><h4>' . $desc . '</h4>';
				break;
			case 'helptext':
				echo '</td></tr><tr valign="top"><td colspan="2">' . $desc . '';
				break;
			
			case 'checkbox':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
				break;
			case 'checkbox-readonly':
				
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' disabled="disabled" /> <label for="' . $id . '">' . $desc . '</label>';
				break;
			
			case 'select':
				echo '<select class="select' . $field_class . '" name="leafletmapsmarker_options[' . $id . ']">';
				
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="leafletmapsmarker_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" style="width:30em;" type="text" id="' . $id . '" name="leafletmapsmarker_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;
		}
	}
	
	/**
	 * Settings and defaults
	 */
	public function get_settings() {
		
		/*===========================================
		*
		*
		* section basemaps
		*
		*
		===========================================*/
		/*
		* Default basemap for new markers/layers
		*/
		$this->settings['default_basemap_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'Default basemap for new markers/layers', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['default_basemap_helptext1'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __( 'Please select the basemap which should be pre-selected as default for new markers and layers. Can be changed afterwards on each marker/layer.', 'lmm').'<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['standard_basemap'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Default basemap','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ogdwien_basemap',
			'choices' => array(
				'osm_mapnik' => __('OpenStreetMap (Mapnik, max zoom 18)','lmm'),
				'osm_osmarender' => __('OpenStreetMap (Osmarender, max zoom 17)','lmm'),
				'mapquest_osm' => __('MapQuest (OSM, max zoom 18)','lmm'),
				'mapquest_aerial' => __('MapQuest (Aerial, max zoom 12 globally, 12+ in the United States)','lmm'),
				'ogdwien_basemap' => __('OGD Vienna basemap (max zoom 19)','lmm'),
				'ogdwien_satellite' => __('OGD Vienna satellite (max zoom 19)','lmm'),
				'custom_basemap' => __('Custom basemap','lmm'),
				'custom_basemap2' => __('Custom basemap 2','lmm'),
				'custom_basemap3' => __('Custom basemap 3','lmm')
			)
		);
		/*
		* Names for default basemaps
		*/
		$this->settings['default_basemap_heading2'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'Names for default basemaps', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['default_basemap_helptext2'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Optionally you can also change the name of the predefined basemaps in the controlbox.', 'lmm').'<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap-names.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['default_basemap_name_osm_mapnik'] = array(
			'version' => '1.0',
			'title'   => 'OpenStreetMap (Mapnik)',
			'desc'    => '',
			'std'     => 'OSM Mapnik',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_osm_osmarender'] = array(
			'version' => '1.0',
			'title'   => 'OpenStreetMap (Osmarender)',
			'desc'    => '',
			'std'     => 'OSM Osmarender',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_mapquest_osm'] = array(
			'version' => '1.0',
			'title'   => 'Mapquest',
			'desc'    => '',
			'std'     => 'Mapquest OSM',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_mapquest_aerial'] = array(
			'version' => '1.0',
			'title'   => 'Mapquest (Aerial)',
			'desc'    => '',
			'std'     => 'Mapquest (Aerial)',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_ogdwien_basemap'] = array(
			'version' => '1.0',
			'title'   => 'OGD Vienna basemap',
			'desc'    => '',
			'std'     => 'OGD Vienna basemap',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['default_basemap_name_ogdwien_satellite'] = array(
			'version' => '1.0',
			'title'   => 'OGD Vienna satellite',
			'desc'    => '',
			'std'     => 'OGD Vienna satellite',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* Available basemaps in control box
		*/
		$this->settings['layer_controlbox_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'Available basemaps in control box', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['default_basemap_helptext3'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'    => '',
			'desc'    => __( 'Please select the basemaps which should be available in the control box.', 'lmm').'<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap-available-basemaps.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['controlbox_osm_mapnik'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __( 'Basemaps available in control box', 'lmm' ),
			'desc'    => __( 'OpenStreetMap (Mapnik)', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_osm_osmarender'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('OpenStreetMap (Osmarender)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_mapquest_osm'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('MapQuest (OSM)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_mapquest_aerial'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('MapQuest (Aerial)','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		
		$this->settings['controlbox_ogdwien_basemap'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('OGD Vienna basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_ogdwien_satellite'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('OGD Vienna satellite','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['controlbox_custom_basemap'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Custom basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_custom_basemap2'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Custom basemap 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['controlbox_custom_basemap3'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __('Custom basemap 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		/*
		* OGD Vienna Selector
		*/
		$this->settings['ogdvienna_selector_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'OGD Vienna Selector', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['ogdvienna_selector_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'If coordinates within boundaries of Vienna/Austria are selected for a marker or layer, the basemap automatically switches to OGD Vienna basemap and the overlay OGD Vienna addresses gets checked.', 'lmm').'<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap-ogdvienna-selector.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['ogdvienna_selector'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('OGD Vienna Selector','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'ogdwien_basemap',
			'choices' => array(
				'ogdwien_basemap' => __('enabled (use OGD Vienna basemap)','lmm'),
				'ogdwien_satellite' => __('enabled (use OGD Vienna satellite)','lmm'),
				'disabled' => __('disabled (use default basemap)','lmm')
			)
		);
		$this->settings['ogdvienna_selector_addresses'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '',
			'desc'    => __( 'enable OGD Vienna addresses overlay', 'lmm' ),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		/*
		* Custom basemap 1 settings
		*/
		$this->settings['custom_basemap_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'Custom basemap 1 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['custom_basemap_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom basemap', 'lmm').' (custom 1):<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap-custom-basemap1.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['custom_basemap_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom1',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		
		$this->settings['custom_basemap_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap_attribution'] = array(
			'version' => '1.0',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "Copyright ".date('Y')." <a href=&quot;http://xy.com&quot;>Provider X</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['custom_basemap_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;",
			'std'     => '&quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		/*
		* Custom basemap 2 settings
		*/
		$this->settings['custom_basemap2_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'Custom basemap 2 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['custom_basemap2_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom basemap', 'lmm').' (custom 2):<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap-custom-basemap2.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['custom_basemap2_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom2',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		
		$this->settings['custom_basemap2_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_attribution'] = array(
			'version' => '1.0',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider X&lt;/a&gt;",
			'std'     => "Copyright ".date('Y')." <a href=&quot;http://xy.com&quot;>Provider Y</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap2_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap2_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['custom_basemap2_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;",
			'std'     => '&quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		/*
		* Custom basemap 3 settings
		*/
		$this->settings['custom_basemap3_heading'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => '', 
			'desc'    => __( 'Custom basemap 3 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['custom_basemap3_helptext'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom basemap', 'lmm').' (custom 3):<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-default-basemap-custom-basemap3.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['custom_basemap3_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom3',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		
		$this->settings['custom_basemap3_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png",
			'std'     => 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_attribution'] = array(
			'version' => '1.0',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => __("For example","lmm"). ": Copyright ".date('Y')." &lt;a href=&quot;http://xy.com&quot;&gt;Provider XY&lt;/a&gt;",
			'std'     => "Copyright ".date('Y')." <a href=&quot;http://xy.com&quot;>Provider Z</a>",
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		$this->settings['custom_basemap3_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '18',
			'type'    => 'text',
			'section' => 'basemaps'
		);		
		$this->settings['custom_basemap3_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'basemaps',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['custom_basemap3_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;",
			'std'     => '&quot;otile1&quot;, &quot;otile2&quot;, &quot;otile3&quot;, &quot;otile4&quot;',
			'type'    => 'text',
			'section' => 'basemaps'
		);
		
		/*===========================================
		*
		*
		* section overlays
		*
		*
		===========================================*/
		/*
		* Available overlays for new markers/layers
		*/
		$this->settings['overlays_available_heading'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => __( 'Available overlays for new markers/layers', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['overlays_available_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the overlays which should be available in the control box.', 'lmm').'<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-custom-overlays-available-overlays.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'    => __('Available overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['overlays_custom2'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['overlays_custom3'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['overlays_custom4'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		/*
		* Custom overlay settings
		*/
		$this->settings['overlays_heading_custom'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => __( 'Custom overlay settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['overlays_custom_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').':<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-overlays-custom.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => __('OGD Vienna addresses','lmm'),
			'type'    => 'text',
			'section' => 'overlays'
		);		
		
		$this->settings['overlays_custom_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __('For example','lmm'). ": http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '19',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		/*
		* Custom overlay 2 settings
		*/
		$this->settings['overlays_heading_custom2'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => __( 'Custom overlay 2 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['overlays_custom2_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').' 2:<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-overlays-custom2.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom2_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom2',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		
		$this->settings['overlays_custom2_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __('For example','lmm'). ": http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://{s}.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom2_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);	
		$this->settings['overlays_custom2_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom2_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom2_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom2_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		/*
		* Custom overlay 3 settings
		*/
		$this->settings['overlays_heading_custom3'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => __( 'Custom overlay 3 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['overlays_custom3_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').' 3:<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-overlays-custom3.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom3_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom3',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom3_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom3_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);	
		$this->settings['overlays_custom3_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom3_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom3_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom3_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		/*
		* Custom overlay 4 settings
		*/
		$this->settings['overlays_heading_custom4'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => '', 
			'desc'    => __( 'Custom overlay 4 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['overlays_custom4_helptext'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please enter settings for custom overlay', 'lmm').' 4:<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-overlays-custom4.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['overlays_custom4_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Name', 'lmm' ),
			'desc'   => __( 'Will be displayed in controlbox if selected', 'lmm' ),
			'std'     => 'Custom4',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		
		$this->settings['overlays_custom4_tileurl'] = array(
			'version' => '1.0',
			'title'   => __( 'Tiles URL', 'lmm' ),
			'desc'    => __("For example","lmm"). ": http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png",
			'std'     => 'http://maps.wien.gv.at/wmts/beschriftung/normal/google3857/{z}/{y}/{x}.png',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom4_attribution'] = array(
			'version' => '1.1',
			'title'   => __( 'Attribution', 'lmm' ),
			'desc'    => '',
			'std'     => 'Addresses: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>data.wien.gv.at</a>)',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom4_minzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Minimum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '1',
			'type'    => 'text',
			'section' => 'overlays'
		);
		$this->settings['overlays_custom4_maxzoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum zoom level', 'lmm' ),
			'desc'    => __('Note: maximum zoom level may vary on your basemap','lmm'),
			'std'     => '17',
			'type'    => 'text',
			'section' => 'overlays'
		);		
		$this->settings['overlays_custom4_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'overlays',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from tiles url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes (please enter subdomains in next form field)','lmm'),
				'no' => __('No','lmm')
			)
		);
		$this->settings['overlays_custom4_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;",
			'std'     => '&quot;maps&quot;, &quot;maps1&quot;, &quot;maps2&quot;, &quot;maps3&quot;',
			'type'    => 'text',
			'section' => 'overlays'
		);
		
		/*===========================================
		*
		*
		* section wms
		*
		*
		===========================================*/
		/*
		* Available WMS layers for new markers/layers
		*/
		$this->settings['wms_available_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'Available WMS layers for new markers/layers', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_available_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the WMS layers which should be available when creating new markers/layers', 'lmm').'<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-wms-available-wms-layers.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['wms_wms_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Available WMS layers','lmm'),
			'desc'    => 'WMS 1',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms2_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 2',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms3_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 3',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms4_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 4',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms5_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 5',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms6_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 6',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms7_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 7',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms8_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 8',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms9_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 9',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['wms_wms10_available'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => '',
			'desc'    => 'WMS 10',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		/*
		* WMS layer settings
		*/
		$this->settings['wms_wms_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 1 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://data.wien.gv.at/katalog/wc-anlagen.html&quot; target=&quot;_blank&quot;>OGD Vienna - Public Toilets</a>' 
		);
		$this->settings['wms_wms_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://data.wien.gv.at/daten/wms' 
		);
		$this->settings['wms_wms_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => 'OEFFWCOGD' 
		);
		$this->settings['wms_wms_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/gif' 
		);		
		$this->settings['wms_wms_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.1.1' 
		);
		$this->settings['wms_wms_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>http://data.wien.gv.at</a>)' 
		);		
		$this->settings['wms_wms_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);		
		$this->settings['wms_wms_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		
		/*
		* WMS layer 2 settings
		*/
		$this->settings['wms_wms2_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 2 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms2_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms2_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://data.wien.gv.at/katalog/aufzuege.html&quot; target=&quot;_blank&quot;>OGD Vienna - Elevators at stations</a>' 
		);
		$this->settings['wms_wms2_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://data.wien.gv.at/daten/wms' 
		);
		$this->settings['wms_wms2_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => 'AUFZUGOGD' 
		);
		$this->settings['wms_wms2_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms2_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/gif' 
		);		
		$this->settings['wms_wms2_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms2_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.1.1' 
		);	
		$this->settings['wms_wms2_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: City of Vienna (<a href=&quot;http://data.wien.gv.at&quot; target=&quot;_blank&quot;>http://data.wien.gv.at</a>)' 
		);		
		$this->settings['wms_wms2_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms2_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);		
		$this->settings['wms_wms2_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms2_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		/*
		* WMS layer 3 settings
		*/
		$this->settings['wms_wms3_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 3 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms3_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms3_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/7&quot; target=&quot;_blank&quot;>EEA - CO emissions from road transport</a>' 
		);
		$this->settings['wms_wms3_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/WMSServer' 
		);
		$this->settings['wms_wms3_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '24' 
		);
		$this->settings['wms_wms3_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms3_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms3_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms3_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms3_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms3_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Air/EPRTRDiffuseAir_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=1' 
		);		
		$this->settings['wms_wms3_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);		
		$this->settings['wms_wms3_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms3_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
	
		/*
		* WMS layer 4 settings
		*/
		$this->settings['wms_wms4_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 4 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms4_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms4_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Agricultural areas</a>' 
		);
		$this->settings['wms_wms4_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer' 
		);
		$this->settings['wms_wms4_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '10' 
		);
		$this->settings['wms_wms4_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms4_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms4_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms4_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);
		$this->settings['wms_wms4_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms4_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms4_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2000_Cach_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=11'			
		);		
		$this->settings['wms_wms4_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms4_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
	
		/*
		* WMS layer 5 settings
		*/
		$this->settings['wms_wms5_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 5 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms5_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms5_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer/460&quot; target=&quot;_blank&quot;>EEA - Airport Annual Traffic</a>' 
		);
		$this->settings['wms_wms5_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer' 
		);
		$this->settings['wms_wms5_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '8' 
		);
		$this->settings['wms_wms5_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms5_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms5_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms5_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms5_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms5_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms5_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=8'			
		);		
		$this->settings['wms_wms5_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms5_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
				
		/*
		* WMS layer 6 settings
		*/
		$this->settings['wms_wms6_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 6 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms6_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms6_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Land/CLC2006_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - WaterBodies</a>' 
		);
		$this->settings['wms_wms6_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer' 
		);
		$this->settings['wms_wms6_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '2' 
		);
		$this->settings['wms_wms6_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms6_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms6_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms6_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms6_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms6_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms6_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Land/CLC2006_Dyna_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=2'			
		);		
		$this->settings['wms_wms6_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms6_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		/*
		* WMS layer 7 settings
		*/
		$this->settings['wms_wms7_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 7 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms7_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms7_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Water/RiverAndLakes_Dyna_WM/MapServer&quot; target=&quot;_blank&quot;>EEA - Mean annual nitrates in rivers 2008</a>' 
		);
		$this->settings['wms_wms7_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Water/RiverAndLakes_Dyna_WM/MapServer/WMSServer' 
		);
		$this->settings['wms_wms7_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '14' 
		);
		$this->settings['wms_wms7_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms7_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms7_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms7_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms7_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms7_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms7_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Water/RiverAndLakes_Dyna_WM/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=14'			
		);		
		$this->settings['wms_wms7_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms7_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		/*
		* WMS layer 8 settings
		*/
		$this->settings['wms_wms8_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 8 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms8_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms8_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Reports2010/Reports2008_Dyna_WGS84/MapServer&quot; target=&quot;_blank&quot;>EEA - Temperature Change</a>' 
		);
		$this->settings['wms_wms8_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/WMSServer' 
		);
		$this->settings['wms_wms8_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '5' 
		);
		$this->settings['wms_wms8_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms8_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms8_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms8_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms8_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms8_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms8_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://cow6/ArcGIS/services/Reports2010/Reports2008_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=5'			
		);		
		$this->settings['wms_wms8_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms8_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		/*
		* WMS layer 9 settings
		*/
		$this->settings['wms_wms9_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 9 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms9_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms9_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Bio/CDDA_Dyna_WGS84/MapServer&quot; target=&quot;_blank&quot;>EEA - Common Database on Designated Areas</a>' 
		);
		$this->settings['wms_wms9_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Bio/CDDA_Dyna_WGS84/MapServer/WMSServer' 
		);
		$this->settings['wms_wms9_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '0' 
		);
		$this->settings['wms_wms9_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms9_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms9_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms9_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms9_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms9_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms9_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Bio/CDDA_Dyna_WGS84/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=0'			
		);		
		$this->settings['wms_wms9_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms9_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);	
		/*
		* WMS layer 10 settings
		*/
		$this->settings['wms_wms10_heading'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => '', 
			'desc'    => __( 'WMS layer 10 settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['wms_wms10_helptext'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['wms_wms10_name'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Name','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => '<a href=&quot;http://discomap.eea.europa.eu/ArcGIS/rest/services/Noise/Noise_Dyna_LAEA/MapServer&quot; target=&quot;_blank&quot;>EEA - Road noise Austria</a>' 
		);
		$this->settings['wms_wms10_baseurl'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('baseURL','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer' 
		);
		$this->settings['wms_wms10_layers'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Layers','lmm'),
			'desc'    => __('(required) Comma-separated list of WMS layers to show','lmm'),
			'type'    => 'text',
			'std'     => '247' 
		);
		$this->settings['wms_wms10_styles'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Styles','lmm'),
			'desc'    => __('Comma-separated list of WMS styles','lmm'),
			'type'    => 'text',
			'std'     => '' 
		);
		$this->settings['wms_wms10_format'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Format','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'image/png' 
		);		
		$this->settings['wms_wms10_transparent'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Transparent','lmm'),
			'desc'    => __('If yes, the WMS service will return images with transparency','lmm'),
			'type'    => 'radio',
			'std'     => 'TRUE',
			'choices' => array(
				'TRUE' => 'TRUE',
				'FALSE' => 'FALSE'
			)
		);
		$this->settings['wms_wms10_version'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Version','lmm'),
			'desc'    => __('Version of the WMS service to use','lmm'),
			'type'    => 'text',
			'std'     => '1.3.0' 
		);	
		$this->settings['wms_wms10_attribution'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Attribution','lmm'),
			'desc'    => '',
			'type'    => 'text',
			'std'     => 'WMS: <a href=&quot;http://www.eea.europa.eu/code/gis&quot; target=&quot;_blank&quot;>European Environment Agency</a>' 
		);		
		$this->settings['wms_wms10_legend_enabled'] = array(
			'version' => '1.1',
			'section' => 'wms',
			'title'   => __('Display legend?','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'yes',
			'choices' => array(
				'yes' => __('Yes','lmm'),
				'no' => __('No','lmm')
			)
		);		
		$this->settings['wms_wms10_legend'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'    => __('Legend','lmm'),
			'desc'    => __('URL of image which gets show when hovering the text "(Legend)" next to WMS attribution text','lmm'),
			'type'    => 'text',
			'std'     => 'http://discomap.eea.europa.eu/ArcGIS/services/Noise/Noise_Dyna_LAEA/MapServer/WMSServer?request=GetLegendGraphic%26version=1.3.0%26format=image/png%26layer=247'			
		);		
		$this->settings['wms_wms10_subdomains_enabled'] = array(
			'version' => '1.0',
			'section' => 'wms',
			'title'   => __('Support for subdomains?','lmm'),
			'desc'    => __('Will replace {s} from base url if available','lmm'),
			'type'    => 'radio',
			'std'     => 'no',
			'choices' => array(
				'no' => __('No','lmm'),
				'yes' => __('Yes (please enter subdomains in next form field)','lmm')
			)
		);
		$this->settings['wms_wms10_subdomains_names'] = array(
			'version' => '1.0',
			'title'   => __( 'Subdomain names', 'lmm' ),
			'desc'    => __('For example','lmm'). ": &quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;",
			'std'     => '&quot;subdomain1&quot;, &quot;subdomain2&quot;, &quot;subdomain3&quot;',
			'type'    => 'text',
			'section' => 'wms'
		);											
				
		/*===========================================
		*
		*
		* section marker defaults
		*
		*
		===========================================*/	
		/*
		* Default values for new markers
		*/
		$this->settings['defaults_marker_heading'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '', 
			'desc'    => __( 'Default values for new markers', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['defaults_marker_helptext1'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Will be used when creating a new marker. All values can be changed afterwards on each marker.', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-marker-defaults.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_lat'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.216038',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_lon'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.378984',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_zoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_mapwidth'] = array(
			'version' => '1.0',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_mapwidthunit'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->settings['defaults_marker_mapheight'] = array(
			'version' => '1.0',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_openpopup'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Open popup by default','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => 'disabled',
				'1' => 'enabled'
			)
		);
		$this->settings['defaults_marker_controlbox'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Basemap/layer controlbox on frontend','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed (except on mobiles)','lmm'),
				'2' => __('expanded','lmm')
			)
		);		
		// defaults_marker - which overlays are active by default?
		$this->settings['defaults_marker_overlays_custom_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_overlays_custom2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['defaults_marker_overlays_custom3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_overlays_custom4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_panel'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Panel for displaying marker name and API URLs on top of map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('show','lmm'),
				'0' => __('hide','lmm'),
			)
		);	
		// defaults_marker - active API links in panel
		$this->settings['defaults_marker_panel_kml'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Visible API links in panel','lmm'),
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'defaults_marker',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_marker_panel_qr_code'] = array(
			'version' => '1.1',
			'section' => 'defaults_marker',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_marker_panel_geojson'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_georss'] = array(
			'version' => '1.2',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_marker_panel_wikitude'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_marker_panel_background_color'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel background color', 'lmm' ),
			'desc'    => 'Please use hexidecimal color values',
			'std'     => '#efefef',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);		
		$this->settings['defaults_marker_panel_paneltext_css'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel text css', 'lmm' ),
			'desc'    => '',
			'std'     => 'font-weight:bold;',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		// defaults_marker - which WMS layers are active by default?
		$this->settings['defaults_marker_wms_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_wms2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_wms3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms5_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms6_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms7_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms8_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms9_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_wms10_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
	
		/*
		* Default values for markers added directly
		*/
		$this->settings['defaults_marker_shortcode_heading'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '', 
			'desc'    => __( 'Default values for markers added directly', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['defaults_marker_shortcode_helptext'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'You can also add markers directly to posts or pages without having to save them to your database previously. You just have to use the shortcode with the attributes mlat and mlon (e.g. <strong>[mapsmarker mlat="48.216038" mlon="16.378984"]</strong>).', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-marker-direct.jpg /><br/><br/>' . __('Defaults values for markers added directly:','lmm'),
			'type'    => 'helptext'
		);
		$this->settings['defaults_marker_shortcode_basemap'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Default basemap','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'osm_mapnik',
			'choices' => array(
				'osm_mapnik' => __('OpenStreetMap (Mapnik, max zoom 18)','lmm'),
				'osm_osmarender' => __('OpenStreetMap (Osmarender, max zoom 17)','lmm'),
				'mapquest_osm' => __('MapQuest (OSM, max zoom 18)','lmm'),
				'mapquest_aerial' => __('MapQuest (Aerial, max zoom 12 globally, 12+ in the United States)','lmm'),
				'ogdwien_basemap' => __('OGD Vienna basemap (max zoom 19)','lmm'),
				'ogdwien_satellite' => __('OGD Vienna satellite (max zoom 19)','lmm'),
				'custom_basemap' => __('Custom basemap','lmm'),
				'custom_basemap2' => __('Custom basemap 2','lmm'),
				'custom_basemap3' => __('Custom basemap 3','lmm')
			)
		);
		$this->settings['defaults_marker_shortcode_zoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_shortcode_mapwidth'] = array(
			'version' => '1.0',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_shortcode_mapwidthunit'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->settings['defaults_marker_shortcode_mapheight'] = array(
			'version' => '1.0',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text',
			'section' => 'defaults_marker'
		);
		$this->settings['defaults_marker_shortcode_controlbox'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => __('Basemap/layer controlbox on frontend','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed (except on mobiles)','lmm'),
				'2' => __('expanded','lmm')
			)
		);		
		// defaults_marker - which overlays are active by default?
		$this->settings['defaults_marker_shortcode_overlays_custom_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_shortcode_overlays_custom2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['defaults_marker_shortcode_overlays_custom3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		
		$this->settings['defaults_marker_shortcode_overlays_custom4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		// defaults_marker shortcode - which WMS layers are active by default?
		$this->settings['defaults_marker_shortcode_wms_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_marker_shortcode_wms2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_marker_shortcode_wms3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms5_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms6_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms7_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms8_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms9_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_marker_shortcode_wms10_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_marker',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		
		/*===========================================
		*
		*
		* section layer defaults
		*
		*
		===========================================*/		
		/*
		* Default values for new layers
		*/
		$this->settings['defaults_layer_heading'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '', 
			'desc'    => __( 'Default values for new layers', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['defaults_layer_helptext1'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Will be used when creating a new layer. All values can be changed afterwards on each layer.', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-layer-defaults.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['defaults_layer_lat'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.216038',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_lon'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ),
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.378984',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_zoom'] = array(
			'version' => '1.0',
			'title'   => __( 'Zoom', 'lmm' ),
			'desc'    => '',
			'std'     => '11',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_mapwidth'] = array(
			'version' => '1.0',
			'title'   => __( 'Map width', 'lmm' ),
			'desc'    => '',
			'std'     => '640',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_mapwidthunit'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => __('Map width unit','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'px',
			'choices' => array(
				'px' => 'px',
				'%' => '%'
			)
		);
		$this->settings['defaults_layer_mapheight'] = array(
			'version' => '1.0',
			'title'   => __( 'Map height', 'lmm' ) . ' (px)',
			'desc'    => '',
			'std'     => '480',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);
		$this->settings['defaults_layer_controlbox'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => __('Basemap/layer controlbox on frontend','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'0' => __('hidden','lmm'),
				'1' => __('collapsed (except on mobiles)','lmm'),
				'2' => __('expanded','lmm')
			)
		);		
		// defaults_layer - which overlays are active by default?
		$this->settings['defaults_layer_overlays_custom_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'    => __('Checked overlays in control box','lmm'),
			'desc'    => __('Custom overlay','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_layer_overlays_custom2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('Custom overlay 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_overlays_custom3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('Custom overlay 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_overlays_custom4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('Custom overlay 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_panel'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => __('Panel for displaying layer name and API URLs on top of map','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array(
				'1' => __('show','lmm'),
				'0' => __('hide','lmm'),
			)
		);	
		// defaults_layer - active API links in panel
		$this->settings['defaults_layer_panel_kml'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'    => __('Visible API links in panel','lmm'),
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_panel_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_panel_qr_code'] = array(
			'version' => '1.1',
			'section' => 'defaults_layer',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_panel_geojson'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_panel_georss'] = array(
			'version' => '1.2',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 1 
		);
		$this->settings['defaults_layer_panel_wikitude'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['defaults_layer_panel_background_color'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel background color', 'lmm' ),
			'desc'    => 'Please use hexidecimal color values',
			'std'     => '#efefef',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);		
		$this->settings['defaults_layer_panel_paneltext_css'] = array(
			'version' => '1.0',
			'title'   => __( 'Panel text css', 'lmm' ),
			'desc'    => '',
			'std'     => 'font-weight:bold;',
			'type'    => 'text',
			'section' => 'defaults_layer'
		);		
		// defaults_layer - which WMS layers are active by default?
		$this->settings['defaults_layer_wms_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'    => __('Checked WMS layers','lmm'),
			'desc'    => __('WMS 1','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['defaults_layer_wms2_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 2','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);		
		$this->settings['defaults_layer_wms3_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 3','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms4_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 4','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms5_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 5','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms6_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 6','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms7_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 7','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms8_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 8','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms9_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 9','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['defaults_layer_wms10_active'] = array(
			'version' => '1.0',
			'section' => 'defaults_layer',
			'title'   => '',
			'desc'    => __('WMS 10','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
			
		/*===========================================
		*
		*
		* section Google Places
		*
		*
		===========================================*/	
		/*
		* Google Places Bounds
		*/
		$this->settings['google_places_bounds_helptext1'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Leaflet Maps Marker uses the <a href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank">Google Places Autocomplete API</a> to easily find coordinates for places or addresses. This feature is enabled by default. Preview:', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-google-places-preview.png />',
			'type'    => 'helptext'
		);
		$this->settings['google_places_bounds_heading'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'title'   => '', 
			'desc'    => __( 'Google Places bounds', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['google_places_bounds_helptext2'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'You can get better search results if you enable the bounds feature. This allows you to specify the area in which to primarily search for places or addresses. Please note: the results are biased towards, but not restricted to places or addresses contained within these bounds.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['google_places_bounds_status'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'title'   => __('Google Places bounds','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'enabled' => __('enabled','lmm')
			)
		);
		$this->settings['google_places_bounds_helptext3'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'If enabled, please enter longitude and latitude values below for the corner points of the prefered search area. Below you find an example for Vienna/Austria:', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-google-places-bounds.jpg />',
			'type'    => 'helptext'
		);
		$this->settings['google_places_bounds_lat1'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ) . ' 1',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.326583',
			'type'    => 'text',
			'section' => 'google_places'
		);
		$this->settings['google_places_bounds_lon1'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ) . ' 1',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.55056',
			'type'    => 'text',
			'section' => 'google_places'
		);
		$this->settings['google_places_bounds_lat2'] = array(
			'version' => '1.0',
			'title'   => __( 'Latitude', 'lmm' ) . ' 2',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '48.114308',
			'type'    => 'text',
			'section' => 'google_places'
		);
		$this->settings['google_places_bounds_lon2'] = array(
			'version' => '1.0',
			'title'   => __( 'Longitude', 'lmm' ) . ' 2',
			'desc'    => __( 'Please use a dot instead of a coma as decimal delimiter!', 'lmm' ),
			'std'     => '16.187325',
			'type'    => 'text',
			'section' => 'google_places'
		);		
		$this->settings['google_places_search_prefix_heading'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'title'   => '', 
			'desc'    => __( 'Google Places search prefix', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['google_places_search_prefix_helptext1'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'You can also select a search prefix, which automatically gets added to search form when creating a new marker or layer.', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-google-places-prefix.png />',
			'type'    => 'helptext'
		);
		$this->settings['google_places_search_prefix_status'] = array(
			'version' => '1.0',
			'section' => 'google_places',
			'title'   => __('Google Places search prefix','lmm'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'disabled',
			'choices' => array(
				'disabled' => __('disabled','lmm'),
				'enabled' => __('enabled','lmm')
			)
		);
		/*
		* Google Places Search Prefix
		*/
		$this->settings['google_places_search_prefix'] = array(
			'version' => '1.0',
			'title'   => __( 'Prefix to use', 'lmm' ),
			'desc'    => '',
			'std'     => 'Wien, ',
			'type'    => 'text',
			'section' => 'google_places'
		);	
		/*===========================================
		*
		*
		* section Augmented-Reality
		*
		*
		===========================================*/	
		/*
		* AR General
		*/
		$this->settings['ar_general_helptext1'] = array(
			'version' => '1.0',
			'section' => 'ar',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Markers created with Leaflet Maps Marker can also be displayed via <a href="http://en.wikipedia.org/wiki/Augmented_reality" target="_blank">Augmented-Reality technology</a> on mobile devices. As a first steps, an API to <a href="http://www.wikitude.com" target="_blank">Wikitude</a> has been implemented. APIs to other Augmented-Reality-Providers (like <a href="http://www.layar.com" target="_blank">Layar</a> or <a href="http://www.junaio.de" target="_blank">Junaio</a>) will probably follow in one of the next versions. Sample screenshots:', 'lmm') . '<br/><br/><img src='. LEAFLET_PLUGIN_URL .'/img/help-augmented-reality-samples.jpg />',
			'type'    => 'helptext'
		);
		/*
		* AR Wikitude
		*/
		$this->settings['ar_wikitude_heading'] = array(
			'version' => '1.0',
			'section' => 'ar',
			'title'   => '', 
			'desc'    => __( 'Wikitude settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['ar_wikitude_helptext'] = array(
			'version' => '1.0',
			'section' => 'ar',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please visit <a href="http://www.mapsmarker.com/wikitude" target="_blank">http://www.mapsmarker.com/wikitude</a> for instructions how to submit your marker or layer maps to Wikitude.', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['ar_wikitude_provider_name'] = array(
			'version' => '1.0',
			'title'   => __( 'Provider name', 'lmm' ),
			'desc'    => '<strong>' . __( 'Identifies the content provider or content channel, no spaces/special characters', 'lmm' ) . '</strong>',
			'std'     => 'www_mapsmarker_com',
			'type'    => 'text',
			'section' => 'ar'
		);
		$this->settings['ar_wikitude_provider_url'] = array(
			'version' => '1.0',
			'title'   => __( 'Provider URL', 'lmm' ),
			'desc'    => __( 'Link to content provider', 'lmm' ),
			'std'     => 'http://www.mapsmarker.com',
			'type'    => 'text',
			'section' => 'ar'
		);
		$this->settings['ar_wikitude_logo'] = array(
			'version' => '1.0',
			'title'   => __( 'Logo', 'lmm' ),
			'desc'    => __( 'The logo is displayed on the left bottom corner on Wikitude when an icon is selected - 96x96 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'img/wikitude-logo-96x96.png',
			'type'    => 'text',
			'section' => 'ar'
		);
		$this->settings['ar_wikitude_icon'] = array(
			'version' => '1.0',
			'title'   => __( 'Icon', 'lmm' ),
			'desc'    => __( 'The icon is displayed in the cam view of Wikitude to indicate a marker - 32x32 pixel, transparent PNG', 'lmm' ),
			'std'     => LEAFLET_PLUGIN_URL . 'img/wikitude-icon-32x32.png',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_email'] = array(
			'version' => '1.0',
			'title'   => __( 'E-Mail', 'lmm' ),
			'desc'    => __( 'Optional: displayed on each marker; used for sending an email directly from Wikitude', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_phone'] = array(
			'version' => '1.0',
			'title'   => __( 'Phone', 'lmm' ),
			'desc'    => __( 'Optional: example: +4312345 - when a phone number is given, Wikitude displays a "call me" button in the bubble; used for every marker.', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_attachment'] = array(
			'version' => '1.0',
			'title'   => __( 'Attachment', 'lmm' ),
			'desc'    => __( 'Optional: displayed on each marker; can be a link to a resource (image, PDF file...). You could use this to issue coupons or vouchers for potential clients that found you via Wikitude.', 'lmm' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_radius'] = array(
			'version' => '1.0',
			'title'   => __( 'Search radius (in meter)', 'lmm' ),
			'desc'    => __( 'Retrieve POIs (Points of Interests) from database within this search radius in meters from the current location of the Wikitude user', 'lmm' ),
			'std'     => '100000',
			'type'    => 'text',
			'section' => 'ar'
		);		
		$this->settings['ar_wikitude_maxnumberpois'] = array(
			'version' => '1.0',
			'title'   => __( 'Maximum number of POIs', 'lmm' ),
			'desc'    => __( 'Used if Wikitude does not pass the variable maxNumberofPois - 50 is the maximum recommended', 'lmm' ),
			'std'     => '50',
			'type'    => 'text',
			'section' => 'ar'
		);		
		
		/*===========================================
		*
		*
		* section miscellaneous
		*
		*
		===========================================*/
		$this->settings['misc_general_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => __( 'General settings', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['misc_general_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => '', //empty for not breaking settings layout
			'type'    => 'helptext'
		);
		$this->settings['capabilities_edit'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __( 'User role needed for adding and editing markers/layers', 'lmm' ),
			'desc'    => __( 'Note: the settings page is always visible to admins only.', 'lmm' ),
			'type'    => 'radio',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')				
			)
		);
		$this->settings['capabilities_delete'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __( 'User role needed for deleting markers/layers', 'lmm' ),
			'desc'    => __( 'Note: the settings page is always visible to admins only.', 'lmm' ),
			'type'    => 'radio',
			'std'     => 'edit_posts',
			'choices' => array(
				'activate_plugins' => __('Administrator (Capability activate_plugins)', 'lmm'),
				'moderate_comments' => __('Editor (Capability moderate_comments)', 'lmm'),
				'edit_published_posts' => __('Author (Capability edit_published_posts)', 'lmm'),
				'edit_posts' => __('Contributor (Capability edit_posts)', 'lmm'),
				'read' => __('Subscriber (Capability read)', 'lmm')		
			)
		);
		$this->settings['markers_per_page'] = array(
			'version' => '1.0',
			'title'   => __( 'Markers per page', 'lmm' ),
			'desc'    => __( 'How many markers should be listed on one page at the page "list all markers"?', 'lmm' ),
			'std'     => '30',
			'type'    => 'text',
			'section' => 'misc'
		);
		$this->settings['shortcode'] = array(
			'version' => '1.0',
			'title'   => __( 'Shortcode', 'lmm' ),
			'desc'    => __( 'Shortcode to add markers or layers into articles or pages  - Example: [mapsmarker marker="1"].<br/> Attention: if you change the shortcode after having embedded shortcodes into posts/Pages, the shortcode on these specific articles/pages has to be changed also manually - otherwise these markers/layers will not be show on frontend!', 'lmm' ),
			'std'     => 'mapsmarker',
			'type'    => 'text',
			'section' => 'misc'
		);
		$this->settings['admin_bar_integration'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __('Wordpress Admin Bar integration','lmm'),
			'desc'    => __('show or hide drop down menu for Leaflet Maps Marker in Wordpress Admin Bar','lmm'),
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);
		$this->settings['misc_global_stats'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'   => __('Global statistics','lmm'),
			'desc'    => __('Anonymous marker/layer hit counter','lmm') . ' <a href="http://www.mapsmarker.com/legal#global-stats" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-question-mark.png" width="12" height="12" border="0"/></a>',
			'type'    => 'radio',
			'std'     => 'enabled',
			'choices' => array(
				'enabled' => __('enabled','lmm'),
				'disabled' => __('disabled','lmm')
			)
		);		
		$this->settings['misc_qrcode_size'] = array(
			'version' => '1.1',
			'title'   => __( 'QR code image size', 'lmm' ),
			'desc'    => __( 'Width and height in pixel of QR code image for marker/layer standalone fullscreen map links', 'lmm' ),
			'std'     => '150',
			'type'    => 'text',
			'section' => 'misc'
		);
		/*
		* Projections / CRS - Coordinate Reference System
		*/
		$this->settings['misc_projections_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => __( 'CRS (Coordinate Reference System)', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['misc_projections_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Used for created maps - do not change this if you are not sure what it means!', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_projections'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => __( 'Coordinate Reference System', 'lmm' ),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'L.CRS.EPSG3857',
			'choices' => array(
				'L.CRS.EPSG3857' => __('EPSG:3857 (Spherical Mercator), used by most of commercial map providers (CloudMade, Google, Yahoo, Bing, etc.)', 'lmm'),
				'L.CRS.EPSG4326' => __('EPSG:4326 (Plate Carree), very popular among GIS enthusiasts', 'lmm'),
				'L.CRS.EPSG3395' => __('EPSG:4326 (Mercator), used by some map providers.', 'lmm')
			)
		);
		/*
		* Available columns for marker listing page
		*/
		$this->settings['misc_marker_listing_columns_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => __( 'Available columns for marker listing page', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['misc_marker_listing_columns_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the columns which should be available on the page "List all markers"', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_marker_listing_columns_id'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => __('Columns to show','lmm'),
			'desc'    => 'ID',
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_marker_listing_columns_icon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Icon','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_marker_listing_columns_markername'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Marker name','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_marker_listing_columns_popuptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Popup text','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);		
		$this->settings['misc_marker_listing_columns_basemap'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_layer'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_coordinates'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Coordinates','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_zoom'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Zoom','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_openpopup'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Popup status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_mapsize'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Map size','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_createdby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_createdon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_updatedby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_updatedon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_marker_listing_columns_controlbox'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Controlbox status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['misc_marker_listing_columns_shortcode'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Shortcode','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_kml'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_qr_code'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_geojson'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_georss'] = array(
			'version' => '1.2',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_marker_listing_columns_wikitude'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 1 
		);			
		/*
		* Available columns for layer listing page
		*/
		$this->settings['misc_layer_listing_columns_heading'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'   => '', 
			'desc'    => __( 'Available columns for layer listing page', 'lmm'),
			'type'    => 'heading'
		);
		$this->settings['misc_layer_listing_columns_helptext'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'std'     => '', 
			'title'   => '',
			'desc'    => __( 'Please select the columns which should be available on the page "List all layers"', 'lmm'),
			'type'    => 'helptext'
		);
		$this->settings['misc_layer_listing_columns_id'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => __('Columns to show','lmm'),
			'desc'    => 'ID',
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_layer_listing_columns_layername'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer name','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);
		$this->settings['misc_layer_listing_columns_markercount'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Number of markers','lmm'),
			'type'    => 'checkbox-readonly',
			'std'     => 1 
		);		
		$this->settings['misc_layer_listing_columns_basemap'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Basemap','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_layercenter'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Layer center','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_zoom'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Zoom','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_mapsize'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Map size','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_createdby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_createdon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Created on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_updatedby'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated by','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_updatedon'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Updated on','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);	
		$this->settings['misc_layer_listing_columns_controlbox'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Controlbox status','lmm'),
			'type'    => 'checkbox',
			'std'     => 0 
		);
		$this->settings['misc_layer_listing_columns_shortcode'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Shortcode','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_kml'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'KML',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_fullscreen'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('Fullscreen','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_qr_code'] = array(
			'version' => '1.1',
			'section' => 'misc',
			'title'    => '',
			'desc'    => __('QR code','lmm'),
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_geojson'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoJSON',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_georss'] = array(
			'version' => '1.2',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'GeoRSS',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
		$this->settings['misc_layer_listing_columns_wikitude'] = array(
			'version' => '1.0',
			'section' => 'misc',
			'title'    => '',
			'desc'    => 'Wikitude',
			'type'    => 'checkbox',
			'std'     => 1 
		);	
				
		/*===========================================
		*
		*
		* section reset
		*
		*
		===========================================*/
		$this->settings['reset_settings'] = array(
			'version' => '1.0',
			'section' => 'reset',
			'title'   => __( 'Reset Settings','lmm' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset plugin options to their defaults.','lmm' )
		);
	}
	
	/**
	 * Initialize settings to their default values
	 */ 
	public function initialize_settings() {
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' ) {
				$default_settings[$id] = $setting['std'];
				}
		}
		update_option( 'leafletmapsmarker_options', $default_settings );
	}
	/**
	* Register settings
	*/
	public function register_settings() {
		
		register_setting( 'leafletmapsmarker_options', 'leafletmapsmarker_options', array ( &$this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'basemaps' )
				add_settings_section( $slug, $title, array( &$this, 'display_basemaps_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'overlays' )
				add_settings_section( $slug, $title, array( &$this, 'display_overlays_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'wms' )
				add_settings_section( $slug, $title, array( &$this, 'display_wms_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'defaults_marker' )
				add_settings_section( $slug, $title, array( &$this, 'display_defaults_marker_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'google_places' )
				add_settings_section( $slug, $title, array( &$this, 'display_google_places_section' ), 'leafletmapsmarker_settings' );
			else if ( $slug == 'misc' )
				add_settings_section( $slug, $title, array( &$this, 'display_misc_section' ), 'leafletmapsmarker_settings' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'leafletmapsmarker_settings' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
	}
	/**
	 * save defaults for new options after plugin updates but keep values of old settings
	 */
	public function save_defaults_for_new_options() {
		//info:  set defaults for options introduced in v1.1
		if (get_option('leafletmapsmarker_version') == '1.0' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.1')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		//info:  set defaults for options introduced in v1.2
		if (get_option('leafletmapsmarker_version') == '1.1' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.2')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		/* template for plugin updates 
		//info:  set defaults for options introduced in v1.3
		if (get_option('leafletmapsmarker_version') == '1.2.2' )
		{
			$new_options_defaults = array();
			foreach ( $this->settings as $id => $setting ) 
			{
				if ( $setting['type'] != 'heading' && $setting['type'] != 'helptext' && $setting['version'] == '1.3')
				{
				$new_options_defaults[$id] = $setting['std'];
				}
			}
		$options_current = get_option( 'leafletmapsmarker_options' );
		$options_new = array_merge($options_current, $new_options_defaults);
		update_option( 'leafletmapsmarker_options', $options_new );
		}
		*/
	}
	
	/**
	* Validate settings
	*/
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_settings'] ) ) {
			$options = get_option( 'leafletmapsmarker_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		return false;
	}
}
$leafletmapsmarker_options = new Leafletmapsmarker_options();
function lmm_option( $option ) {
	$options = get_option( 'leafletmapsmarker_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}
?>