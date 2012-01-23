=== Leaflet Maps Marker ===
Contributors:      harmr
Plugin Name:       Leaflet Maps Marker
Plugin URI:        http://www.mapsmarker.com
Tags:              map, maps, Leaflet, OpenStreetMap, geoJSON, json, jsonp, OSM, travelblog, opendata, open data, opengov, open government, ogdwien, google maps, googlemaps, gmaps, WMTS, geoRSS, location, geo, geocoding, geolocation, travel, mapnick, osmarender, cloudmade, mapquest, geotag, geocaching, gpx, OpenLayers, mapping, bikemap, coordinates, geocode, geocoding, geotagging, latitude, longitude, position, route, tracks, google maps, google earth, gmaps, ar, augmented-reality, wikitude, wms, web map service, geocache, geocaching, qr, qr code, fullscreen, marker, layer, karte, blogmap, geocms, geographic, routes, tracks, directions, navigation, routing, location plan, YOURS, yournavigation, ORS, openrouteservice
Author URI:        http://www.harm.co.at
Author:            Robert Harm (with special support from Sindre Wimberger)
Donate link:       http://www.mapsmarker.com/donations
Requires at least: 3.0 
Tested up to:      3.4-alpha-19704
Stable tag:        1.4

Pin, organize & show your favorite places through OpenStreetMap/WMTS, Google Maps/Earth (KML), GeoJSON, GeoRSS or Augmented-Reality browsers

== Description ==

= Plugin's Official Site =
http://mapsmarker.com

* [Demo](http://www.mapsmarker.com/demo/) - [FAQ](http://www.mapsmarker.com/faq/) - [Docs](http://www.mapsmarker.com/docs/) - [Support](http://mapsmarker.com/support/) - [Github](https://github.com/robertharm/Leaflet-Maps-Marker) - [Donations](http://mapsmarker.com/donations) - [Twitter](http://twitter.com/mapsmarker) - [Facebook](http://facebook.com/mapsmarker)

= Main features = 

Leaflet Maps Marker allows you to

* pin your favorites places with markers,
* use integrated address search (Google Places API) for quickly finding your places,
* choose from up to 700 custom free map icons from [Maps Icons Collection](http://mapicons.nicolasmollet.com),
* add popup description text or images for each marker,
* choose individual basemap, size and zoom level for each marker/layer map,
* organize your markers in layers and
* show them thanks to the [Leaflet library from Cloudmade](http://leaflet.cloudmade.com/)
* by adding a shortcode (e.g. mapsmarker marker="1")] to posts or pages
* through OSM/OpenStreetMap, MapQuest, [OGD Vienna Maps](http://data.wien.gv.at)
* or any custom WMTS-map

to the visitors of your website.

= Additional features =

* show directions for your locations using Google Maps, yournavigation.org or openrouteservice.org
* configure up to 10 WMS servers to display additional information from external geodata providers (like the European Environment Agency) on your maps
* export your markers as KML file for displaying in Google Earth or Google Maps
* export your markers as GeoJSON file for embedding in external websites or apps
* export your markers as GeoRSS for embedding in external websites or apps
* export your markers as ARML for displaying in the augmented-reality browser from Wikitude
* export your markers as csv-file
* show standalone maps in fullscreen mode
* support for microformat geo-markup to make your maps machine-readable
* create QR code images for standalone maps in fullscreen mode
* automatically add meta-tags with location information to maps
* automatically add microformat geo-markup to maps
* option to set Wordpress roles (administrator, editor, author, contributor) which are allowed to add/edit/delete markers and layers
* option to add marker directly to posts or pages without saving them to your database
* audit log for changes on markers & layers (saving first created by/on and last updated by/on info)
* search within your marker list
* mass actions for markers (assignment to layers, deletions)
* option to reset plugin settings to defaults
* option to change the default shortcode '[mapsmarker...]'
* dynamic preview of maps in backend (no need to reload)
* WordPress Admin Bar integration to quickly access plugins features (can be disabled)
* global stats for marker/layer count on mapsmarker.com (can be disabled)
* "OGD Vienna selector": if a place within boundaries of Vienna/Austria is chosen, OGD Vienna basemaps are automatically selected
* integrated [donation links](http://www.mapsmarker.com/donations) to show your support for this plugin :-)

= Technical details =

* Wordpress Multisite compatibility
* full UTF8-support for cyrillic, chinese or other alphabets on marker/layername and marker popup text
* support for other languages through .po/.mo-files (please see http://mapsmarker.com/languages for details if you want to contribute a new translation)
* GeoJSON feeds for every marker and layer with [JSONP support](http://www.mapsmarker.com/geojson)
* use of Wordpress settings API for storing options
* TinyMCE editor on backend for editing popuptext
* version check for minimum Wordpress (3.0) and PHP (5.2) requirements
* use of prepared SQL statements to prevent SQL injections
* use of Wordpress nounces on forms to prevent attacks and input mistakes
* use of custom function names and enque plugin scripts/css only on plugin pages to prevent conflicts with other plugins
* update functions implemented for smooth updates of the plugin
* uninstall function to completely remove the plugin and its data (also on WordPress Multisite installations)

Please let me know which feature you think is missing by adding your ideas at [http://www.mapsmarker.com/ideas](http://www.mapsmarker.com/ideas)

= Available translations =

* English (en_US)
* German (de_DE)
* Japanes (ja) [Shu Higashi](http://twitter.com/higa4)

For more information on translations of the plugin and how to contribute a new translation, please visit [http://www.mapsmarker.com/languages](http://www.mapsmarker.com/languages).

= Leaflet Maps Marker Needs Your Support =

It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy using Leaflet Maps Marker - particularly within a commercial context - please consider [__making a donation__](http://www.mapsmarker.com/donations). Your donation will help keeping the plugin free for everyone and allow me to spend more time on developing, maintaining and support. I´d be happy to accept your donation! Thanks! [Robert Harm](http://www.harm.co.at)

== Installation ==

Note: plugin requires at least PHP 5.2 and Wordpress 3.0!

1. Upload leaflet-maps-marker folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Optional: change default settings (you are getting redirected to plugin settings page automatically on first activation)

After installation you will find a 'Leaflet Maps Marker' menu in your WordPress admin panel and in WordPress Admin Bar.
For basic usage and tutorials, you can also have a look at [http://www.mapsmarker.com/docs](http://www.mapsmarker.com/docs "Docs").

== Frequently Asked Questions ==

Do you have questions or issues with Leaflet Maps Marker? Please use these support channels appropriately:

1. [FAQ](www.mapsmarker.com/faq/)
2. [Docs](http://www.mapsmarker.com/docs/)
3. [Ideas (for feature requests)](www.mapsmarker.com/ideas/)
4. [Wordpress Support Forum](http://wordpress.org/tags/leaflet-maps-marker?forum_id=10) (free community support)
5. [WP Questions](http://wpquestions.com/affiliates/register/name/robertharm) (paid community support)
6. [WordPress HelpCenter](http://wphelpcenter.com/) (paid professional support)

[More info on support](http://mapsmarker.com/support/)

== Screenshots ==

For demo maps please visit [http://www.mapsmarker.com/demo](http://www.mapsmarker.com/demo).

1. Frontend: marker map (with open popup and image, basemap: OGD Vienna satellite, overlay: OGD Vienna addresses, controlbox: expanded)
2. Frontend: layer map (5 marker, different icons, basemap: OpenStreetMap, controlbox: collapsed)
3. Frontend: map with WMS layer enabled and additional marker
4. Frontend: layer map in Google Earth (via KML export)
5. Frontend: layer map in Google Maps (via KML export)
6. Frontend: showing marker in Wikitude (via Augmented-Reality API)
7. Backend: add/edit marker-screen - allows you to fully customize the marker map (used basemap & overlays, map size, zoom, controlbox status, marker icon, popup-text and behaviour etc).
8. Backend: add/edit layer - allows you to fully customize the layer map (used basemap & overlays, set center, map size, zoom, controlbox status etc).
9. Backend: markerlist - for easy administration of all your markers
10. Backend: layerlist - for easy administration of all your layers
11. Backend: plugin settings page allows you to easily set all necessary settings & restore the defaults if you messed something up
12. Backend: csv-export of all markers - just copy and paste into your favorite spreadsheet application for use in other applications
13. Backend: tools section - allows mass-actions more markers (assignements, deletions)

== Other Notes ==

= Licence =

Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a small donation for the time I've spent writing and supporting this plugin. Please see [http://www.mapsmarker.com/donations](http://www.mapsmarker.com/donations) for details.
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License included with this plugin for more details. 

= Licenses for used libraries, services and images =

* Leaflet - Copyright (c) 2010-2011, CloudMade, Vladimir Agafonkin [http://leaflet.cloudmade.com](http://leaflet.cloudmade.com)
* OpenStreetMap - The Free Wiki World Map: [OpenStreetMap License](http://wiki.openstreetmap.org/wiki/OpenStreetMap_License) 
* Map Icons Collection by Nicolas Mollet - [http://mapicons.nicolasmollet.com](http://mapicons.nicolasmollet.com)
* Datasource OGD Vienna maps: Stadt Wien, Creative Commons Attribution (by) [http://data.wien.gv.at](http://data.wien.gv.at)
* Adress autocompletion powered by [Google Places API](http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html)
* Map center icon [Joseph Wain](http://glyphish.com/) - Creative Commons Attribution (by)
* Plus, json & csv-export icon by [Yusuke Kamiyamane](http://www.pinvoke.com/) - Creative Commons Attribution (by)
* Question Mark Icon by [RandomJabber](http://www.randomjabber.com/)
= Credits & special thanks to =
* Sindre Wimberger ([http://www.sindre.at](http://www.sindre.at)) for help with bugfixing & geo-consulting
* Susanne Mandl ([http://www.greenflamingomedia.com](http://www.greenflamingomedia.com)) for plugin logo
* Wordpress-Settings-API-Class by Aliso the geek ([http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/](http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/))
* [Hind](http://www.nanodesu.ru) who originally release a basic [Leaflet plugin](https://wordpress.org/extend/plugins/leaflet/) which I used partly as a basis for Leaflet Maps Marker plugin

= Translations =

For more information on translations of the plugin and how to contribute a new translation, please visit [http://www.mapsmarker.com/languages](http://www.mapsmarker.com/languages).

== Upgrade Notice ==
= v1.4 =
added support for routing providers and more mass-actions for markers - see http://www.mapsmarker.com/v1.4 for more details

= v1.3 =
added marker mass actions and browser/template compatibility bugfixes - see http://www.mapsmarker.com/v1.3 for more details

= 1.2.2 =
Fix for custom marker icons not showing on certain hosting providers - see http://www.mapsmarker.com/v1.2.2 for more details

= 1.2.1 =
Important bugfixes - see http://www.mapsmarker.com/v1.2.1 for more details

= 1.2 =
Important bugfixes and new feature: GeoRSS-Support - see http://www.mapsmarker.com/v1.2 for more details

= 1.1 =
Added new features and bugfixes - see http://www.mapsmarker.com/v1.1 for more details

= 1.0 =
Initial release - see http://www.mapsmarker.com/v1.0 for more details

== Changelog ==

= v1.4 - 23.01.2012 =
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.4)
* NEW: added support for routing service from Google Maps
* NEW: added support for routing service from yournavigation.org
* NEW: added support for routing service from openrouteservice.org
* NEW: mass-actions for changing default values for existing markers (map size, icon, panel status, zoom, basemap...)
* CHANGED: panel status can now also be selected as column for marker/layer listing page
* CHANGED: controlbox status column for markers/layers list view now displays text instead of 0/1/2
* BUGFIX: method for adding markers/layers as some users reported that new markers/layers were not saved to database
* BUGFIX: method for plugin active-check as some users reported that API links did not work
* BUGFIX: marker/layer name in fullscreen panel did not support UTF8-characters
* BUGFIX: text width in tinymce editor was not the same as in popup text
* BUGFIX: several German translation text strings
* BUGFIX: markers added directly with shortcode caused error on frontend

= v1.3 - 17.01.2012 =
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.3)
* NEW: add mass actions for makers (assign markers to layer, delete markers)
* CHANGED: flattr now embedded as static image as long loadtimes decrease usability because Google Places scripts starts only afterwards
* CHANGED: marker-/layername for panel in backend now gets refreshed dynamically after entering in form field
* CHANGED: geo microformat tags are now also added to maps added directly via shortcode
* OPTIMIZED: div structure and order for maps on frontend
* BUGFIX: map/panel width were not the same due to css inheritance
* BUGFIX: map css partially broken in IE < 9 when viewing backend maps
* BUGFIX: links in maps were underlined on some templates
* BUGFIX: panel API link images had borders on some templates
* BUGFIX: text in layer controlbox was centered on some templates
* REMOVED: global stats for plugin installs, marker/layer edits and deletions
* REMOVED: featured sponsor in admin header
* REMOVED: developers comments from css- and js-files

= v1.2.2 - 14.01.2012 =
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.2.2)
* BUGFIX: custom marker icons were not shown on certain hosts due to different wp-upload-directories

= v1.2.1 - 13.01.2012 =
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.2.1)
* BUGFIX: plugin installation failed on certain hosting providers due to path/directory issues
* BUGFIX: (interactive) maps do not get display in RSS feeds (which is not possible), so now a static image with a link to the fullscreen standalone map is displayed
* BUGFIX: removed redundant slashes from paths
* BUGFIX: fullscreen maps did not get loaded if WordPress is installed in subdirectory
* BUGFIX: API images in panel did show a border on some templates

= v1.2 - 11.01.2012 =
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.2)
* NEW: added [GeoRSS-feeds for marker- and layer maps](http://www.mapsmarker.com/georss) (RSS 2.0 & ATOM 1.0)
* NEW: added microformat geo-markup to maps, to make your maps machine-readable
* CHANGE: Default custom overlay (OGD Vienna Addresses) is not active anymore by default on new markers/layers (but still gets active when an address through search by Google Places is selected)
* CHANGE: added attribution text for default custom overlay (OGD Vienna Addresses) to see if overlay has accidently been activated
* CHANGE: added sanitization for wikitude provider name 
* BUGFIX: plugin conflict with Google Analytics for WordPress resulting in maps not showing up
* BUGFIX: plugin did not work on several hosts as path to wp-load.php for API links could not be constructed
* BUGFIX: reset settings to default values did only reset values from v1.0
* BUGFIX: when default custom overlay for new markers/layers got unchecked, the map in backend did not show up anymore
* BUGFIX: fullscreen standalone maps didnt work in Internet Explorer
* BUGFIX: maps did not show up in Internet Explorer 7 at all
* BUGFIX: attribution box on standalone maps did not show up if windows size is too small
* BUGFIX: slashes were not stripped from marker/layer name on frontend maps
* BUGFIX: quotes were not shown on marker/layer names (note: double quotes are replaced with single quotes automatically due to compatibility reasons)

= v1.1 - 08.01.2012 =
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.1)
* NEW: [show standalone maps in fullscreen mode](http://www.mapsmarker.com/wp-content/plugins/leaflet-maps-marker/leaflet-fullscreen.php?marker=1)
* NEW: [create QR code images for standalone maps in fullscreen mode](https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=http://www.mapsmarker.com/wp-content/plugins/leaflet-maps-marker/leaflet-fullscreen.php?marker=1)
* NEW: API links (KML, GeoJSON, Fullscreen, QR Code, Wikitude) now only work if plugin is active
* NEW: German translation
* NEW: Japanese translation thanks to Shu Higashi ([@higa4](http://twitter.com/higa4))
* NEW: option to show/hide WMS layer legend link
* NEW: option to disable global statistics
* CHANGED: added more default marker icons, based on the top 100 icons from the Map Icons Collection
* CHANGED: added attribution text field in settings for custom overlays
* CHANGED: removed settings for Wikitude debug lon/lat -> now marker lat/lon respectively layer center lat/lon are used when Wikitude API links are called without explicit parameters &latitude= and &longitude=
* CHANGED: default setting fields can now be changed by focusing with mouse click
* CHANGED: added icons to API links on backend for better usability
* BUGFIX: dynamic preview of marker/layer panel in backend not working as designed
* BUGFIX: language pot-file didn´t include all text strings for translations
* BUGFIX: active translations made setting tabs unaccessible

= v1.0 - 01.01.2012 = 
* [Blog post with more details about this release](http://www.mapsmarker.com/v1.0)
* NEW: Initial release