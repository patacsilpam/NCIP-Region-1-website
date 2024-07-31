<?php
/**
 * Represents the view for the Google Geo Chart
 *
 * @package   MapMyPosts
 * @author    Erik Fantasia <erik@aroundthisworld.com>
 * @license   GPL-2.0+
 * @link      http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright 2013 Erik Fantasia
 */
?>
<!-- Begin Map My Posts Geochart -->
<?php if ( self::$geochartcount == 1 ): ?>
<style type="text/css">
div.mmp-geochart-container circle:hover { cursor:pointer; }
div.mmp-geochart-container path:not([fill^="<?php echo strtolower( $empty_color ); // CSS uses all lower hex color values ?>"]):hover { cursor:pointer; }
</style>
<?php endif; // end self::$geochartcount == 1 ?>
<script type="text/javascript">
function MMPGeochart<?php echo self::$geochartcount ?>() {
	var data = new google.visualization.DataTable();
	var geochart = new google.visualization.GeoChart(document.getElementById('mmp-geochart<?php echo self::$geochartcount ?>'));
<?php
// show the region geochart using country codes
if ( strtolower( $mode ) == 'region' ):
?>
	data.addColumn('string', 'Country');
	data.addColumn('number', '<?php _e('Posts', 'map-my-posts') ?>');
	data.addColumn('string', 'Name');
	data.addColumn('string', 'URL');
	data.addRows([ <?php
	// format the region table data
	$rows = array();
	foreach ( $term_list as $ary ) {
		// region geocharts can ONLY contain country names
		$rows[] = sprintf("['%s', %d, '%s', '%s']", esc_attr($ary["country"]), $ary["count"], esc_attr($ary["name"]), esc_attr($ary["url"]));
	}
	echo join( ', ', $rows );
	?> ]);
	google.visualization.events.addListener(geochart, 'select', function() {
		var selectIdx = geochart.getSelection()[0].row;
		var countryUrl = data.getValue(selectIdx, 3);
		window.open(countryUrl, '<?php esc_attr($target) ?>');
	});
	var formatter = new google.visualization.PatternFormat('{1}');
	formatter.format(data, [0, 2]);
	var view = new google.visualization.DataView(data);
	view.setColumns([0, 1]);
<?php
// if not region, show marker geochart with lat/lng
else:
?>
	data.addColumn('number', 'Lat');
	data.addColumn('number', 'Lon');
	data.addColumn('string', 'Name');
	data.addColumn('number', '<?php esc_attr_e('Posts', 'map-my-posts'); ?>');
	data.addColumn('string', 'URL');
	data.addRows([ <?php
	// format the marker table data
	$rows = array();
	foreach ( $term_list as $ary ) {
		// we need to get the lat/lon data
		if ( $ary["latlng"] ) {
			// $ary["latlng"] is actually a formatted "%f,%f" string
			$rows[] = sprintf("[%s, '%s', %d, '%s']", $ary["latlng"], esc_attr($ary["name"]), $ary["count"], esc_attr($ary["url"]));
		}
	}
	echo join( ', ', $rows );
	?> ]);
	google.visualization.events.addListener(geochart, 'select', function() {
		var selectIdx = geochart.getSelection()[0].row;
		var countryUrl = data.getValue(selectIdx, 4);
		window.open(countryUrl, '<?php echo esc_attr($target); ?>');
	});
	var view = new google.visualization.DataView(data); view.setColumns([0, 1, 2, 3]);
<?php endif; ?>
	var options = {
		region: '<?php echo esc_attr($region); ?>',
		width: '<?php echo esc_attr($width); ?>',
		height: '<?php echo esc_attr($height); ?>',
		displayMode: '<?php echo esc_attr($mode); ?>',
		backgroundColor: {fill: '<?php echo esc_attr($background); ?>', stroke: '<?php echo esc_attr($border_color); ?>', strokeWidth: '<?php echo esc_attr($border_width); ?>'},
		datalessRegionColor: '<?php echo esc_attr($empty_color); ?>',
		colorAxis: {colors: ['<?php echo esc_attr($min_color); ?>', '<?php echo esc_attr($max_color); ?>']},
		tooltip: {textStyle: {color: '<?php echo esc_attr($text_color); ?>', fontName: '<?php echo esc_attr($text_font); ?>', fontSize: <?php echo esc_attr($text_size); ?>}, trigger: '<?php echo esc_attr($tooltip); ?>'},
		legend: '<?php echo esc_attr($legend); ?>',
		keepAspectRatio: true
		};
	geochart.draw(view, options);
}
</script>
<div class="mmp-geochart-container" id="mmp-geochart<?php echo self::$geochartcount; ?>" <?php echo $div_style; ?>></div>
<!-- End Map My Posts Geochart -->