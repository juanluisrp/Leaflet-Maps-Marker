<?php
/*
    Tools - Leaflet Maps Marker Plugin
*/
?>
<div class="wrap">
	<?php include('leaflet-admin-header.php'); ?>
	<?php
$action = isset($_POST['action']) ? $_POST['action'] : '';
if (!empty($action)) {
	$toolnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($toolnonce, 'tool-nonce') ) { die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').''); };
  if ($action == 'mass_assign') {
		$result = $wpdb->prepare( "UPDATE $table_name_markers SET layer = %d where layer = %d", $_POST['layer_assign_to'], $_POST['layer_assign_from'] );
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('All markers from layer ID %1$s have been successfully assigned to layer ID %2$s','lmm'), $_POST['layer_assign_from'], $_POST['layer_assign_to']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
		
  }
  elseif ($action == 'mass_delete_from_layer') {
		$result = $wpdb->prepare( "DELETE FROM $table_name_markers where layer = %d", $_POST['delete_from_layer']);
		$wpdb->query( $result );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<p><div class="updated" style="padding:10px;">' . sprintf( esc_attr__('All markers from layer ID %1$s have been successfully deleted','lmm'), $_POST['delete_from_layer']) . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
  }
  elseif ($action == 'mass_delete_all_markers') {
		$result = $wpdb->prepare( "DELETE FROM $table_name_markers");
		$wpdb->query( $result );
  		$delete_confirm_checkbox = isset($_POST['delete_confirm_checkbox']) ? '1' : '0';
	  	if ($delete_confirm_checkbox == 1) {
			echo '<p><div class="updated" style="padding:10px;">' . __('All markers from all layers have been successfully deleted','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Please confirm that you want to delete all markers by checking the checkbox','lmm') . '</div><br/><a class="button-secondary" href="' . WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_tools">' . __('Back to Tools', 'lmm') . '</a></p>';  
		}
  }
} else {
$layerlist = $wpdb->get_results('SELECT * FROM ' . $table_name_layers . ' WHERE id>0', ARRAY_A);
?>
<h3>Tools</h3>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<form method="post">
<input type="hidden" name="action" value="mass_assign" />
<?php wp_nonce_field('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<td colspan="2"><strong><?php _e('Move markers to a layer','lmm') ?></strong></td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<?php _e('Source','lmm') ?>: 
		<select id="layer_assign_from" name="layer_assign_from">
		<?php $markercount_layer0 = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id=0'); ?>
		<option value="0">ID 0 - <?php _e('unassigned','lmm') ?> (<?php echo $markercount_layer0; ?> <?php _e('marker','lmm'); ?>)</option>
		<?php
		foreach ($layerlist as $row) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
			echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes($row['name']) . ' (' . $markercount .' ' . __('marker','lmm') . ')</option>';
		}
		?>
		</select>
		<?php _e('Target','lmm') ?>: 
		<select id="layer_assign_to" name="layer_assign_to">
		<option value="0">ID 0 - <?php _e('unassigned','lmm') ?> (<?php echo $markercount_layer0; ?> <?php _e('marker','lmm'); ?>)</option>
		<?php
		foreach ($layerlist as $row) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
			echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes($row['name']) . ' (' . $markercount .' ' . __('marker','lmm') . ')</option>';
		}
		?>
		</select>
		</td>
		<td>
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_asign" value="<?php _e('move markers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to move the selected markers?','lmm') ?>')" />
		</td>
	</tr>
</table>
</form>
<br/><br/>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<form method="post">
<input type="hidden" name="action" value="mass_delete_from_layer" />
<?php wp_nonce_field('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<td colspan="2"><strong><?php _e('Delete all markers from a layer','lmm') ?></strong></td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<?php _e('Layer','lmm') ?>: 
		<select id="delete_from_layer" name="delete_from_layer">
		<option value="0">ID 0 - <?php _e('unassigned','lmm') ?> (<?php echo $markercount_layer0; ?> <?php _e('marker','lmm'); ?>)</option>
		<?php
		foreach ($layerlist as $row) {
			$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
			echo '<option value="' . $row['id'] . '">ID ' . $row['id'] . ' - ' . stripslashes($row['name']) . ' (' . $markercount .' ' . __('marker','lmm') . ')</option>';
		}
		?>
		</select>
		</td>
		<td>
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_delete_from_layer" value="<?php _e('delete all markers from selected layer','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to delete all markers from the selected layer? (cannot be undone)','lmm') ?>')" />
		</td>
	</tr>
</table>
</form>
<br/><br/>
<?php $nonce= wp_create_nonce('tool-nonce'); ?>
<form method="post">
<input type="hidden" name="action" value="mass_delete_all_markers" />
<?php wp_nonce_field('tool-nonce'); ?>
<table class="widefat fixed" style="width:auto;">
	<tr style="background-color:#efefef;">
		<?php 
		$markercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_markers.''); 
		$layercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.''); 
		$delete_all = sprintf( esc_attr__('Delete all %1$s markers from all %2$s layers','lmm'), $markercount_all, $layercount_all);
		?>
		<td colspan="2"><strong><?php echo $delete_all ?></strong></td>
	</tr>
	<tr>
		<td style="vertical-align:middle;">
		<input type="checkbox" id="delete_confirm_checkbox" name="delete_confirm_checkbox" /> <?php _e('Yes','lmm') ?>
		</td>
		<td>
		<input style="font-weight:bold;" class="submit button-primary" type="submit" name="mass_delete_all_markers" value="<?php _e('delete all markers from all layers','lmm') ?> &raquo;" onclick="return confirm('<?php _e('Do you really want to delete all markers from all layers? (cannot be undone)','lmm') ?>')" />
		</td>
	</tr>
</table>
</form>
	
</div>
<!--wrap--> 
<?php } ?>