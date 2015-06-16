// This code is generally not necessary, but it is here to demonstrate
// how to customize specific editor instances on the fly. This fits well
// this demo because we have editable elements (like headers) that
// require less features.

// The "instanceCreated" event is fired for every editor instance created.

$(function() {
	
	$(".date-picker").datepicker({
        format: "MM dd, yyyy",
        autoclose: true,
        todayBtn: true,
        linkField: "mirror_field_hidden"
    }).on("changeDate", function(ev) {
		console.log("Date changed.");
	});
	
});

var dateFormat	= {};

dateFormat.i18n = {
    dayNames: [
        "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
        "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ],
    monthNames: [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
        "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    ]
};
