<?php
/*
    List all markers - Leaflet Maps Marker Plugin
*/
//info:  get pagination
$getorder = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : ''; 
if ($getorder == 'asc') { $sortorder = 'desc'; } else { $sortorder= 'asc'; };
if ($getorder == 'asc') { $sortordericon = 'asc'; } else { $sortordericon = 'desc'; };
$pager = '';
if ($mcount > intval($lmm_options[ 'markers_per_page' ])) {
  $maxpage = intval(ceil($mcount / intval($lmm_options[ 'markers_per_page' ])));
  if ($maxpage > 1) {
    $pager .= '<div class="tablenav-pages" style="display:inline;float:right;margin-right:25px;"><form method="POST" action="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers">' . __('page','lmm') . ' ';
    if ($pagenum > (2 + $radius * 2)) {
      foreach (range(1, 1 + $radius) as $num)
        $pager .= '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
      $pager .= ' … ';
      foreach (range($pagenum - $radius, $pagenum - 1) as $num)
        $pager .= '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    }
    else
      if ($pagenum > 1)
        foreach (range(1, $pagenum - 1) as $num)
          $pager .= '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    $pager .= '<span class="paging-input"><input type="text" size="2" value="'.$pagenum.'" name="paged" class="current-page"> <!--total pages <span class="total-pages">'.$maxpage.' </span>--></span>';
    if (($maxpage - $pagenum) >= (2 + $radius * 2)) {
      foreach (range($pagenum + 1, $pagenum + $radius) as $num)
        $pager .= '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
      $pager .= ' … ';
      foreach (range($maxpage - $radius, $maxpage) as $num)
        $pager .= '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    }
    else
      if ($pagenum < $maxpage)
        foreach (range($pagenum + 1, $maxpage) as $num)
          $pager .= '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers&paged='.$num.'&order='.$getorder.'" class="first-page">'.$num.'</a>';
    $pager .= '</div></form>';
  }
}
?>
<div class="wrap">
	<?php include('leaflet-admin-header.php'); ?>
	<h3>
		<?php _e('List all markers','lmm') ?>
	</h3>
	<?php 
$noncelink= wp_create_nonce('exportcsv-nonce');
$csvexportlink = LEAFLET_PLUGIN_URL . 'leaflet-exportcsv.php?_wpnonce=' . $noncelink; ?>
	<p> <?php echo "<a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-add.png\" /></a> <a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker\" style=\"text-decoration:none;\">" . __('Add marker','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . $csvexportlink . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-csv.png\" /></a> <a target=\"_blank\" href=\"" . $csvexportlink . "\" style=\"text-decoration:none;\">" . __('Export all markers as csv-file','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-kml.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-kml.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-kml.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as KML','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geojson.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-json.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geojson.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as GeoJSON','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-georss.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-georss.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-georss.php?layer=all\" style=\"text-decoration:none;\">" . __('Subscribe to markers via GeoRSS','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"" . LEAFLET_PLUGIN_URL . "leaflet-wikitude.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-wikitude.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-wikitude.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as ARML for Wikitude','lmm') . "</a>"; ?> </p>
	<div class="tablenav top"><?php echo (isset($_POST['searchtext']) != NULL) ? __('Search result','lmm') : __('Total','lmm') ?>: <?php echo $mcount; ?>
		<?php _e('marker','lmm') ?>&nbsp;&nbsp;|&nbsp;&nbsp;
		<?php _e('Markers per page','lmm') ?>:
		<?php if (current_user_can('activate_plugins')) { echo '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#misc" title="' . esc_attr__('Change number in settings','lmm') . '" style="text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a> (<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#misc" title="' . esc_attr__('select columns to display','lmm') . '" style="text-decoration:none;">' . __('columns','lmm') . '</a>)'; } else { echo intval($lmm_options[ 'markers_per_page' ]); } ?>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<?php $nonce= wp_create_nonce  ('markersearch-nonce'); ?>
		<form method="post" style="display:inline;">
			<?php wp_nonce_field('markersearch-nonce'); ?>
			<input type="hidden" name="action" value="search" />
			<input style="width: 150px;" type="text" id="searchtext" name="searchtext" value="<?php echo (isset($_POST['searchtext']) != NULL) ? stripslashes($_POST['searchtext']) : "" ?>"/>
			<input type="submit" name="searchsubmit" value="<?php _e('search', 'lmm') ?>"/>
		</form>
		&nbsp;&nbsp;&nbsp;<?php echo $showall = (isset($_POST['searchtext']) != NULL) ? "<a style=\"text-decoration:none;\" href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_markers\">" . __('show all markers','lmm') . "</a>" : ""; ?> <?php echo $pager; ?> </div>
	<form method="POST">
		<!--needed for multiple checkboxes, planed for next version-->
		<table cellspacing="0" class="wp-list-table widefat fixed bookmarks" style="width:auto;">
			<thead>
				<tr> 
					<!--<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>-->
					<th class="manage-column column-id sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.id&order=<?php echo $sortorder; ?>"><span>ID</span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-icon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.icon&order=<?php echo $sortorder; ?>"><span><?php _e('Icon', 'lmm') ?></span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-markername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.markername&order=<?php echo $sortorder; ?>"><span><?php _e('Marker name','lmm') ?></span><span class="sorting-indicator"></span></a></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.popuptext&order=<?php echo $sortorder; ?>"><span><?php _e('Popup text','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) { ?>
					<th class="manage-column column-layername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=l.name&order=<?php echo $sortorder; ?>"><span><?php _e('Layer', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.openpopup&order=<?php echo $sortorder; ?>"><span><?php _e('Popup status', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.zoom&order=<?php echo $sortorder; ?>"><span><?php _e('Zoom', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.basemap&order=<?php echo $sortorder; ?>"><span><?php _e('Basemap', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdby&order=<?php echo $sortorder; ?>"><span><?php _e('Created by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdon&order=<?php echo $sortorder; ?>"><span><?php _e('Created on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedby&order=<?php echo $sortorder; ?>"><span><?php _e('Updated by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedon&order=<?php echo $sortorder; ?>"><span><?php _e('Updated on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.controlbox&order=<?php echo $sortorder; ?>"><span><?php _e('Controlbox status','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
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
					<th class="manage-column column-id sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.id&order=<?php echo $sortorder; ?>"><span>ID</span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-icon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.icon&order=<?php echo $sortorder; ?>"><span><?php _e('Icon', 'lmm') ?></span><span class="sorting-indicator"></span></a></th>
					<th class="manage-column column-markername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.markername&order=<?php echo $sortorder; ?>"><span><?php _e('Marker name','lmm') ?></span><span class="sorting-indicator"></span></a></th>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) { ?>
					<th class="manage-column column-popuptext sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.popuptext&order=<?php echo $sortorder; ?>"><span><?php _e('Popup text','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) { ?>
					<th class="manage-column column-layername sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=l.name&order=<?php echo $sortorder; ?>"><span><?php _e('Layer', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) { ?>
					<th class="manage-column column-openpopup"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.openpopup&order=<?php echo $sortorder; ?>"><span><?php _e('Popup status', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) { ?>
					<th class="manage-column column-coords" scope="col"><?php _e('Coordinates', 'lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) { ?>
					<th class="manage-column column-mapsize" scope="col"><?php _e('Map size','lmm') ?></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) { ?>
					<th class="manage-column column-zoom" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.zoom&order=<?php echo $sortorder; ?>"><span><?php _e('Zoom', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) { ?>
					<th class="manage-column column-basemap" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.basemap&order=<?php echo $sortorder; ?>"><span><?php _e('Basemap', 'lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) { ?>
					<th class="manage-column column-createdby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdby&order=<?php echo $sortorder; ?>"><span><?php _e('Created by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) { ?>
					<th class="manage-column column-createdon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.createdon&order=<?php echo $sortorder; ?>"><span><?php _e('Created on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) { ?>
					<th class="manage-column column-updatedby sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedby&order=<?php echo $sortorder; ?>"><span><?php _e('Updated by','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) { ?>
					<th class="manage-column column-updatedon sortable <?php echo $sortordericon; ?>" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.updatedon&order=<?php echo $sortorder; ?>"><span><?php _e('Updated on','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
					<?php if ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) { ?>
					<th class="manage-column column-code" scope="col"><a href="<?php echo WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_markers&orderby=m.controlbox&order=<?php echo $sortorder; ?>"><span><?php _e('Controlbox status','lmm') ?></span><span class="sorting-indicator"></span></a></th><?php } ?>
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
  if (count($marklist) < 1)
    echo '<tr><td colspan="11">'.__('No marker created yet', 'lmm').'</td></tr>';
  else
    foreach ($marklist as $row)
	{
		if (current_user_can( $lmm_options[ 'capabilities_delete' ])) {
			$delete_link_marker = ' | </span><span class="delete"><a onclick="if ( confirm( \'' . __('Do you really want to delete this marker?', 'lmm') . ' (' . $row['markername'] . ' - ID ' . $row['id'] . ')\' ) ) { return true;}return false;" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=delete&id=' . $row['id'] . '&_wpnonce=' . $markernonce . '" class="submitdelete">' . __('delete','lmm') . '</a></span>';
		} else {
			$delete_link_marker = '';
		}
     $rowlayername = ($row['layerid'] == 0) ? "" . __('unassigned','lmm') . "<br>" : "<a title='" . __('Edit layer ','lmm') . $row['layer'] . "' href='" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $row['layer'] . "'>" . $row['layername'] . "</a>";
     $openpopupstatus = ($row['openpopup'] == 1) ? __('open','lmm') : __('closed','lmm');
     $popuptextabstract = (strlen($row['popuptext']) >= 90) ? "...": "";
     //info: set column display variables - need for for-each
     $column_popuptext = ((isset($lmm_options[ 'misc_marker_listing_columns_popuptext' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_popuptext' ] == 1 )) ?
'<td><a title="' . __('Edit marker ', 'lmm') . ' ' . $row['id'] . '" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" >' . mb_substr(strip_tags(stripslashes($row['popuptext'])), 0, 90) . $popuptextabstract . '</a></td>' : '';
     $column_layer = ((isset($lmm_options[ 'misc_marker_listing_columns_layer' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_layer' ] == 1 )) ?
'<td>' . stripslashes($rowlayername) . '</td>' : '';
     $column_openpopup = ((isset($lmm_options[ 'misc_marker_listing_columns_openpopup' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_openpopup' ] == 1 )) ?
'<td>' . $openpopupstatus . '</td>' : '';
     $column_coordinates = ((isset($lmm_options[ 'misc_marker_listing_columns_coordinates' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_coordinates' ] == 1 )) ? '<td>Lat: ' . $row['lat'] . '<br/>Lon: ' . $row['lon'] . '</td>' : '';
     $column_mapsize = ((isset($lmm_options[ 'misc_marker_listing_columns_mapsize' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_mapsize' ] == 1 )) ? '<td>' . __('Width','lmm') . ': '.$row['mapwidth'].$row['mapwidthunit'].'<br/>' . __('Height','lmm') . ': '.$row['mapheight'].'px</td>' : '';
     $column_zoom = ((isset($lmm_options[ 'misc_marker_listing_columns_zoom' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_zoom' ] == 1 )) ? '<td style="text-align:center;">' . $row['zoom'] . '</td>' : '';
     $column_controlbox = ((isset($lmm_options[ 'misc_marker_listing_columns_controlbox' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_controlbox' ] == 1 )) ? '<td style="text-align:center;">'.$row['controlbox'].'</td>' : '';
     $column_shortcode = ((isset($lmm_options[ 'misc_marker_listing_columns_shortcode' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_shortcode' ] == 1 )) ? '<td><input style="width:200px;background:#f3efef;" type="text" value="[' . $lmm_options[ 'shortcode' ] . ' marker=&quot;' . $row['id'] . '&quot;]" readonly></td>' : '';
     $column_kml = ((isset($lmm_options[ 'misc_marker_listing_columns_kml' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_kml' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-kml.png" width="14" height="14" alt="KML-Logo" /><br/>KML</a></td>' : '';
     $column_fullscreen = ((isset($lmm_options[ 'misc_marker_listing_columns_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_fullscreen' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo"><br/>' . __('Fullscreen','lmm') . '</a></td>' : '';
     $column_qr_code = ((isset($lmm_options[ 'misc_marker_listing_columns_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_qr_code' ] == 1 )) ? '<td style="text-align:center;"><a href="https://chart.googleapis.com/chart?chs=' . $lmm_options[ 'misc_qrcode_size' ] . 'x' . $lmm_options[ 'misc_qrcode_size' ] . '&cht=qr&chl=' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo"><br/>' . __('QR code','lmm') . '</a></td>' : '';
     $column_geojson = ((isset($lmm_options[ 'misc_marker_listing_columns_geojson' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_geojson' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $row['id'] . '&callback=jsonp" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-json.png" width="14" height="14" alt="GeoJSON-logo"><br/>GeoJSON</a></td>' : '';
     $column_georss = ((isset($lmm_options[ 'misc_marker_listing_columns_georss' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_georss' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-georss.png" width="14" height="14" alt="GeoRSS-logo"><br/>GeoRSS</a></td>' : '';
     $column_wikitude = ((isset($lmm_options[ 'misc_marker_listing_columns_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_wikitude' ] == 1 )) ? '<td style="text-align:center;"><a href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $row['id'] . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'img/icon-wikitude.png" width="14" height="14" alt="Wikitude-logo"><br/>Wikitude</a></td>' : '';
     $column_basemap = ((isset($lmm_options[ 'misc_marker_listing_columns_basemap' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_basemap' ] == 1 )) ? '<td >' . $row['basemap'] . '</td>' : '';
     $column_createdby = ((isset($lmm_options[ 'misc_marker_listing_columns_createdby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdby' ] == 1 )) ? '<td >' . $row['createdby'] . '</td>' : '';
     $column_createdon = ((isset($lmm_options[ 'misc_marker_listing_columns_createdon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_createdon' ] == 1 )) ? '<td >' . $row['createdon'] . '</td>' : '';
     $column_updatedby = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedby' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedby' ] == 1 )) ? '<td >' . $row['updatedby'] . '</td>' : '';
     $column_updatedon = ((isset($lmm_options[ 'misc_marker_listing_columns_updatedon' ] ) == TRUE ) && ( $lmm_options[ 'misc_marker_listing_columns_updatedon' ] == 1 )) ? '<td >' . $row['updatedon'] . '</td>' : '';
	  echo '<tr valign="middle" class="alternate" id="link-' . $row['id'] . '">
      <!--<th class="check-column" scope="row"><input type="checkbox" value="' . $row['id'] . '" name="markercheck[]"></th>-->
      <td>' . $row['id'] . '</td>
      <td>';
      if ($row['icon'] != null) { 
         echo '<img src="' . LEAFLET_PLUGIN_ICONS_URL . '/' . $row['icon'] . '" title="' . $row['icon'] . '" />'; 
         } else { 
         echo '<img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" title="' . esc_attr__('standard icon','lmm') . '" />';};
      echo '</td>
		  <td><strong><a title="' . esc_attr__('Edit marker','lmm') . ' (' . $row['id'].')" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '" class="row-title">' . stripslashes($row['markername']) . '</a></strong><br/><div class="row-actions"><span class="edit"><a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $row['id'] . '">' . __('edit','lmm') . '</a>' . $delete_link_marker . '</div></td>	  
		  
		  ' . $column_popuptext . '
		  ' . $column_layer . '
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
	</form>
	<div class="tablenav top"><?php echo (isset($_POST['searchtext']) != NULL) ? __('Search result','lmm') : __('Total','lmm') ?>: <?php echo $mcount; ?>
		<?php _e('Marker','lmm') ?>&nbsp;&nbsp;|&nbsp;&nbsp;
		<?php _e('Markers per page','lmm') ?>:
		<?php if (current_user_can('activate_plugins')) { echo '<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#misc" title="' . esc_attr__('Change number in settings','lmm') . '" style="text-decoration:none;">' . intval($lmm_options[ 'markers_per_page' ]) . '</a> (<a href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#misc" title="' . esc_attr__('select columns to display','lmm') . '" style="text-decoration:none;">' . __('columns','lmm') . '</a>)'; } else { echo intval($lmm_options[ 'markers_per_page' ]); } ?>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<form method="post" style="display:inline;">
			<?php wp_nonce_field('markersearch-nonce'); ?>
			<input type="hidden" name="action" value="search" />
			<input style="width: 150px;" type="text" id="searchtext" name="searchtext" value="<?php echo (isset($_POST['searchtext']) != NULL) ? stripslashes($_POST['searchtext']) : "" ?>"/>
			<input type="submit" name="searchsubmit" value="<?php _e('search', 'lmm') ?>"/>
		</form>
		&nbsp;&nbsp;&nbsp;<?php echo $showall = (isset($_POST['searchtext']) != NULL) ? "<a style=\"text-decoration:none;\" href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_markers\">" . __('show all markers','lmm') . "</a>" : ""; ?> <?php echo $pager; ?> </div>
	<p> <?php echo "<a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-add.png\" /></a> <a href=\"" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_marker\" style=\"text-decoration:none;\">" . __('Add marker','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . $csvexportlink . "\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-csv.png\" /></a> <a target=\"_blank\" href=\"" . $csvexportlink . "\" style=\"text-decoration:none;\">" . __('Export all markers as csv-file','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-kml.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-kml.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-kml.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as KML','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geojson.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-json.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-geojson.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as GeoJSON','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-georss.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-georss.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-georss.php?layer=all\" style=\"text-decoration:none;\">" . __('Subscribe to markers via GeoRSS','lmm') . "</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"" . LEAFLET_PLUGIN_URL . "leaflet-wikitude.php?layer=all\" style=\"text-decoration:none;\"><img src=\"" . LEAFLET_PLUGIN_URL . "img/icon-wikitude.png\" /></a> <a target=\"_blank\" href=\"" . LEAFLET_PLUGIN_URL . "leaflet-wikitude.php?layer=all\" style=\"text-decoration:none;\">" . __('Export all markers as ARML for Wikitude','lmm') . "</a>"; ?> </p>
</div>