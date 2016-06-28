jQuery(document).ready(function(){
	var $ = jQuery;
	var events = {
		event_view: function(){
			var col = parseInt($(this).attr('data-column'));
			var action;
			switch($(this).val()){
				case 'portfolio':
					action = 'select_event_portfolio';
				break;
				case 'calendar':
					action = 'select_event_calendar';
				break;
				case 'slider':
					action = 'select_event_slider';
				break;
			}
			
			data = {
				action	: action,
				num_col : col,
			};

			$.getJSON(ajaxurl, data, function(response){
				$('#cpevent_view_'+col).empty();
				$('#cpevent_view_'+col).append(response.data);
			});
		}
	};
	$('.calendar-bgcolor-colorpick').wpColorPicker();
	$('.cpevent-form-when').cpdatepicker();
	$('#cpevent-no-location').change(function(){
		if( $('#cpevent-no-location').is(':checked') ){
			$('#cpevent-location-data').hide();
		}else{
			$('#cpevent-location-data').show();
		}
	}).trigger('change');
	$('#cpevent-map').cpgmaps();
	$(document).on('cp_press_refresh_content_types', function(){
		$('.cpevent-selectview').bind('change', events.event_view);
	});
	$('.cpevent-selectview').change(events.event_view);
});

function cp_google_maps(){
	jQuery('#cpevent-map').cpgmaps();
}

(function($){
	
	var CpDatePicker = function (element){
		that = this;
		this.$element  = $(element);
		this.optionsTime = {
			show24Hours: true,
			step:15
		}
		this.optionsDate = {
			firstDay: '1',
			dateFormat: 'dd/mm/yy'
		}
	}
	
	CpDatePicker.prototype.date = function($el){
		that = this
		var datepicker_vals = {changeMonth: true, changeYear: true, firstDay : this.optionsDate.firstDay, yearRange:'-100:+10' };
		if( this.optionsDate.dateFormat ) datepicker_vals.dateFormat = this.optionsDate.dateFormat;
		$(document).triggerHandler('cpevent_datepicker', datepicker_vals);
		if( $el.length > 0 ){
			//apply datepickers to elements
			$el.find('.cpevent-date-input').each(function(i,dateInput){
				//init the datepicker
				var dateInput = $(dateInput);
				var dateValue = dateInput;
				var dateValue_value = dateValue.val();
				dateInput.datepicker(datepicker_vals);
				dateInput.datepicker('option', 'altField', dateValue);
				//now set the value
				if( dateValue_value ){
					dateInput.val(dateValue_value);
				}
				//add logic for texts
			});
			//deal with date ranges
			$el.filter('.cpevent-date-range').find('.cpevent-date-input').each(function(i,dateInput){
				//finally, apply start/end logic to this field
				dateInput = $(dateInput);
				if( dateInput.hasClass('cpevent-date-start') ){
					dateInput.datepicker('option','onSelect', function( selectedDate ) {
						//get corresponding end date input, we expect ranges to be contained in .em-date-range with a start/end input element
						var startDate = $(this);
						var endDate = startDate.parents('.cpevent-date-range').find('.cpevent-date-end');
						if( startDate.val() > endDate.val() && endDate.val() != '' ){
							endDate.datepicker( "setDate" , selectedDate );
						}
						endDate.datepicker( "option", 'minDate', selectedDate );
					});
				}else if( dateInput.hasClass('cpevent-date-end') ){
					var startInput = dateInput.parents('.cpevent-date-range').find('.cpevent-date-start');
					if( startInput.val() != '' ){
						dateInput.datepicker('option', 'minDate', startInput.val());
					}
				}
			});
		}
	}
	
	CpDatePicker.prototype.time = function($el){
		that = this;
		$el.timePicker(this.options);
		// Keep the duration between the two inputs.
		this.$element.find(".cpevent-time-start").each( function(i, input){
			$(input).data('oldTime', $.timePicker(input).getTime());
		}).change( function() {
			var start = $(this);
			var end = start.nextAll('.cpevent-time-end');
			if (end.val()) { // Only update when second input has a value.
				// Calculate duration.
				var oldTime = start.data('oldTime');
				var duration = ($.timePicker(end).getTime() - oldTime);
				var time = $.timePicker(start).getTime();
				if( $.timePicker(end).getTime() >= oldTime ){
					// Calculate and update the time in the second input.
					$.timePicker(end).setTime(new Date(new Date(time.getTime() + duration)));
				}
				start.data('oldTime', time); 
			}
		});
		// Validate.
		this.$element.find(".cpevent-time-end").change(function() {
			var end = $(this);
			var start = end.prevAll('.cpevent-time-start');
			if( start.val() ){
				if( $.timePicker(start).getTime() > $.timePicker(this).getTime() && ( that.$element.find('.cpevent-date-end').val().length == 0 || that.$element.find('.cpevent-date-start').val() == that.$element.find('.cpevent-date-end').val() ) ) { end.addClass("error"); }
				else { end.removeClass("error"); }
			}
		});
		//Sort out all day checkbox
		this.$element.find('.cpevent-time-all-day').change(function(){
			var allday = $(this);
			if( allday.is(':checked') ){
				allday.siblings('.cpevent-time-input').css('background-color','#ccc');
			}else{
				allday.siblings('.cpevent-time-input').css('background-color','#fff');
			}
		}).trigger('change');
	}
	
	$.fn.cpdatepicker = function(){
		return this.each(function ()
		{
			var datepicker = new CpDatePicker(this);
			$(this).find('.cpevent-date-range').each(function(){
				datepicker.date($(this));
			});
			$(this).find('.cpevent-time-input').each(function(){
				datepicker.time($(this));
			});
		})
	};

	$.fn.cpdatepicker.Constructor = CpDatePicker;
}(jQuery));

(function($){
	
	var CpGMaps = function (element){
		that = this;
		this.$element  = $(element);
		this.$element404 = $('#'+$(element).attr('id')+'-404');
		this.maps = {};
		this.maps_markers = {};
		this.infowindow = null;
	}
	
	CpGMaps.prototype.showMap = function(){
		that = this;
		//Find all the maps on this page
		$('.cpevent-location-map').each( function(index){
			el = $(this);
			var map_id = el.attr('id').replace('cpevent-location-map-','');
			em_LatLng = new google.maps.LatLng( $('#cpevent-location-map-coords-'+map_id+' .lat').text(), $('#cpevent-location-map-coords-'+map_id+' .lng').text());
			that.maps[map_id] = new google.maps.Map( document.getElementById('cpevent-location-map-'+map_id), {
				zoom: 14,
				center: em_LatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: false
			});
			that.maps_markers[map_id] = new google.maps.Marker({
				position: em_LatLng,
				map: maps[map_id]
			});
			that.infowindow = new google.maps.InfoWindow({ content: $('#cpevent-location-map-info-'+map_id+' .cpeventy-map-balloon').get(0) });
			that.infowindow.open(maps[map_id],maps_markers[map_id]);
			that.maps[map_id].panBy(40,-70);

			//JS Hook for handling map after instantiation
			$(document).triggerHandler('cpevent_maps_location_hook', [that.maps[map_id], that.infowindow, that.maps_markers[map_id], map_id]);
			//map resize listener
			$(window).on('resize', function(e) {
				google.maps.event.trigger(that.maps[map_id], "resize");
				that.maps[map_id].setCenter(that.maps_markers[map_id].getPosition());
				that.maps[map_id].panBy(40,-70);
			});
		});
		$('.cpevent-locations-map').each( function(index){
			var el = $(this);
			var map_id = el.attr('id').replace('cpevent-locations-map-','');
			var em_data = $.parseJSON( jQuery('#cpevent-locations-map-coords-'+map_id).text() );
			$.getJSON(document.URL, em_data , function(data){
				if(data.length > 0){
					  var myOptions = {
						mapTypeId: google.maps.MapTypeId.ROADMAP
					  };
					  maps[map_id] = new google.maps.Map(document.getElementById("cpevent-locations-map-"+map_id), myOptions);
					  maps_markers[map_id] = [];

					  var minLatLngArr = [0,0];
					  var maxLatLngArr = [0,0];

					  for (var i = 0; i < data.length; i++) {
						  if( !(data[i].location_latitude == 0 && data[i].location_longitude == 0) ){
							var latitude = parseFloat( data[i].location_latitude );
							var longitude = parseFloat( data[i].location_longitude );
							var location = new google.maps.LatLng( latitude, longitude );
							var marker = new google.maps.Marker({
								position: location, 
								map: maps[map_id]
							});
							maps_markers[map_id].push(marker);
							marker.setTitle(data[i].location_name);
							var myContent = '<div class="cpevent-map-balloon"><div id="cpevent-map-balloon-'+map_id+'" class="cpevent-map-balloon-content">'+ data[i].location_balloon +'</div></div>';
							em_map_infobox(marker, myContent, maps[map_id]);

							//Get min and max long/lats
							minLatLngArr[0] = (latitude < minLatLngArr[0] || i == 0) ? latitude : minLatLngArr[0];
							minLatLngArr[1] = (longitude < minLatLngArr[1] || i == 0) ? longitude : minLatLngArr[1];
							maxLatLngArr[0] = (latitude > maxLatLngArr[0] || i == 0) ? latitude : maxLatLngArr[0];
							maxLatLngArr[1] = (longitude > maxLatLngArr[1] || i == 0) ? longitude : maxLatLngArr[1];
						  }
					  }
					  // Zoom in to the bounds
					  var minLatLng = new google.maps.LatLng(minLatLngArr[0],minLatLngArr[1]);
					  var maxLatLng = new google.maps.LatLng(maxLatLngArr[0],maxLatLngArr[1]);
					  var bounds = new google.maps.LatLngBounds(minLatLng,maxLatLng);
					  maps[map_id].fitBounds(bounds);

					//Call a hook if exists
					jQuery(document).triggerHandler('cpevent_maps_locations_hook', [maps[map_id], data, map_id]);
				}else{
					el.children().first().html('No locations found');
					jQuery(document).triggerHandler('cpevent_maps_locations_hook_not_found', [el]);
				}
			});
		});
		//Location stuff - only needed if inputs for location exist
		if( $('input#location-address').length > 0 ){
			var map, marker;
			//load map info
			var refresh_map_location = function(){
				var location_latitude = $('#location-latitude').val();
				var location_longitude = $('#location-longitude').val();
				if( !(location_latitude == 0 && location_longitude == 0) ){
					var position = new google.maps.LatLng(location_latitude, location_longitude); //the location coords
					marker.setPosition(position);
					var mapTitle = ($('input#location-name').length > 0) ? $('input#location-name').val():$('input#title').val();
					marker.setTitle( $('input#location-name input#title, #location-select-id').first().val() );
					that.$element.show();
					that.$element404.hide();
					google.maps.event.trigger(map, 'resize');
					map.setCenter(position);
					map.panBy(40,-55);
					that.infoWindow.setContent( 
						'<div id="location-balloon-content"><strong>' + 
						mapTitle + 
						'</strong><br/>' + 
						$('#location-address').val() + 
						'<br/>' + $('#location-town').val()+ 
						'</div>'
					);
					that.infoWindow.open(map, marker);
					$(document).triggerHandler('cpevent_maps_location_hook', [map, that.infowindow, marker, 0]);
				} else {
					that.$element.hide();
					that.$element404.show();
				}
			};

			//Add listeners for changes to address
			$('#location-name, #location-town, #location-address').change( function(){
				//build address
				var addresses = [ $('#location-address').val(), $('#location-town').val()  ];
				var address = '';
				$.each( addresses, function(i, val){
					if( val != '' ){
						address = ( address == '' ) ? address+val:address+', '+val;
					}
				});
				if( address == '' ){ //in case only name is entered, no address
					that.$element.hide();
					that.$element404.show();
					return false;
				}
				if( address != '' && that.$element.length > 0 ){
					geocoder.geocode( { 'address': address }, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							$('#location-latitude').attr('value', results[0].geometry.location.lat());
							$('#location-longitude').attr('value', results[0].geometry.location.lng());
						}
						refresh_map_location();
					});
				}
			});

			//Load map
			if(that.$element.length > 0){
				var cpeventLatLng = new google.maps.LatLng(0, 0);
				map = new google.maps.Map( document.getElementById('cpevent-map'), {
					zoom: 14,
					center: cpeventLatLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					mapTypeControl: false
				});
				var marker = new google.maps.Marker({
					position: cpeventLatLng,
					map: map,
					draggable: true
				});
				that.infoWindow = new google.maps.InfoWindow({
					content: ''
				});
				var geocoder = new google.maps.Geocoder();
				google.maps.event.addListener(that.infoWindow, 'domready', function() { 
					document.getElementById('location-balloon-content').parentNode.style.overflow=''; 
					document.getElementById('location-balloon-content').parentNode.parentNode.style.overflow=''; 
				});
				google.maps.event.addListener(marker, 'dragend', function() {
					var position = marker.getPosition();
					$('#location-latitude').attr('value', position.lat());
					$('#location-longitude').attr('value', position.lng());
					map.setCenter(position);
					map.panBy(40,-55);
				});
				if( $('#location-select-id').length > 0 ){
					$('#location-select-id').trigger('change');
				}else{
					refresh_map_location();
				}
				$(document).triggerHandler('cpevent_map_loaded', [map, that.infowindow, marker]);
			}
			//map resize listener
			jQuery(window).on('resize', function(e) {
				google.maps.event.trigger(map, "resize");
				map.setCenter(marker.getPosition());
				map.panBy(40,-55);
			});
		}
	}
	
	
	
	$.fn.cpgmaps = function(){
			return this.each(function ()
		{
			if ( typeof google !== 'object' || typeof google.maps !== 'object' ){ 
				var script = document.createElement("script");
				script.type = "text/javascript";
				var proto = 'https:';
				script.src = proto + '//maps.google.com/maps/api/js?v=3.12&sensor=false&libraries=places&callback=cp_google_maps';
				document.body.appendChild(script);
			}else{
				var gmaps = new CpGMaps(this);
				gmaps.showMap();
			}
		})
	};

	$.fn.cpgmaps.Constructor = CpGMaps;
}(jQuery));

/* jQuery timePicker - http://labs.perifer.se/timedatepicker/ @ http://github.com/perifer/timePicker commit 100644 */
/*
 * A time picker for jQuery
 *
 * Dual licensed under the MIT and GPL licenses.
 * Copyright (c) 2009 Anders Fajerson
 * @name     timePicker
 * @author   Anders Fajerson (http://perifer.se)
 * @example  $("#mytime").timePicker();
 * @example  $("#mytime").timePicker({step:30, startTime:"15:00", endTime:"18:00"});
 *
 * Based on timePicker by Sam Collet (http://www.texotela.co.uk/code/jquery/timepicker/)
 *
 * Options:
 *   step: # of minutes to step the time by
 *   startTime: beginning of the range of acceptable times
 *   endTime: end of the range of acceptable times
 *   separator: separator string to use between hours and minutes (e.g. ':')
 *   show24Hours: use a 24-hour scheme
 */
(function($){
  $.fn.timePicker = function(options) {
    // Build main options before element iteration
    var settings = $.extend({}, $.fn.timePicker.defaults, options);

    return this.each(function() {
      $.timePicker(this, settings);
    });
  };

  $.timePicker = function (elm, settings) {
    var e = $(elm)[0];
    return e.timePicker || (e.timePicker = new jQuery._timePicker(e, settings));
  };

  $.timePicker.version = '0.3';

  $._timePicker = function(elm, settings) {

    var tpOver = false;
    var keyDown = false;
    var startTime = timeToDate(settings.startTime, settings);
    var endTime = timeToDate(settings.endTime, settings);
    var selectedClass = "selected";
    var selectedSelector = "li." + selectedClass;

    $(elm).attr('autocomplete', 'OFF'); // Disable browser autocomplete

    var times = [];
    var time = new Date(startTime); // Create a new date object.
    while(time <= endTime) {
      times[times.length] = formatTime(time, settings);
      time = new Date(time.setMinutes(time.getMinutes() + settings.step));
    }

    var $tpDiv = $('<div class="time-picker'+ (settings.show24Hours ? '' : ' time-picker-12hours') +'"></div>');
    var $tpList = $('<ul></ul>');

    // Build the list.
    for(var i = 0; i < times.length; i++) {
      $tpList.append("<li>" + times[i] + "</li>");
    }
    $tpDiv.append($tpList);
    // Append the timPicker to the body and position it.
    $tpDiv.appendTo('body').hide();

    // Store the mouse state, used by the blur event. Use mouseover instead of
    // mousedown since Opera fires blur before mousedown.
    $tpDiv.mouseover(function() {
      tpOver = true;
    }).mouseout(function() {
      tpOver = false;
    });

    $("li", $tpList).mouseover(function() {
      if (!keyDown) {
        $(selectedSelector, $tpDiv).removeClass(selectedClass);
        $(this).addClass(selectedClass);
      }
    }).mousedown(function() {
       tpOver = true;
    }).click(function() {
      setTimeVal(elm, this, $tpDiv, settings);
      tpOver = false;
    });

    var showPicker = function() {
      if ($tpDiv.is(":visible")) {
        return false;
      }
      $("li", $tpDiv).removeClass(selectedClass);

      // Position
      var elmOffset = $(elm).offset();
      $tpDiv.css({'top':elmOffset.top + elm.offsetHeight, 'left':elmOffset.left});

      // Show picker. This has to be done before scrollTop is set since that
      // can't be done on hidden elements.
      $tpDiv.show();

      // Try to find a time in the list that matches the entered time.
      var time = elm.value ? timeStringToDate(elm.value, settings) : startTime;
      var startMin = startTime.getHours() * 60 + startTime.getMinutes();
      var min = (time.getHours() * 60 + time.getMinutes()) - startMin;
      var steps = Math.round(min / settings.step);
      var roundTime = normaliseTime(new Date(0, 0, 0, 0, (steps * settings.step + startMin), 0));
      roundTime = (startTime < roundTime && roundTime <= endTime) ? roundTime : startTime;
      var $matchedTime = $("li:contains(" + formatTime(roundTime, settings) + ")", $tpDiv);

      if ($matchedTime.length) {
        $matchedTime.addClass(selectedClass);
        // Scroll to matched time.
        $tpDiv[0].scrollTop = $matchedTime[0].offsetTop;
      }
      return true;
    };
    // Attach to click as well as focus so timePicker can be shown again when
    // clicking on the input when it already has focus.
    $(elm).focus(showPicker).click(showPicker);
    // Hide timepicker on blur
    $(elm).blur(function() {
      if (!tpOver) {
        $tpDiv.hide();
      }
    });
    // Keypress doesn't repeat on Safari for non-text keys.
    // Keydown doesn't repeat on Firefox and Opera on Mac.
    // Using kepress for Opera and Firefox and keydown for the rest seems to
    // work with up/down/enter/esc.
    $(elm)['keydown'](function(e) {
      var $selected;
      keyDown = true;
      var top = $tpDiv[0].scrollTop;
      switch (e.keyCode) {
        case 38: // Up arrow.
          // Just show picker if it's hidden.
          if (showPicker()) {
            return false;
          };
          $selected = $(selectedSelector, $tpList);
          var prev = $selected.prev().addClass(selectedClass)[0];
          if (prev) {
            $selected.removeClass(selectedClass);
            // Scroll item into view.
            if (prev.offsetTop < top) {
              $tpDiv[0].scrollTop = top - prev.offsetHeight;
            }
          }
          else {
            // Loop to next item.
            $selected.removeClass(selectedClass);
            prev = $("li:last", $tpList).addClass(selectedClass)[0];
            $tpDiv[0].scrollTop = prev.offsetTop - prev.offsetHeight;
          }
          return false;
          break;
        case 40: // Down arrow, similar in behaviour to up arrow.
          if (showPicker()) {
            return false;
          };
          $selected = $(selectedSelector, $tpList);
          var next = $selected.next().addClass(selectedClass)[0];
          if (next) {
            $selected.removeClass(selectedClass);
            if (next.offsetTop + next.offsetHeight > top + $tpDiv[0].offsetHeight) {
              $tpDiv[0].scrollTop = top + next.offsetHeight;
            }
          }
          else {
            $selected.removeClass(selectedClass);
            next = $("li:first", $tpList).addClass(selectedClass)[0];
            $tpDiv[0].scrollTop = 0;
          }
          return false;
          break;
        case 13: // Enter
          if ($tpDiv.is(":visible")) {
            var sel = $(selectedSelector, $tpList)[0];
            setTimeVal(elm, sel, $tpDiv, settings);
          }
          return false;
          break;
        case 27: // Esc
          $tpDiv.hide();
          return false;
          break;
      }
      return true;
    });
    $(elm).keyup(function(e) {
      keyDown = false;
    });
    // Helper function to get an inputs current time as Date object.
    // Returns a Date object.
    this.getTime = function() {
      return timeStringToDate(elm.value, settings);
    };
    // Helper function to set a time input.
    // Takes a Date object or string.
    this.setTime = function(time) {
      elm.value = formatTime(timeToDate(time, settings), settings);
      // Trigger element's change events.
      $(elm).change();
    };

  }; // End fn;

  // Plugin defaults.
  $.fn.timePicker.defaults = {
    step:30,
    startTime: new Date(0, 0, 0, 0, 0, 0),
    endTime: new Date(0, 0, 0, 23, 30, 0),
    separator: ':',
    show24Hours: true
  };

  // Private functions.

  function setTimeVal(elm, sel, $tpDiv, settings) {
    // Update input field
    elm.value = $(sel).text();
    // Trigger element's change events.
    $(elm).change();
    // Keep focus for all but IE (which doesn't like it)
    if (!navigator.userAgent.match(/msie/i)) {
      elm.focus();
    }
    // Hide picker
    $tpDiv.hide();
  }

  function formatTime(time, settings) {
    var h = time.getHours();
    var hours = settings.show24Hours ? h : (((h + 11) % 12) + 1);
    var minutes = time.getMinutes();
    return formatNumber(hours) + settings.separator + formatNumber(minutes) + (settings.show24Hours ? '' : ((h < 12) ? ' AM' : ' PM'));
  }

  function formatNumber(value) {
    return (value < 10 ? '0' : '') + value;
  }

  function timeToDate(input, settings) {
    return (typeof input == 'object') ? normaliseTime(input) : timeStringToDate(input, settings);
  }

  function timeStringToDate(input, settings) {
    if (input) {
      var array = input.split(settings.separator);
      var hours = parseFloat(array[0]);
      var minutes = parseFloat(array[1]);

      // Convert AM/PM hour to 24-hour format.
      if (!settings.show24Hours) {
        if (hours === 12 && input.indexOf('AM') !== -1) {
          hours = 0;
        }
        else if (hours !== 12 && input.indexOf('PM') !== -1) {
          hours += 12;
        }
      }
      var time = new Date(0, 0, 0, hours, minutes, 0);
      return normaliseTime(time);
    }
    return null;
  }

  /* Normalise time object to a common date. */
  function normaliseTime(time) {
    time.setFullYear(2001);
    time.setMonth(0);
    time.setDate(0);
    return time;
  }

})(jQuery);


