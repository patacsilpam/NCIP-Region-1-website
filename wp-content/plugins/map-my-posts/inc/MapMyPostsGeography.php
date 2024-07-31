<?php
/**
 * Country and region geographic tools.
 *
 * @package	MapMyPosts
 * @author	Erik Fantasia <erik@aroundthisworld.com>
 * @license	GPL-2.0+
 * @link	http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright	2013 Erik Fantasia
 */

if ( !defined( 'MAPMYPOSTS_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

class MapMyPostsGeography {
	
	/**
	 * Find UN region ID given a list of country codes.
	 * 
	 * Used to pick best region (zoom) of Google Geochart.
	 * See: https://developers.google.com/chart/interactive/docs/gallery/geochart#Continent_Hierarchy
	 *
	 * @since    1.0
	 *
	 * @param    array    $countries    Array of ISO 3166-1 country abbreviations
	 * 
	 * @return   string   UN region ID
	 */
	public static function find_region( $countries = array() ) {
		if ( count( $countries ) == 1 ) {
			// antarctica does not work as a country/region
			if ($countries[0] != 'AQ') {
				return $countries[0];
			}
		}
		$region = array();
		foreach ( $countries as $country ) {
			if (in_array($country, array('DZ', 'EG', 'EH', 'LY', 'MA', 'SD', 'TN'))) {
				// Northern Africa - 015
				$region['015'] = 1;
			} elseif (in_array($country, array('BF', 'BJ', 'CI', 'CV', 'GH', 'GM', 'GN', 'GW', 'LR', 'ML', 'MR', 'NE', 'NG', 'SH', 'SL', 'SN', 'TG'))) {
				// Western Africa - 011
				$region['011'] = 1;
			} elseif (in_array($country, array('AO' ,'CD' ,'ZR' ,'CF' ,'CG' ,'CM' ,'GA' ,'GQ' ,'ST' ,'TD'))) {
				// Middle Africa - 017
				$region['017'] = 1;
			} elseif (in_array($country, array('BI' ,'DJ' ,'ER' ,'ET' ,'KE' ,'KM' ,'MG' ,'MU' ,'MW' ,'MZ' ,'RE' ,'RW' ,'SC' ,'SO' ,'TZ' ,'UG' ,'YT' ,'ZM' ,'ZW'))) {
				// Eastern Africa - 014
				$region['014'] = 1;
			} elseif (in_array($country, array('BW', 'LS', 'NA', 'SZ', 'ZA'))) {
				// Southern Africa - 018
				$region['018'] = 1;
			} elseif (in_array($country, array('GG', 'JE', 'AX', 'DK', 'EE', 'FI', 'FO', 'GB', 'IE', 'IM', 'IS', 'LT', 'LV', 'NO', 'SE', 'SJ'))) {
				// Northern Europe - 154
				$region['154'] = 1;
			} elseif (in_array($country, array('AT', 'BE', 'CH', 'DE', 'DD', 'FR', 'FX', 'LI', 'LU', 'MC', 'NL'))) {
				// Western Europe - 155
				$region['155'] = 1;
			} elseif (in_array($country, array('BG', 'BY', 'CZ', 'HU', 'MD', 'PL', 'RO', 'RU', 'SU', 'SK', 'UA'))) {
				// Eastern Europe - 151
				$region['151'] = 1;
			} elseif (in_array($country, array('AD', 'AL', 'BA', 'ES', 'GI', 'GR', 'HR', 'IT', 'ME', 'MK', 'MT', 'CS', 'RS', 'PT', 'SI', 'SM', 'VA', 'YU'))) {
				// Southern Europe - 039
				$region['039'] = 1;
			} elseif (in_array($country, array('BM', 'CA', 'GL', 'PM', 'US'))) {
				// Northern America - 021
				$region['021'] = 1;
			} elseif (in_array($country, array('AG', 'AI', 'AN', 'AW', 'BB', 'BL', 'BS', 'CU', 'DM', 'DO', 'GD', 'GP', 'HT', 'JM', 'KN', 'KY', 'LC', 'MF', 'MQ', 'MS', 'PR', 'TC', 'TT', 'VC', 'VG', 'VI'))) {
				// Caribbean - 029
				$region['029'] = 1;
			} elseif (in_array($country, array('BZ', 'CR', 'GT', 'HN', 'MX', 'NI', 'PA', 'SV'))) {
				// Central America - 013
				$region['013'] = 1;
			} elseif (in_array($country, array('AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'PE', 'PY', 'SR', 'UY', 'VE'))) {
				// South America - 005
				$region['005'] = 1;
			} elseif (in_array($country, array('TM', 'TJ', 'KG', 'KZ', 'UZ'))) {
				// Central Asia - 143
				$region['143'] = 1;
			} elseif (in_array($country, array('CN', 'HK', 'JP', 'KP', 'KR', 'MN', 'MO', 'TW'))) {
				// Eastern Asia - 030
				$region['030'] = 1;
			} elseif (in_array($country, array('AF', 'BD', 'BT', 'IN', 'IR', 'LK', 'MV', 'NP', 'PK'))) {
				// Southern Asia - 034
				$region['034'] = 1;
			} elseif (in_array($country, array('BN', 'ID', 'KH', 'LA', 'MM', 'BU', 'MY', 'PH', 'SG', 'TH', 'TL', 'TP', 'VN'))) {
				// South-Eastern - 035
				$region['035'] = 1;
			} elseif (in_array($country, array('AE', 'AM', 'AZ', 'BH', 'CY', 'GE', 'IL', 'IQ', 'JO', 'KW', 'LB', 'OM', 'PS', 'QA', 'SA', 'NT', 'SY', 'TR', 'YE', 'YD'))) {
				// Western - 145
				$region['145'] = 1;
			} elseif (in_array($country, array('AU', 'NF', 'NZ'))) {
				// Australia and New Zealand - 053
				$region['053'] = 1;
			} elseif (in_array($country, array('FJ', 'NC', 'PG', 'SB', 'VU'))) {
				// Melanesia - 054
				$region['054'] = 1;
			} elseif (in_array($country, array('FM', 'GU', 'KI', 'MH', 'MP', 'NR', 'PW'))) {
				// Micronesia - 057
				$region['057'] = 1;
			} elseif (in_array($country, array('AS', 'CK', 'NU', 'PF', 'PN', 'TK', 'TO', 'TV', 'WF', 'WS'))) {
				// Polynesia - 061
				$region['061'] = 1;
			} else {
				$region['world'] = 1;
			}
		}
		$region = array_keys( $region );
		if ( count( $region ) == 1 ) {
			return $region[0];
		}
		$region2 = array();
		foreach ( $region as $key ) {
			if (in_array($key, array('015', '011', '017', '014', '018'))) {
				// Africa - 002
				$region2['002'] = 1;
			} elseif (in_array($key, array('154', '155', '151', '039'))) {
				// Europe - 150
				$region2['150'] = 1;
			} elseif (in_array($key, array('021', '029', '013', '005'))) {
				// Americas - 019
				$region2['019'] = 1;
			} elseif (in_array($key, array('143', '030', '034', '035', '145'))) {
				// Asia - 142
				$region2['142'] = 1;
			} elseif (in_array($key, array('053', '054', '057', '061'))) {
				// Oceania - 009
				$region2['009'] = 1;
			} else {
				$region2['world'] = 1;
			}
		}
		$region2 = array_keys( $region2 );
		if ( count( $region2 ) == 1 ) {
			return $region2[0];
		}
		return "world";
	}
	
	/**
	 * Get all ISO 3166-1 countries.
	 * 
	 * ISO 3166-1 list of countries compatible with Google GeoChart regions, along with lat/lng to use for markers.
	 * Some locations (eg, Antarctica) require fudging to work with GeoCharts.
	 * 
	 * Source for most of these: http://www.ohadpr.com/2010/04/countries-approximate-lat-lon-and-iso-3166-1-alpha-2/index.html
	 *
	 * @since    1.0
	 *
	 * @return   array    Returns the full array with country code as key, value as an array with: lat, lng, name
	 */
	public static function get_countries() {
		$countries = array(
			'AF' => array(33.93911,67.709953,__('Afghanistan', 'map-my-posts')),
			'AX' => array(37.0625,-95.677068,__('Åland Islands', 'map-my-posts')),
			'AL' => array(41.153332,20.168331,__('Albania', 'map-my-posts')),
			'DZ' => array(28.033886,1.659626,__('Algeria', 'map-my-posts')),
			'AS' => array(-14.270972,-170.132217,__('American Samoa', 'map-my-posts')),
			'AD' => array(42.546245,1.601554,__('Andorra', 'map-my-posts')),
			'AO' => array(-11.202692,17.873887,__('Angola', 'map-my-posts')),
			'AI' => array(18.220554,-63.068615,__('Anguilla', 'map-my-posts')),
			'AQ' => array(-69.81914,-65.138509,__('Antarctica', 'map-my-posts')), // location not available as geochart region and requires tweaking for geochart marker 
			'AG' => array(17.060816,-61.796428,__('Antigua and Barbuda', 'map-my-posts')),
			'AR' => array(-38.416097,-63.616672,__('Argentina', 'map-my-posts')),
			'AM' => array(40.069099,45.038189,__('Armenia', 'map-my-posts')),
			'AW' => array(12.52111,-69.968338,__('Aruba', 'map-my-posts')),
			'AU' => array(-25.274398,133.775136,__('Australia', 'map-my-posts')),
			'AT' => array(47.516231,14.550072,__('Austria', 'map-my-posts')),
			'AZ' => array(40.143105,47.576927,__('Azerbaijan', 'map-my-posts')),
			'BS' => array(25.03428,-77.39628,__('Bahamas', 'map-my-posts')),
			'BH' => array(25.930414,50.637772,__('Bahrain', 'map-my-posts')),
			'BD' => array(23.684994,90.356331,__('Bangladesh', 'map-my-posts')),
			'BB' => array(13.193887,-59.543198,__('Barbados', 'map-my-posts')),
			'BY' => array(53.709807,27.953389,__('Belarus', 'map-my-posts')),
			'BE' => array(50.503887,4.469936,__('Belgium', 'map-my-posts')),
			'BZ' => array(17.189877,-88.49765,__('Belize', 'map-my-posts')),
			'BJ' => array(9.30769,2.315834,__('Benin', 'map-my-posts')),
			'BM' => array(32.321384,-64.75737,__('Bermuda', 'map-my-posts')),
			'BT' => array(27.514162,90.433601,__('Bhutan', 'map-my-posts')),
			'BO' => array(-16.290154,-63.588653,__('Bolivia, Plurinational State of', 'map-my-posts')),
			'BQ' => array(12.2128600,-68.294450,__('Bonaire, Sint Eustatius and Saba', 'map-my-posts')),
			'BA' => array(43.915886,17.679076,__('Bosnia and Herzegovina', 'map-my-posts')),
			'BW' => array(-22.328474,24.684866,__('Botswana', 'map-my-posts')),
			'BV' => array(-54.423199,3.413194,__('Bouvet Island', 'map-my-posts')),
			'BR' => array(-14.235004,-51.92528,__('Brazil', 'map-my-posts')),
			'IO' => array(-6.343194,71.876519,__('British Indian Ocean Territory', 'map-my-posts')),
			'BN' => array(4.535277,114.727669,__('Brunei Darussalam', 'map-my-posts')),
			'BG' => array(42.733883,25.48583,__('Bulgaria', 'map-my-posts')),
			'BF' => array(12.238333,-1.561593,__('Burkina Faso', 'map-my-posts')),
			'BI' => array(-3.373056,29.918886,__('Burundi', 'map-my-posts')),
			'KH' => array(12.565679,104.990963,__('Cambodia', 'map-my-posts')),
			'CM' => array(7.369722,12.354722,__('Cameroon', 'map-my-posts')),
			'CA' => array(56.130366,-106.346771,__('Canada', 'map-my-posts')),
			'CV' => array(16.002082,-24.013197,__('Cape Verde', 'map-my-posts')),
			'KY' => array(19.513469,-80.566956,__('Cayman Islands', 'map-my-posts')),
			'CF' => array(6.611111,20.939444,__('Central African Republic', 'map-my-posts')),
			'TD' => array(15.454166,18.732207,__('Chad', 'map-my-posts')),
			'CL' => array(-35.675147,-71.542969,__('Chile', 'map-my-posts')),
			'CN' => array(35.86166,104.195397,__('China', 'map-my-posts')),
			'CX' => array(-10.447525,105.690449,__('Christmas Island', 'map-my-posts')),
			'CC' => array(37.0625,-95.677068,__('Cocos (Keeling) Islands', 'map-my-posts')),
			'CO' => array(4.570868,-74.297333,__('Colombia', 'map-my-posts')),
			'KM' => array(-11.875001,43.872219,__('Comoros', 'map-my-posts')),
			'CG' => array(-0.228021,15.827659,__('Congo', 'map-my-posts')),
			'CD' => array(-0.228021,15.827659,__('Congo, the Democratic Republic of the', 'map-my-posts')),
			'CK' => array(-21.236736,-159.777671,__('Cook Islands', 'map-my-posts')),
			'CR' => array(9.748917,-83.753428,__('Costa Rica', 'map-my-posts')),
			'CI' => array(7.539989,-5.54708,__('Côte d\'Ivoire', 'map-my-posts')),
			'HR' => array(45.1,15.2,__('Croatia', 'map-my-posts')),
			'CU' => array(21.521757,-77.781167,__('Cuba', 'map-my-posts')),
			'CW' => array(12.1833,69.0000,__('Curaçao', 'map-my-posts')),
			'CY' => array(35.126413,33.429859,__('Cyprus', 'map-my-posts')),
			'CZ' => array(49.817492,15.472962,__('Czech Republic', 'map-my-posts')),
			'DK' => array(56.26392,9.501785,__('Denmark', 'map-my-posts')),
			'DJ' => array(11.825138,42.590275,__('Djibouti', 'map-my-posts')),
			'DM' => array(15.414999,-61.370976,__('Dominica', 'map-my-posts')),
			'DO' => array(18.735693,-70.162651,__('Dominican Republic', 'map-my-posts')),
			'EC' => array(-1.831239,-78.183406,__('Ecuador', 'map-my-posts')),
			'EG' => array(26.820553,30.802498,__('Egypt', 'map-my-posts')),
			'SV' => array(13.794185,-88.89653,__('El Salvador', 'map-my-posts')),
			'GQ' => array(1.650801,10.267895,__('Equatorial Guinea', 'map-my-posts')),
			'ER' => array(15.179384,39.782334,__('Eritrea', 'map-my-posts')),
			'EE' => array(58.595272,25.013607,__('Estonia', 'map-my-posts')),
			'ET' => array(9.145,40.489673,__('Ethiopia', 'map-my-posts')),
			'FK' => array(-51.796253,-59.523613,__('Falkland Islands (Malvinas)', 'map-my-posts')),
			'FO' => array(61.892635,-6.911806,__('Faroe Islands', 'map-my-posts')),
			'FJ' => array(-16.578193,179.414413,__('Fiji', 'map-my-posts')),
			'FI' => array(61.92411,25.748151,__('Finland', 'map-my-posts')),
			'FR' => array(46.227638,2.213749,__('France', 'map-my-posts')),
			'GF' => array(3.933889,-53.125782,__('French Guiana', 'map-my-posts')),
			'PF' => array(-17.679742,-149.406843,__('French Polynesia', 'map-my-posts')),
			'TF' => array(37.0625,-95.677068,__('French Southern Territories', 'map-my-posts')),
			'GA' => array(-0.803689,11.609444,__('Gabon', 'map-my-posts')),
			'GM' => array(13.443182,-15.310139,__('Gambia', 'map-my-posts')),
			'GE' => array(32.157435,-82.907123,__('Georgia', 'map-my-posts')),
			'DE' => array(51.165691,10.451526,__('Germany', 'map-my-posts')),
			'GH' => array(7.946527,-1.023194,__('Ghana', 'map-my-posts')),
			'GI' => array(36.137741,-5.345374,__('Gibraltar', 'map-my-posts')),
			'GR' => array(39.074208,21.824312,__('Greece', 'map-my-posts')),
			'GL' => array(71.706936,-42.604303,__('Greenland', 'map-my-posts')),
			'GD' => array(12.262776,-61.604171,__('Grenada', 'map-my-posts')),
			'GP' => array(16.995971,-62.067641,__('Guadeloupe', 'map-my-posts')),
			'GU' => array(13.444304,144.793731,__('Guam', 'map-my-posts')),
			'GT' => array(15.783471,-90.230759,__('Guatemala', 'map-my-posts')),
			'GG' => array(49.465691,-2.585278,__('Guernsey', 'map-my-posts')),
			'GN' => array(9.945587,-9.696645,__('Guinea', 'map-my-posts')),
			'GW' => array(11.803749,-15.180413,__('Guinea-Bissau', 'map-my-posts')),
			'GY' => array(4.860416,-58.93018,__('Guyana', 'map-my-posts')),
			'HT' => array(18.971187,-72.285215,__('Haiti', 'map-my-posts')),
			'HM' => array(-53.08181,73.504158,__('Heard Island and McDonald Islands', 'map-my-posts')),
			'VA' => array(37.0625,-95.677068,__('Holy See (Vatican City State)', 'map-my-posts')),
			'HN' => array(15.199999,-86.241905,__('Honduras', 'map-my-posts')),
			'HK' => array(22.396428,114.109497,__('Hong Kong', 'map-my-posts')),
			'HU' => array(47.162494,19.503304,__('Hungary', 'map-my-posts')),
			'IS' => array(64.963051,-19.020835,__('Iceland', 'map-my-posts')),
			'IN' => array(20.593684,78.96288,__('India', 'map-my-posts')),
			'ID' => array(-6.224014,106.7980,__('Indonesia', 'map-my-posts')),
			'IR' => array(32.427908,53.688046,__('Iran, Islamic Republic of', 'map-my-posts')),
			'IQ' => array(33.223191,43.679291,__('Iraq', 'map-my-posts')),
			'IE' => array(53.41291,-8.24389,__('Ireland', 'map-my-posts')),
			'IM' => array(54.236107,-4.548056,__('Isle of Man', 'map-my-posts')),
			'IL' => array(31.046051,34.851612,__('Israel', 'map-my-posts')),
			'IT' => array(41.87194,12.56738,__('Italy', 'map-my-posts')),
			'JM' => array(18.109581,-77.297508,__('Jamaica', 'map-my-posts')),
			'JP' => array(36.204824,138.252924,__('Japan', 'map-my-posts')),
			'JE' => array(49.214439,-2.13125,__('Jersey', 'map-my-posts')),
			'JO' => array(30.585164,36.238414,__('Jordan', 'map-my-posts')),
			'KZ' => array(48.019573,66.923684,__('Kazakhstan', 'map-my-posts')),
			'KE' => array(-0.023559,37.906193,__('Kenya', 'map-my-posts')),
			'KI' => array(-3.370417,-168.734039,__('Kiribati', 'map-my-posts')),
			'KP' => array(35.907757,127.766922,__('Korea, Democratic People\'s Republic of', 'map-my-posts')),
			'KR' => array(35.907757,127.766922,__('Korea, Republic of', 'map-my-posts')),
			'KW' => array(29.31166,47.481766,__('Kuwait', 'map-my-posts')),
			'KG' => array(41.20438,74.766098,__('Kyrgyzstan', 'map-my-posts')),
			'LA' => array(19.85627,102.495496,__('Lao People\'s Democratic Republic', 'map-my-posts')),
			'LV' => array(56.879635,24.603189,__('Latvia', 'map-my-posts')),
			'LB' => array(33.854721,35.862285,__('Lebanon', 'map-my-posts')),
			'LS' => array(-29.609988,28.233608,__('Lesotho', 'map-my-posts')),
			'LR' => array(6.428055,-9.429499,__('Liberia', 'map-my-posts')),
			'LY' => array(37.0625,-95.677068,__('Libya', 'map-my-posts')),
			'LI' => array(47.166,9.555373,__('Liechtenstein', 'map-my-posts')),
			'LT' => array(55.169438,23.881275,__('Lithuania', 'map-my-posts')),
			'LU' => array(49.815273,6.129583,__('Luxembourg', 'map-my-posts')),
			'MO' => array(22.198745,113.543873,__('Macao', 'map-my-posts')),
			'MK' => array(41.608635,21.745275,__('Macedonia, The Former Yugoslav Republic of', 'map-my-posts')),
			'MG' => array(-18.766947,46.869107,__('Madagascar', 'map-my-posts')),
			'MW' => array(-13.254308,34.301525,__('Malawi', 'map-my-posts')),
			'MY' => array(4.210484,101.975766,__('Malaysia', 'map-my-posts')),
			'MV' => array(3.202778,73.22068,__('Maldives', 'map-my-posts')),
			'ML' => array(17.570692,-3.996166,__('Mali', 'map-my-posts')),
			'MT' => array(35.937496,14.375416,__('Malta', 'map-my-posts')),
			'MH' => array(7.131474,171.184478,__('Marshall Islands', 'map-my-posts')),
			'MQ' => array(14.641528,-61.024174,__('Martinique', 'map-my-posts')),
			'MR' => array(21.00789,-10.940835,__('Mauritania', 'map-my-posts')),
			'MU' => array(-20.348404,57.552152,__('Mauritius', 'map-my-posts')),
			'YT' => array(-12.8275,45.166244,__('Mayotte', 'map-my-posts')),
			'MX' => array(23.634501,-102.552784,__('Mexico', 'map-my-posts')),
			'FM' => array(7.425554,150.550812,__('Micronesia, Federated States of', 'map-my-posts')),
			'MD' => array(47.411631,28.369885,__('Moldova, Republic of', 'map-my-posts')),
			'MC' => array(43.750298,7.412841,__('Monaco', 'map-my-posts')),
			'MN' => array(46.862496,103.846656,__('Mongolia', 'map-my-posts')),
			'ME' => array(42.708678,19.37439,__('Montenegro', 'map-my-posts')),
			'MS' => array(16.742498,-62.187366,__('Montserrat', 'map-my-posts')),
			'MA' => array(31.791702,-7.09262,__('Morocco', 'map-my-posts')),
			'MZ' => array(-18.665695,35.529562,__('Mozambique', 'map-my-posts')),
			'MM' => array(21.913965,95.956223,__('Myanmar', 'map-my-posts')),
			'NA' => array(-22.95764,18.49041,__('Namibia', 'map-my-posts')),
			'NR' => array(-0.522778,166.931503,__('Nauru', 'map-my-posts')),
			'NP' => array(28.394857,84.124008,__('Nepal', 'map-my-posts')),
			'NL' => array(52.132633,5.291266,__('Netherlands', 'map-my-posts')),
			'AN' => array(12.226079,-69.060087,__('Netherlands Antilles', 'map-my-posts')),
			'NC' => array(-20.904305,165.618042,__('New Caledonia', 'map-my-posts')),
			'NZ' => array(-40.900557,174.885971,__('New Zealand', 'map-my-posts')),
			'NI' => array(12.865416,-85.207229,__('Nicaragua', 'map-my-posts')),
			'NE' => array(17.607789,8.081666,__('Niger', 'map-my-posts')),
			'NG' => array(9.081999,8.675277,__('Nigeria', 'map-my-posts')),
			'NU' => array(-19.054445,-169.867233,__('Niue', 'map-my-posts')),
			'NF' => array(-29.040835,167.954712,__('Norfolk Island', 'map-my-posts')),
			'MP' => array(17.33083,145.38469,__('Northern Mariana Islands', 'map-my-posts')),
			'NO' => array(60.472024,8.468946,__('Norway', 'map-my-posts')),
			'OM' => array(21.512583,55.923255,__('Oman', 'map-my-posts')),
			'PK' => array(30.375321,69.345116,__('Pakistan', 'map-my-posts')),
			'PW' => array(7.51498,134.58252,__('Palau', 'map-my-posts')),
			'PS' => array(42.094445,17.266614,__('Palestine, State of', 'map-my-posts')),
			'PA' => array(8.537981,-80.782127,__('Panama', 'map-my-posts')),
			'PG' => array(-6.314993,143.95555,__('Papua New Guinea', 'map-my-posts')),
			'PY' => array(-23.442503,-58.443832,__('Paraguay', 'map-my-posts')),
			'PE' => array(-9.189967,-75.015152,__('Peru', 'map-my-posts')),
			'PH' => array(12.879721,121.774017,__('Philippines', 'map-my-posts')),
			'PN' => array(-24.703615,-127.439308,__('Pitcairn', 'map-my-posts')),
			'PL' => array(51.919438,19.145136,__('Poland', 'map-my-posts')),
			'PT' => array(39.399872,-8.224454,__('Portugal', 'map-my-posts')),
			'PR' => array(18.220833,-66.590149,__('Puerto Rico', 'map-my-posts')),
			'QA' => array(25.354826,51.183884,__('Qatar', 'map-my-posts')),
			'RE' => array(-21.115141,55.536384,__('Réunion', 'map-my-posts')),
			'RO' => array(45.943161,24.96676,__('Romania', 'map-my-posts')),
			'RU' => array(61.52401,105.318756,__('Russian Federation', 'map-my-posts')),
			'RW' => array(-1.940278,29.873888,__('Rwanda', 'map-my-posts')),
			'BL' => array(37.0625,-95.677068,__('Saint Barthélemy', 'map-my-posts')),
			'SH' => array(-24.143474,-10.030696,__('Saint Helena, Ascension and Tristan da Cunha', 'map-my-posts')),
			'KN' => array(17.357822,-62.782998,__('Saint Kitts and Nevis', 'map-my-posts')),
			'LC' => array(13.909444,-60.978893,__('Saint Lucia', 'map-my-posts')),
			'MF' => array(43.589046,5.885031,__('Saint Martin (French part)', 'map-my-posts')),
			'PM' => array(46.941936,-56.27111,__('Saint Pierre and Miquelon', 'map-my-posts')),
			'VC' => array(12.984305,-61.287228,__('Saint Vincent and the Grenadines', 'map-my-posts')),
			'WS' => array(-13.759029,-172.104629,__('Samoa', 'map-my-posts')),
			'SM' => array(43.94236,12.457777,__('San Marino', 'map-my-posts')),
			'ST' => array(0.18636,6.613081,__('Sao Tome and Principe', 'map-my-posts')),
			'SA' => array(23.885942,45.079162,__('Saudi Arabia', 'map-my-posts')),
			'SN' => array(14.497401,-14.452362,__('Senegal', 'map-my-posts')),
			'RS' => array(44.016521,21.005859,__('Serbia', 'map-my-posts')),
			'SC' => array(-4.679574,55.491977,__('Seychelles', 'map-my-posts')),
			'SL' => array(8.460555,-11.779889,__('Sierra Leone', 'map-my-posts')),
			'SG' => array(1.352083,103.819836,__('Singapore', 'map-my-posts')),
			'SX' => array(18.0167,63.0500,__('Sint Maarten (Dutch part)', 'map-my-posts')),
			'SK' => array(48.669026,19.699024,__('Slovakia', 'map-my-posts')),
			'SI' => array(46.151241,14.995463,__('Slovenia', 'map-my-posts')),
			'SB' => array(-9.64571,160.156194,__('Solomon Islands', 'map-my-posts')),
			'SO' => array(5.152149,46.199616,__('Somalia', 'map-my-posts')),
			'ZA' => array(-30.559482,22.937506,__('South Africa', 'map-my-posts')),
			'GS' => array(-54.429579,-36.587909,__('South Georgia and the South Sandwich Islands', 'map-my-posts')),
			'SS' => array(4.8500,31.6000,__('South Sudan', 'map-my-posts')),
			'ES' => array(40.463667,-3.74922,__('Spain', 'map-my-posts')),
			'LK' => array(7.873054,80.771797,__('Sri Lanka', 'map-my-posts')),
			'SD' => array(12.862807,30.217636,__('Sudan', 'map-my-posts')),
			'SR' => array(12.862807,30.217636,__('Suriname', 'map-my-posts')),
			'SJ' => array(77.553604,23.670272,__('Svalbard and Jan Mayen', 'map-my-posts')),
			'SZ' => array(-26.522503,31.465866,__('Swaziland', 'map-my-posts')),
			'SE' => array(60.128161,18.643501,__('Sweden', 'map-my-posts')),
			'CH' => array(46.818188,8.227512,__('Switzerland', 'map-my-posts')),
			'SY' => array(34.802075,38.996815,__('Syrian Arab Republic', 'map-my-posts')),
			'TW' => array(23.69781,120.960515,__('Taiwan, Province of China', 'map-my-posts')),
			'TJ' => array(38.861034,71.276093,__('Tajikistan', 'map-my-posts')),
			'TZ' => array(-6.369028,34.888822,__('Tanzania, United Republic of', 'map-my-posts')),
			'TH' => array(15.870032,100.992541,__('Thailand', 'map-my-posts')),
			'TL' => array(-8.874217,125.727539,__('Timor-Leste', 'map-my-posts')),
			'TG' => array(8.619543,0.824782,__('Togo', 'map-my-posts')),
			'TK' => array(-8.967363,-171.855881,__('Tokelau', 'map-my-posts')),
			'TO' => array(-21.178986,-175.198242,__('Tonga', 'map-my-posts')),
			'TT' => array(10.691803,-61.222503,__('Trinidad and Tobago', 'map-my-posts')),
			'TN' => array(33.886917,9.537499,__('Tunisia', 'map-my-posts')),
			'TR' => array(38.963745,35.243322,__('Turkey', 'map-my-posts')),
			'TM' => array(38.969719,59.556278,__('Turkmenistan', 'map-my-posts')),
			'TC' => array(21.694025,-71.797928,__('Turks and Caicos Islands', 'map-my-posts')),
			'TV' => array(-7.109535,177.64933,__('Tuvalu', 'map-my-posts')),
			'UG' => array(1.373333,32.290275,__('Uganda', 'map-my-posts')),
			'UA' => array(48.379433,31.16558,__('Ukraine', 'map-my-posts')),
			'AE' => array(23.424076,53.847818,__('United Arab Emirates', 'map-my-posts')),
			'GB' => array(55.378051,-3.435973,__('United Kingdom', 'map-my-posts')),
			'US' => array(37.09024,-95.712891,__('United States', 'map-my-posts')),
			'UM' => array(5.8833,-162.0832,__('United States Minor Outlying Islands', 'map-my-posts')),
			'UY' => array(-32.522779,-55.765835,__('Uruguay', 'map-my-posts')),
			'UZ' => array(41.377491,64.585262,__('Uzbekistan', 'map-my-posts')),
			'VU' => array(-15.376706,166.959158,__('Vanuatu', 'map-my-posts')),
			'VE' => array(6.42375,-66.58973,__('Venezuela, Bolivarian Republic of', 'map-my-posts')),
			'VN' => array(14.058324,108.277199,__('Viet Nam', 'map-my-posts')),
			'VG' => array(18.335765,-64.896335,__('Virgin Islands, British', 'map-my-posts')),
			'VI' => array(18.335765,-64.896335,__('Virgin Islands, U.S.', 'map-my-posts')),
			'WF' => array(-13.768752,-177.156097,__('Wallis and Futuna', 'map-my-posts')),
			'EH' => array(24.215527,-12.885834,__('Western Sahara', 'map-my-posts')),
			'YE' => array(15.552727,48.516388,__('Yemen', 'map-my-posts')),
			'ZM' => array(-13.133897,27.849332,__('Zambia', 'map-my-posts')),
			'ZW' => array(-19.015438,29.154857,__('Zimbabwe', 'map-my-posts')),
		);
		return $countries;
	}
	
	/**
	 * Get data for a single country code.
	 * 
	 * @since    1.0
	 *
	 * @return   array    Returns array for single country code with: lat, lon, name
	 */
	public static function get_country( $code ) {
		$countries = self::get_countries();
		return $countries[$code];
	}
	
	/**
	 * Guess country code given a country name.
	 *
	 * @since    1.0
	 *
	 * @param    string    $name    Name of country
	 *
	 * @return   mixed     Returns string of country code if matched or false if no match.
	 */
	public static function guess_country( $name ) {
		$name = trim( strtolower( $name ) );
		// check for some common "unofficial" english substitutions first
		$subs = array(
			'oz'		=> 'AU',
			'brasil'	=> 'BR',
			'columbia'	=> 'CO',
			'macau'		=> 'MO',
			'korea'		=> 'KR',
			'south korea'	=> 'KR',
			'north korea'	=> 'KP',
			'laos'		=> 'LA',
			'burma'		=> 'MM',
			'russia'	=> 'RU',
			'syria'		=> 'SY',
			'vietnam'	=> 'VN',
		);
		if ( in_array( $name, array_keys( $subs ) ) ) {
			return $subs[$name];
		}
		$countries = self::get_countries();
		foreach ( $countries as $key => $ary ) {
			if ($key == $name) {
				return $key;
			}
			if ( preg_match( "|\b$name\b|i", $ary[2] ) ) {
				return $key;
			}
		}
		return false;
	}
	
	/**
	 * Geocode an address using Google Maps API.
	 *
	 * @since    1.0
	 *
	 * @param    string    $address    Input address to be geocoded.
	 *
	 * @return   string    Raw JSON string for AJAX - json_decode() as necessary.
	 */
	public static function geocode_json( $address ) {
		$json = file_get_contents( MAPMYPOSTS_REQUEST_PROTOCOL . 'maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . urlencode( $address ) );
		return $json;
	}
}
