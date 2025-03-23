jQuery(document).ready(function($) {
    var calendarEl = document.getElementById('csb-calendar');
    if (calendarEl) {
        var postId = $(calendarEl).data('post-id');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            events: function(fetchInfo, successCallback) {
                $.ajax({
                    url: csb_ajax.ajax_url,
                    data: {
                        action: 'csb_get_booking_slots',
                        post_id: postId
                    },
                    success: function(data) {
                        successCallback(data);
                    }
                });
            }
        });
        calendar.render();
    }
});