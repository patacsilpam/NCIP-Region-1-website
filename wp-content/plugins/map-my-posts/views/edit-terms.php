<?php
/**
 * Additional form fields shown when adding or editing a category or tag.
 *
 * @package   MapMyPosts
 * @author    Erik Fantasia <erik@aroundthisworld.com>
 * @license   GPL-2.0+
 * @link      http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright 2013 Erik Fantasia
 */
?>
<tr class="form-field">
	<th valign="top" scope="row">
		<label for="mmp_country"><?php _e('Country', 'map-my-posts'); ?></label>
	</th>
	<td>
		<?php if ($country_guess): ?>
		<div class="updated">
			<p><strong><?php _e('Map My Posts has automatically selected a country for this category based upon its name.', 'map-my-posts'); ?></strong></p>
			<p><?php _e('You must hit the update button to save this association. You can adjust or clear the selection below.', 'map-my-posts' ); ?></strong></p>
		</div>
		<?php endif; ?>
		<select name="mmp_country" id="mmp_country">
			<option value=""><?php _e('None', 'map-my-posts'); ?></option>
<?php foreach ( $countries as $key => $ary ): ?>
			<option value="<?php echo esc_attr($key); ?>" <?php echo selected($key, $data['country'], false); ?>><?php echo esc_html( __( $ary[2], 'map-my-posts' ) ); ?></option>
<?php endforeach; ?>
		</select>
		<p class="description"><?php  _e('Associate a country with this category to be used by', 'map-my-posts'); ?> <a href="<?php echo MAPMYPOSTS_PLUGIN_URI; ?>" target="_blank"><?php echo MAPMYPOSTS_PLUGIN_NAME; ?></a></p>
	</td>
</tr>
<tr class="form-field">
	<th valign="top" scope="row">
		<label for="mmp_address"><?php _e('Location', 'map-my-posts'); ?></label>
	</th>
	<td>
		<input type="hidden" name="mmp_guess" id="mmp_guess" value="<?php echo esc_attr($country_guess); ?>">
		<input type="hidden" name="mmp_zoom" id="mmp_zoom" value="<?php echo esc_attr($data['zoom']); ?>">
		<input type="hidden" name="mmp_lat" id="mmp_lat" value="<?php echo esc_attr($data['lat']); ?>">
		<input type="hidden" name="mmp_lng" id="mmp_lng" value="<?php echo esc_attr($data['lng']); ?>">
		<input type="hidden" name="mmp_geo_address" id="mmp_geo_address" value="<?php echo esc_attr($data['address']); ?>">
		<input type="hidden" name="mmp_geo_city" id="mmp_geo_city" value="<?php echo esc_attr($data['city']); ?>">
		<input type="hidden" name="mmp_geo_state" id="mmp_geo_state" value="<?php echo esc_attr($data['state']); ?>">
		<input type="hidden" name="mmp_geo_country" id="mmp_geo_country" value="<?php echo esc_attr($data['country']); ?>">
		<input id="mmp_address" type="text" value="" style="max-width:375px;">
		<input id="mmp_geocode_button" type="button" class="button button-secondary" value="<?php _e('Locate', 'map-my-posts'); ?>" style="width:auto;">
		<input id="mmp_clear_button" type="button" class="button button-secondary" value="<?php _e('Clear Marker', 'map-my-posts'); ?>" style="width:auto;">
		<div id="mmp-map-canvas"></div>
		<p class="description" id="mmp-map-description">
		<?php _e('Enter a location in the box and click the locate button. You can drag the marker to any arbitrary point.'); ?>
		<?php _e('If your selection is not a country, the country box will show None and you will not able to plot this location in region mode.', 'map-my-posts'); ?>
		<?php _e('For more information please visit:', 'map-my-posts'); ?>
		<a href="<?php echo MAPMYPOSTS_PLUGIN_URI; ?>" target="_blank"><?php echo MAPMYPOSTS_PLUGIN_NAME; ?></a></p>
	</td>
</tr>
