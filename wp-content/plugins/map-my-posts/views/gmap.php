<?php
/**
 * Represents the view for the Google Map
 *
 * @package   MapMyPosts
 * @author    Erik Fantasia <erik@aroundthisworld.com>
 * @license   GPL-2.0+
 * @link      http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright 2013 Erik Fantasia
 */
?>
<!-- Begin Map My Posts Google Map -->
<?php if ( self::$gmapcount == 1 ): ?>
<style type="text/css">
div.mmp-gmap-container img{max-width: inherit;}
</style>
<?php endif; // end self::$gmapcount == 1 ?>
<script type="text/javascript">
function MMPGmap<?php echo self::$gmapcount ?>() {
	var locations = [ <?php
	// format the marker table data
	$rows = array();
	foreach ( $term_list as $ary ) {
		// we need to get the lat/lon data
		if ( $ary["latlng"] ) {
			// $ary["latlng"] is actually a formatted "%f,%f" string
			$rows[] = sprintf("['%s', %s, %d, '%s']", esc_attr($ary["name"]), $ary["latlng"], $ary["count"], esc_attr($ary["url"]));
		}
	}
	echo join( ', ', $rows );
	?> ];
	var map = new google.maps.Map(document.getElementById('mmp-gmap<?php echo self::$gmapcount ?>'), {
		zoom: <?php echo $zoom; ?>,
		center: new google.maps.LatLng(0, 0),
		panControl: <?php echo strtolower($pancontrol); ?>,
		streetViewControl: <?php echo strtolower($streetviewcontrol); ?>,
		mapTypeControl: <?php echo strtolower($maptypecontrol); ?>,
		mapTypeId: google.maps.MapTypeId.<?php echo strtoupper($maptype); ?>
	});
	
	var infowindow = new google.maps.InfoWindow();
	
	var latlng, marker, infocontent, i;
	
	var bounds = new google.maps.LatLngBounds ();
	
	for (i = 0; i < locations.length; i++) {
		latlng = new google.maps.LatLng(locations[i][1], locations[i][2]);
		marker = new google.maps.Marker({
			position: latlng,
			<?php if ( in_array( strtolower( $markercolor ), array( 'purple', 'blue', 'green', 'yellow', 'orange' ) ) ) { ?>
			icon: '<?php echo MAPMYPOSTS_REQUEST_PROTOCOL; ?>maps.google.com/mapfiles/ms/micons/<?php echo strtolower( $markercolor ) ?>-dot.png',
			shadow: '<?php echo MAPMYPOSTS_REQUEST_PROTOCOL; ?>maps.google.com/mapfiles/shadow50.png',
			<?php } ?>
			map: map
		});
		bounds.extend(latlng);
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
			return function() {
				<?php if ($infowindow): ?>
				infocontent  = '<strong style="font-size:14px;">' + locations[i][0] + '</strong><br />';
				infocontent += '<?php _e('Posts:', 'map-my-posts'); ?> <strong>' + locations[i][3] + '</strong>';
				<?php if ($linktext): ?>infocontent += '<br /><a href="' + locations[i][4] + '" target="<?php echo $target; ?>"><?php echo esc_attr($linktext); ?></a>';<?php endif; ?>
				infowindow.setContent(infocontent);
				infowindow.open(map, marker);
				<?php else: // if !$infowindow ?>
				window.open(locations[i][4], '<?php echo esc_attr($target); ?>');
				<?php endif; ?>
			}
		})(marker, i));
	}
	
<?php if ( strtolower( $region ) == "auto" && count( $rows ) ): ?>
	map.fitBounds(bounds);
<?php endif; ?>
}
</script>
<div class="mmp-gmap-container" id="mmp-gmap<?php echo self::$gmapcount; ?>" style="width:<?php echo esc_attr($width); ?>;height:<?php echo esc_attr($height); ?>;"></div>
<!-- End Map My Posts Google Map -->