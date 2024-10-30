(function( $ ) {
	'use strict';

	/*
	* Function used to display calender
	* */
	document.addEventListener('DOMContentLoaded', function() {
		var calendarEl = document.getElementById('ce21_single_sign_on_calendar');

		if (!calendarEl) return;

		var calendar = new FullCalendar.Calendar(calendarEl, {
			plugins: [ 'interaction', 'dayGrid' ],
			eventRender: function(info) {
				if(isBootstrapUse) {
					$(info.el).tooltip({
						title: info.event.extendedProps.description,
						placement: 'top',
						trigger: 'hover',
						html: true,
						container: 'body'
					});
				}else{
					var tooltip = new Tooltip(info.el, {
						title: info.event.extendedProps.description,
						placement: 'top',
						trigger: 'hover',
						html: true,
						container: 'body'
					});
				}
				if(info.event.extendedProps.time != '' || info.event.extendedProps.time != 'undefined') {
					$(info.el).find('.fc-title').before('<span class="custom-fc-time">' + info.event.extendedProps.time + '</span>');
				}
				if(info.event.extendedProps.type != '' || info.event.extendedProps.type != 'undefined') {
					$(info.el).find('.fc-title').after('<span class="fc-description">' + info.event.extendedProps.type + '</span>');
				}
			},
			header: {
				left: 'prev,today,next ',
				center: 'title',
				right: ''
			},
			buttonText: {
				prev: '<< Prev',
				next: 'Next >>',
				today: 'Today',
			},
			contentHeight:"auto",
			handleWindowResize:true,
			navLinks: true,
			editable: true,
			eventLimit: false,
			showNonCurrentDates: false,
			views: {
				timeGrid: {
					eventLimit: 6 // adjust to 6 only for timeGridWeek/timeGridDay
				}
			},
			events: {
				url: ajax_login_object.ajaxurl,
				method: 'POST',
				extraParams: {
					action: 'get_ce21_single_sign_on_calendar_events'
				},
				failure: function() {
					console.log('There was an error while fetching events!');
				}
			},
			loading: function( isLoading, view ) {
				if(isLoading) {// isLoading gives boolean value
					$('#ce21wait').show();
					$('#ce21_wait_overlay').show();
				} else {
					$('#ce21wait').hide();
					$('#ce21_wait_overlay').hide();
				}
			}


		});

		calendar.render();
	});

})( jQuery );
