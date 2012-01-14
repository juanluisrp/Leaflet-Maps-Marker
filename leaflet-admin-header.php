<?php
/*
    Admin Header - Leaflet Maps Marker Plugin
*/
?>
<?php 
require_once(ABSPATH . "/wp-includes/pluggable.php");
$admin_quicklink_settings_buttons = ( current_user_can( "activate_plugins" ) ) ? "<a class='button-secondary' href='" . WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_settings'>".__('Settings','lmm')."</a>" : "";
?>
<div style="float:right;">
  <div style="text-align:center;"><small><a href="http://www.mapsmarker.com" target="_blank" style="text-decoration:none;">MapsMarker.com</a> supports</small></div>
  <a href="http://www.open3.at" target="_blank" title="open3.at - network for the promotion of Open Society, OpenGov and OpenData in Austria"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/logo-open3-small.png" width="143" height="30" border="0"/></a></div>
  <div style="font-size:1.5em;margin-bottom:5px;padding:10px 0 0 0;"><span style="font-weight:bold;">Leaflet Maps Marker v<?php echo get_option("leafletmapsmarker_version") ?></span> - "OGD Wien - Meine Platzl im Gr&auml;tzl"-Edition</div>
  <p style="margin:1.5em 0;">
  <a class="button-secondary" href="<?php echo WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_markers"><?php _e("List all markers", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_marker"><?php _e("Add new marker", "lmm") ?></a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layers"><?php _e("List all layers", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer"><?php _e("Add new layer", "lmm") ?></a>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
  <a class="button-secondary" href="<?php echo WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_help"><?php _e("Help & Credits", "lmm") ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <?php echo $admin_quicklink_settings_buttons ?>
  </p>
<table cellpadding="5" cellspacing="0" style="border:1px solid #ccc;width:98%;background:#efefef;">
  <tr>
    <td valign="center"><div style="float:left;"><a href="http://www.mapsmarker.com" target="_blank" title="www.MapsMarker.com"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/logo-mapsmarker.png" width="156" height="125" alt="Leaflet Maps Marker Plugin Logo by Susanne Mandl - www.greenflamingomedia.com" /></a></div>
<div style="float:right;"> 
        <!--Begin support table-->
        <table cellspacing="5">
          <tr>
            <td style="width:185px;text-align:center;background:#fff;"><strong><?php _e('Featured sponsors','lmm') ?></strong> <a href="http://www.mapsmarker.com/sponsors" target="_blank" title="<?php esc_attr_e('Click here for more information on how to become a featured sponsor','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/icon-question-mark.png" width="12" height="12" border="0"/></a><br/><a href="http://mapsmarker.com/sponsors" target="_blank" title="<?php esc_attr_e('Click here for more information on how to become a featured sponsor','lmm') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/logo-featured-sponsor.png" width="150" height="100" border="0"/></a></a><br/><a href="http://mapsmarker.com/sponsors" target="_blank" title="<?php esc_attr_e('Click here for more information on how to become a featured sponsor','lmm') ?>">www.your-url.com</a>
            </td>
            <td style="background:#fff;text-align:center;">
<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ZKVA3VKMEU2TA">
<table>
<tr><td><input type="hidden" name="on0" value="Sponsorship Level">
	<select name="os0" style="width:210px;">
	<option value="Supporter 2">Please select sponsorship level</option>
	<option value="Contributor">Contributor €1,00 EUR</option>
	<option value="Contributor">Contributor €5,00 EUR</option>
	<option value="Supporter">Supporter €10,00 EUR</option>
	<option value="Supporter 2">Supporter €25,00 EUR</option>
	<option value="Donor">Donor €50,00 EUR</option>
	<option value="Sponsor">Sponsor €100,00 EUR</option>
	<option value="Benefactor">Benefactor €250,00 EUR</option>
	<option value="Patron">Patron €500,00 EUR</option>
	<option value="Open Source Angel">Open Source Angel €1.000,00 EUR</option>
	<option value="Corporate Angel">Corporate Angel €2.500,00 EUR</option>
</select> </td></tr>
<tr><td colspan="2"><input type="hidden" name="on1" value="Message"><?php _e('Message','lmm') ?> <input type="text" name="os1" maxlength="200"></td></tr>
</table>
<input type="hidden" name="currency_code" value="EUR">
<input type="image" src="<?php echo LEAFLET_PLUGIN_URL ?>/img/donate-paypal.jpg" width="130" height="89" border="0" name="submit" alt="" title="<?php esc_attr_e('If you like to donate a certain amount of money to show your support, you can also use Paypal. If you don´t have a Paypal account, you can use your credit card or bank account (where available). Please click on the paypal image to proceed to the donation form.','lmm') ?>">
</form>
            </td>
            <td style="background:#fff;width:115px;text-align:center;"><a href="http://www.amazon.com/exec/obidos/redirect-home?tag=leafletmapsmarker-21&site=home" target="_blank" title="<?php esc_attr_e('The easiest way to support this plugin is to buy something from Amazon by using this referrer link. Note: this doesn´t cost you anything as your purchase volume won´t be increased, but I will receive 6 per cent of your purchase volume as a referral fee.','lmm') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/donate-amazon-partnernet.jpg" style="border:1px solid #ccc;padding:4px;" width="84" height="40" border="0"/></a>
		<br/><br/><a href="http://www.amazon.de/registry/wishlist/3P6LQRP11V1AF" target="_blank" title="<?php esc_attr_e('Another way to show your support for this plugin is to buy something from my Amazon wishlist, respectively sending me a greeting card worth from 15 to 500 $ with a personal note, which I would very much appreciate.','lmm') ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>/img/donate-amazon.jpg" width="100" height="50" border="0"/></a></td>
            <td style="text-align:center;width:72px;background:#fff;">Flattr <a class="FlattrButton" style="display:none;" href="http://www.mapsmarker.com" title="MapsMarker.com" lang="<?php echo WPLANG ?>">MapsMarker.com</a>
              <noscript>
              <a href="http://flattr.com/thing/447395/MapsMarker-com" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a>
              </noscript>
			</td>
          </tr>
        </table>
        <!--End support table-->
      </div>
	<p><strong><?php _e('A message from the plugin´s author','lmm') ?> <a href="http://www.harm.co.at" target="_blank" title="<?php esc_attr_e('Show website of plugin author','lmm') ?>" style="text-decoration:none;">Robert Harm</a>:</strong><br/>
			<?php _e('It is hard to continue development and support for Leaflet Maps Marker-plugin without contributions from users like you.','lmm') ?> <?php _e('If you enjoy using the plugin - <strong>particularly within a commercial context</strong> - please consider making a donation.','lmm') ?> <?php _e('Your donation help keeping the plugin free for everyone and allow me to spend more time on developing, maintaining and support.','lmm') ?> <?php _e('I´d be happy to accept your donation! Thanks!','lmm') ?> <?php _e('For more information on how to donate, please visit','lmm') ?>  <a href="http://mapsmarker.com/donations" style="text-decoration:none;" target="_blank">http://mapsmarker.com/donations</a><br/><br/>Web: <a href="http://www.mapsmarker.com"  style="text-decoration:none;" target="_blank">MapsMarker.com</a>&nbsp;&nbsp;|&nbsp;&nbsp;Twitter: <a href="http://twitter.com/mapsmarker" style="text-decoration:none;" target="_blank">@MapsMarker</a>&nbsp;&nbsp;|&nbsp;&nbsp;Facebook: <a href="http://www.facebook.com/mapsmarker" style="text-decoration:none;" target="_blank">facebook.com/MapsMarker</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://wordpress.org/extend/plugins/leaflet-maps-marker/"  style="text-decoration:none;" target="_blank" title="<?php esc_attr_e('please rate this plugin on wordpress.org','lmm') ?>"><?php _e('Rate plugin','lmm') ?></a></p></td>
  </tr>
</table>