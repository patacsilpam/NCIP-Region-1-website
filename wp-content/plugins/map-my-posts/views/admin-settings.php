<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   MapMyPosts
 * @author    Erik Fantasia <erik@aroundthisworld.com>
 * @license   GPL-2.0+
 * @link      http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright 2013 Erik Fantasia
 */
?>
<div class="wrap">
	<div style="float:right;margin:5px 0;">
		<a href="http://www.aroundthisworld.com" target="_blank"><img align="right" src="<?php echo MAPMYPOSTS_URL; ?>/images/atw_globe_32x32.png" width="32" height="32" alt="" /></a>
		<p style="float:left;margin: 0 10px;">
		<strong>Map My Posts <?php _e('is brought to you for free by', 'map-my-posts'); ?> <a href="http://www.aroundthisworld.com" target="_blank">Around This World</a>!</strong><br />
		<?php printf( __('Please link to our %1$sblog%2$s, follow us on %3$sTwitter%4$s, or like us on %5$sFacebook%6$s.', 'map-my-posts'), '<a href="http://www.aroundthisworld.com" target="_blank">', '</a>', '<a href="http://twitter.com/heatherik" target="_blank">', '</a>', '<a href="http://www.facebook.com/aroundthisworld" target="_blank">', '</a>'); ?>
		</p>
	</div>
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<form method="post" action="options.php">
	<?php settings_fields( 'mmp-settings-group' ); // use option group set up with register_setting() ?>
	<p><?php printf( __('For more information on these settings and to learn more about Map My Posts, %1$sclick here%2$s.', 'map-my-posts'), '<a href="http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/" target="_blank">', '</a>'); ?></p>
	<h3>API Key</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_api_key"><?php _e('Google API Key', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[api_key]" id="<?php echo $settings_key; ?>_api_key" value="<?php echo esc_attr( $settings['api_key'] ); ?>" class="regular-text" />
				<p class="description">
				<?php _e('An API key is recommended, but not required, to use the courtesy limit of 25,000 Google Map and Google Static Map requests per day.', 'map-my-posts'); ?><br />
				<?php printf( __('Google API keys are available through the %1$sAPIs console%2$s.', 'map-my-posts'), '<a href="https://code.google.com/apis/console" target="_blank">', '</a>'); ?>
				</p>
			</td>
		</tr>
	</table>
	<h3>Default Settings</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_region"><?php _e('Region', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[region]" id="<?php echo $settings_key; ?>_taxonomy">
					<option value="auto" <?php selected('auto', $settings['region']); ?>><?php _e('Auto', 'map-my-posts'); ?></option>
					<option value="world" <?php selected('world', $settings['region']); ?>><?php _e('World', 'map-my-posts'); ?></option>
				</select>
				<p class="description">
					<?php _e('If the region is set to Auto, the map or geochart will automatically zoom to the most appropriate region.'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_taxonomy"><?php _e('Taxonomy', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[taxonomy]" id="<?php echo $settings_key; ?>_taxonomy">
					<option value="category" <?php selected('category', $settings['taxonomy']); ?>><?php _e('Categories', 'map-my-posts'); ?></option>
					<option value="post_tag" <?php selected('post_tag', $settings['taxonomy']); ?>><?php _e('Tags', 'map-my-posts'); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<h3>Default Map Settings</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_width"><?php _e('Width', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[width]" id="<?php echo $settings_key; ?>_width" value="<?php echo esc_attr( $settings['width'] ); ?>" class="small-text" />
				<p class="description">
					<?php _e('Examples: auto, 100%, 250px', 'map-my-posts'); ?>
					<?php _e('This setting does not affect Static Google Maps, which always have a 300px default width.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_height"><?php _e('Height', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[height]" id="<?php echo $settings_key; ?>_height" value="<?php echo esc_attr( $settings['height'] ); ?>" class="small-text" />
				<p class="description">
					<?php _e('If width is set to auto or percentage value, a px value height must be used.', 'map-my-posts'); ?>
					<?php _e('This setting does not affect Static Google Maps, which always have a 250px default height.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_maptype"><?php _e('Map Type', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[maptype]" id="<?php echo $settings_key; ?>_maptype">
					<option value="ROADMAP" <?php selected('ROADMAP', $settings['maptype']); ?>><?php _e('ROADMAP', 'map-my-posts'); ?></option>
					<option value="SATELLITE" <?php selected('SATELLITE', $settings['maptype']); ?>><?php _e('SATELLITE', 'map-my-posts'); ?></option>
					<option value="HYBRID" <?php selected('HYBRID', $settings['maptype']); ?>><?php _e('HYBRID', 'map-my-posts'); ?></option>
					<option value="TERRAIN" <?php selected('TERRAIN', $settings['maptype']); ?>><?php _e('TERRAIN', 'map-my-posts'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_maptypecontrol"><?php _e('Map Type Control', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[maptypecontrol]" id="<?php echo $settings_key; ?>_maptypecontrol">
					<option value="false" <?php selected('false', $settings['maptypecontrol']); ?>><?php _e('Hide', 'map-my-posts'); ?></option>
					<option value="true" <?php selected('true', $settings['maptypecontrol']); ?>><?php _e('Show', 'map-my-posts'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_streetviewcontrol"><?php _e('Street View Control', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[streetviewcontrol]" id="<?php echo $settings_key; ?>_streetviewcontrol">
					<option value="false" <?php selected('false', $settings['streetviewcontrol']); ?>><?php _e('Hide', 'map-my-posts'); ?></option>
					<option value="true" <?php selected('true', $settings['streetviewcontrol']); ?>><?php _e('Show', 'map-my-posts'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_infowindow"><?php _e('Info Window', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[infowindow]" id="<?php echo $settings_key; ?>_infowindow">
					<option value="false" <?php selected('false', $settings['infowindow']); ?>><?php _e('Hide', 'map-my-posts'); ?></option>
					<option value="true" <?php selected('true', $settings['infowindow']); ?>><?php _e('Show', 'map-my-posts'); ?></option>
				</select>
				<p class="description">
					<?php _e('Shown after clicking a marker on a Google Map.', 'map-my-posts'); ?>
					<?php _e('The info window is not available for widths less than 250px and heights less than 200px.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_linktext"><?php _e('Link Text', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[linktext]" id="<?php echo $settings_key; ?>_linktext" value="<?php echo esc_attr( $settings['linktext'] ); ?>" class="regular-text" />
				<p class="description">
					<?php _e('The link text to show in the info window after clicking a marker on a Google Map.'); ?>
					<?php _e('If you leave this blank, no link to the category or tag will be shown.'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_target"><?php _e('Link Target', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[target]" id="<?php echo $settings_key; ?>_target">
					<option value="_self" <?php selected('_self', $settings['target']); ?>><?php _e('Same Window (_self)', 'map-my-posts'); ?></option>
					<option value="_blank" <?php selected('_blank', $settings['target']); ?>><?php _e('New Window (_blank)', 'map-my-posts'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_markercolor"><?php _e('Marker Color', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[markercolor]" id="<?php echo $settings_key; ?>_markercolor">
					<option value="red" <?php selected('red', $settings['markercolor']); ?>><?php _e('Red', 'map-my-posts'); ?></option>
					<option value="purple" <?php selected('purple', $settings['markercolor']); ?>><?php _e('Purple', 'map-my-posts'); ?></option>
					<option value="blue" <?php selected('blue', $settings['markercolor']); ?>><?php _e('Blue', 'map-my-posts'); ?></option>
					<option value="green" <?php selected('green', $settings['markercolor']); ?>><?php _e('Green', 'map-my-posts'); ?></option>
					<option value="yellow" <?php selected('yellow', $settings['markercolor']); ?>><?php _e('Yellow', 'map-my-posts'); ?></option>
					<option value="orange" <?php selected('orange', $settings['markercolor']); ?>><?php _e('Orange', 'map-my-posts'); ?></option>
				</select>
				<p class="description">
					<?php _e('Geochart markers and regions are defined using the Minimum and Maximum Color controls below.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_markersize"><?php _e('Marker Size', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[markersize]" id="<?php echo $settings_key; ?>_markersize">
					<option value="normal" <?php selected('normal', $settings['markersize']); ?>><?php _e('Normal', 'map-my-posts'); ?></option>
					<option value="small" <?php selected('small', $settings['markersize']); ?>><?php _e('Small', 'map-my-posts'); ?></option>
				</select>
				<p class="description">
					<?php _e('The marker size only applies to Static Google Maps.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
	</table>
	<h3>Default Geochart Settings</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_mode"><?php _e('Geochart Mode', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[mode]" id="<?php echo $settings_key; ?>_mode">
					<option value="region" <?php selected('region', $settings['mode']); ?>><?php _e('region', 'map-my-posts'); ?></option>
					<option value="marker" <?php selected('marker', $settings['mode']); ?>><?php _e('marker', 'map-my-posts'); ?></option>
				</select>
				<p class="description">
					<?php _e('Select region mode to fill countries or marker mode to show proportionally-sized markers.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_tooltip"><?php _e('Tooltip', 'map-my-posts'); ?></label></th>
			<td>
				<select name="<?php echo $settings_key; ?>[tooltip]" id="<?php echo $settings_key; ?>_tooltip">
					<option value="false" <?php selected('false', $settings['tooltip']); ?>><?php _e('Hide', 'map-my-posts'); ?></option>
					<option value="true" <?php selected('true', $settings['tooltip']); ?>><?php _e('Show', 'map-my-posts'); ?></option>
				</select>
				<p class="description">
					<?php _e('Shown when hovering over a Geochart region or marker.', 'map-my-posts'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_min_color"><?php _e('Minimum Color', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[min_color]" id="<?php echo $settings_key; ?>_min_color" value="<?php echo esc_attr( $settings['min_color'] ); ?>" class="color-field" />
				<div id="mincolorpicker"></div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_max_color"><?php _e('Maximum Color', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[max_color]" id="<?php echo $settings_key; ?>_max_color" value="<?php echo esc_attr( $settings['max_color'] ); ?>" class="color-field" />
				<div id="maxcolorpicker"></div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_background"><?php _e('Background Color', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[background]" id="<?php echo $settings_key; ?>_background" value="<?php echo esc_attr( $settings['background'] ); ?>" class="color-field" />
				<div id="bgcolorpicker"></div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $settings_key; ?>_empty_color"><?php _e('Empty Country Color', 'map-my-posts'); ?></label></th>
			<td>
				<input type="text" name="<?php echo $settings_key; ?>[empty_color]" id="<?php echo $settings_key; ?>_empty_color" value="<?php echo esc_attr( $settings['empty_color'] ); ?>" class="color-field" />
				<div id="emptycolorpicker"></div>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
	</form>
	
	<strong><?php _e('Show us the love!', 'map-my-posts'); ?></strong>
	<div style="margin:7px 0;vertical-align:middle;">
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=111410372213067";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<a href="https://twitter.com/heatherik" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @heatherik</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	&nbsp;&nbsp; <div class="fb-like" data-href="http://www.facebook.com/aroundthisworld" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false"></div>
	</div>
</div>
