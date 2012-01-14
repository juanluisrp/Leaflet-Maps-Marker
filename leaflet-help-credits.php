<?php
/*
    Help and credits page - Leaflet Maps Marker Plugin
*/
?>
<div class="wrap">
	<?php $lmm_options = get_option( 'leafletmapsmarker_options' ); ?>
	<?php include('leaflet-admin-header.php'); ?>
	<p>
	<h3>
		<?php _e('Help','lmm') ?>
	</h3>
	<p>
		<?php _e('Do you have questions or issues with Leaflet Maps Marker? Please use the following support channels appropriately.','lmm') ?>
	</p>
	<ul>
		<li>- <a href="http://www.mapsmarker.com/faq/" target="_blank">
			<?php _e('FAQ','lmm') ?>
			</a>
			<?php _e('(frequently asked questions)','lmm') ?>
		</li>
		<li>- <a href="http://www.mapsmarker.com/docs/" target="_blank">
			<?php _e('Documentation','lmm') ?>
			</a></li>
		<li>- <a href="http://www.mapsmarker.com/ideas/" target="_blank">
			<?php _e('Ideas','lmm') ?></a> <?php _e('(feature requests)','lmm') ?>
		</li>
		<li>- <a href="http://wordpress.org/tags/leaflet-maps-marker?forum_id=10" target="_blank">WordPress Support Forum</a>
			<?php _e('(free community support)','lmm') ?>
		</li>
		<li>- <a href="http://wpquestions.com/affiliates/register/name/robertharm" target="_blank">WP Questions</a>
			<?php _e('(paid community support)','lmm') ?>
		</li>
		<li>- <a href="http://wphelpcenter.com/" target="_blank">WordPress HelpCenter</a>
			<?php _e('(paid professional support)','lmm') ?>
		</li>
	</ul>
	<p>
		<?php _e('More information on support','lmm') ?>
		: <a href="http://www.mapsmarker.com/support/" target="_blank">http://www.mapsmarker.com/support</a></p>
	<h3>
		<?php _e('Licence','lmm') ?>
	</h3>
	<p>
		<?php _e('Good news, this plugin is free for everyone! Since it is released under the GPL2, you can use it free of charge on your personal or commercial blog.<br/>But if you enjoy this plugin, you can thank me and leave a small donation for the time I have spent writing and supporting this plugin.<br/>Please see <a href="http://www.mapsmarker.com/donations" target="_blank">http://www.mapsmarker.com/donations</a> for details.','lmm') ?>
	</p>
	<h3>
		<?php _e('Licenses for used libraries, services and images','lmm') ?>
	</h3>
	<ul>
		<li>- OpenStreetMap: <a href="http://wiki.openstreetmap.org/wiki/OpenStreetMap_License" target="_blank">OpenStreetMap License</a></li>
		<li>- Leaflet by Cloudmade, <a href="http://leaflet.cloudmade.com" target="_blank">http://leaflet.cloudmade.com</a></li>
		<li>- Datasource OGD Vienna maps: Stadt Wien - <a href="http://data.wien.gv.at" target="_blank">http://data.wien.gv.at</a></li>
		<li>- Address autocompletion powered by <a href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank">Google Places API</a></li>
		<li>- <a href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a> by Nicolas Mollet</li>
		<li>- Map center icon by <a href="http://glyphish.com/" target="_blank">Joseph Wain</a>, licence: Creative Commons Attribution (by)</li>
		<li>- Plus, json &amp; csv-export icon by <a href="http://www.pinvoke.com/" target="_blank">Yusuke Kamiyamane</a>, licence: Creative Commons Attribution (by)</li>
		<li>- Question Mark Icon by <a href="http://www.randomjabber.com/" target="_blank">RandomJabber</a></li>
	</ul>
	<h3>
		<?php _e('Credits & special thanks','lmm') ?>
	</h3>
	<ul>
		<li>- Sindre Wimberger (<a href="http://www.sindre.at" target="_blank">http://www.sindre.at</a>) - bugfixing &amp; geo-consulting</li>
		<li>- Susanne Mandl (<a href="http://www.greenflamingomedia.com" target="_blank">http://www.greenflamingomedia.com</a>) - plugin logo</li>
		<li>- <a href="http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/" target="_blank">WordPress-Settings-API-Class</a> by Aliso the geek</li>
	</ul>
	</p>
</div>