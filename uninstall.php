<?php
/*die if uninstall not called from Wordpress exit*/
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
/* Remove settings */
if (is_multisite()) 
{
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	if ($blogs) 
		{
		foreach($blogs as $blog) 
			{
			switch_to_blog($blog['blog_id']);
			delete_option('leafletmapsmarker_version');
			delete_option('leafletmapsmarker_options');
			delete_option('leafletmapsmarker_redirect');
			/* Remove and clean tables */
			$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
			$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
			$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
			}
		restore_current_blog();
		/*remove map icons directory*/
		$icons_directory = ABSPATH . 'wp-content/uploads/leaflet-maps-marker-icons/';
		if (is_dir($icons_directory)) 
		{
		foreach(glob($icons_directory.'*.*') as $v){
		unlink($v);
		}
		rmdir($icons_directory);
		}
		}
} 
else
{
	delete_option('leafletmapsmarker_version');
	delete_option('leafletmapsmarker_options');
	delete_option('leafletmapsmarker_redirect');
	/* Remove and clean tables */
	$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_layers`");
	$GLOBALS['wpdb']->query("DROP TABLE `".$GLOBALS['wpdb']->prefix."leafletmapsmarker_markers`");
	$GLOBALS['wpdb']->query("OPTIMIZE TABLE `" .$GLOBALS['wpdb']->prefix."options`");
	/*remove map icons directory*/
	$icons_directory = ABSPATH . 'wp-content/uploads/leaflet-maps-marker-icons/';
	if (is_dir($icons_directory)) 
	{
	foreach(glob($icons_directory.'*.*') as $v){
	unlink($v);
	}
	rmdir($icons_directory);
	}
}
?>