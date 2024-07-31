jQuery(document).ready(function($) {
	var geocoder;
	var map;
	var marker;
	var lastAddress = '';
	
	function initializeGeocoder() {
		geocoder = new google.maps.Geocoder();
		map = new google.maps.Map(document.getElementById('mmp-map-canvas'));
		marker = new google.maps.Marker({
			visible: false,
			draggable: true,
			map: map
		});
		var lat = $('input#mmp_lat').val();
		var lng = $('input#mmp_lng').val()
		
		clearMap();
		
		if ($('select#mmp_country').val()) {
			codeCountry();
		} else if (lat && lng) {
			var latlng = new google.maps.LatLng(lat, lng)
			marker.setPosition(latlng);
			marker.setVisible(true);
			map.setCenter(latlng);
			if ($('input#mmp_zoom')) {
				map.setZoom(parseInt($('input#mmp_zoom').val()));
			} else {
				map.setZoom(5);
			}
			$('input#mmp_address').val(lat + ', ' + lng);
		} else if ($('input#mmp_guess').val()) {
			// map the guessed country value (WP admin notification in the view)
			var country = $('input#mmp_guess').val();
			$('select#mmp_country').val(country).attr('selected',true);
			codeCountry();
		}
		google.maps.event.addListener(marker, 'dragend', dragMarker);
	}
	
	function clearMap() {
		var latlng = new google.maps.LatLng(20, 0);
		var mapOptions = {
			zoom: 1,
			center: latlng,
			streetViewControl: false,
			mapTypeControl: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map.setOptions(mapOptions);
		marker.setVisible(false);
	}
	
	function codeAddress() {
		var address = $('input#mmp_address').val();
		var bounds = null;
		geocoder.geocode( { 'address': address }, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			var latlng = results[0].geometry.location;
			var lat = results[0].geometry.location.lat();
			var lng = results[0].geometry.location.lng();
			var bounds = results[0].geometry.viewport;
			marker.setPosition(latlng);
			marker.setVisible(true);
			//marker.setAnimation(google.maps.Animation.DROP);
			map.setCenter(latlng);
			map.fitBounds(bounds);
			$('input#mmp_zoom').val(map.getZoom());
			$('input#mmp_lat').val(lat);
			$('input#mmp_lng').val(lng);
			$('input#mmp_geo_address').val(results[0].formatted_address);
			$('input#mmp_geo_city').val('');
			$('input#mmp_geo_state').val('');
			$('input#mmp_geo_country').val('');
			// extract address details
			setGeo(results[0].address_components);
			// if first part of address_component type is country, set the mmp_country value
			if (results[0].address_components[0].types[0] == 'country') {
				var country = results[0].address_components[0].short_name;
				$('select#mmp_country').val(country).attr('selected',true);
			} else if (results[0].address_components[0].types[0] == 'establishment' && results[0].address_components[0].short_name == 'Antarctica') {
				// fudge for Antarctica (AQ)
				var country = 'AQ';
				map.setCenter(latlng);
				map.setZoom(2);
				$('select#mmp_country').val(country).attr('selected',true);
			} else {
				// this is not a country - can only be used as a marker
				$('select#mmp_country').val('').attr('selected',true);
			}
		} else {
			if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
				alert(mmp_text.not_found);
			} else {
				alert(mmp_text.general_error);
			}
		}
		});
	}
	
	function codeCountry() {
		var country = $('select#mmp_country').val();
		if (country) {
			// Google seems to think SV is Switzerland!
			if (country == "SV") {
				country = "El Salvador";
			}
			geocoder.geocode( { 'address': 'country:'+country }, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var latlng = results[0].geometry.location;
					var lat = results[0].geometry.location.lat();
					var lng = results[0].geometry.location.lng();
					var bounds = results[0].geometry.viewport;
					marker.setPosition(latlng);
					marker.setVisible(true);
					//marker.setAnimation(google.maps.Animation.DROP);
					map.setCenter(latlng);
					// once again, antarctica giving problems!
					if (country == "AQ") {
						map.setZoom(2);
					} else {
						map.fitBounds(bounds);
					}
					$('input#mmp_zoom').val(map.getZoom());
					$('input#mmp_lat').val(lat);
					$('input#mmp_lng').val(lng);
					$('input#mmp_address').val('');
					$('input#mmp_geo_address').val(results[0].formatted_address);
					setGeo(results[0].address_components);
				}
			});
		}
	}
	
	function dragMarker() {
		var lat = marker.getPosition().lat();
		var lng = marker.getPosition().lng();
		$('input#mmp_lat').val(lat);
		$('input#mmp_lng').val(lng);
		$('input#mmp_address').val(lat + ', ' + lng);
		$('input#mmp_geo_address').val('');
		$('input#mmp_geo_city').val('');
		$('input#mmp_geo_state').val('');
		$('input#mmp_geo_country').val('');
		$('select#mmp_country').val('').attr('selected',true);
		// reverse geocode the marker position and set the hidden geo fields
		geocoder.geocode( { 'latLng': marker.getPosition() }, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				setGeo(results[0].address_components);
			}
		});

	}
	
	function setGeo(address_components) {
		// populate other geo fields, if available
		$.each(address_components, function(i, address_component) {
			if (address_component.types[0] == 'locality') {
				$('input#mmp_geo_city').val(address_component.long_name);
			} else if (address_component.types[0] == 'administrative_area_level_1') {
				$('input#mmp_geo_state').val(address_component.short_name);
			} else if (address_component.types[0] == 'country') {
				$('input#mmp_geo_country').val(address_component.short_name);
			}
		});
	}
	
	google.setOnLoadCallback(initializeGeocoder);
	
	$('select#mmp_country').change(function() {
		codeCountry();
	});
	// do not submit form on enter key in geocode box
	$('input#mmp_address').keypress(function(e) {
		if ( e.which == 13 ) {
			codeAddress();
			return false;
		}
		return true;
	});
	$('input#mmp_address').focus().select();
	
	$('input#mmp_address').click(function() {
		lastAddress = $('input#mmp_address').val();
		$('input#mmp_address').val('');
	});
	
	$('input#mmp_address').blur(function() {
		if ($('input#mmp_address').val() == '' && marker.getVisible()) {
			$('input#mmp_address').val(lastAddress);
		}
	});
	
	$('input#mmp_geocode_button').click(function() {
		codeAddress();
	});
	$('input#mmp_clear_button').click(function() {
		$('select#mmp_country').val('').attr('selected',true);
		$('input#mmp_address').val('');
		$('input#mmp_zoom').val('');
		$('input#mmp_lat').val('');
		$('input#mmp_lng').val('');
		$('input#mmp_geo_address').val('');
		$('input#mmp_geo_city').val('');
		$('input#mmp_geo_state').val('');
		$('input#mmp_geo_country').val('');
		clearMap();
	});
});
