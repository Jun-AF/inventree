$(document).ready(function() {
	let url = window.location.pathname;
	let sidebar_lsit = $('#accordionSidebar li');
	if (url.match(/dashboard/)) {
		sidebar_lsit.eq(0).addClass("active");
		sidebar_lsit.eq(1).removeClass("active");
		sidebar_lsit.eq(2).removeClass("active");
		sidebar_lsit.eq(3).removeClass("active");
		sidebar_lsit.eq(4).removeClass("active");
		sidebar_lsit.eq(5).removeClass("active");
	} else if (url.match(/supervisor.*|scaler.*|operator.*|user.*/)) {
		sidebar_lsit.eq(0).removeClass("active");
		sidebar_lsit.eq(1).addClass("active");
		sidebar_lsit.eq(2).removeClass("active");
		sidebar_lsit.eq(3).removeClass("active");
		sidebar_lsit.eq(4).removeClass("active");
		sidebar_lsit.eq(5).removeClass("active");
	} else if (url.match(/rkt.*/)) {
		sidebar_lsit.eq(0).removeClass("active");
		sidebar_lsit.eq(1).removeClass("active");
		sidebar_lsit.eq(2).addClass("active");
		sidebar_lsit.eq(3).removeClass("active");
		sidebar_lsit.eq(4).removeClass("active");
		sidebar_lsit.eq(5).removeClass("active");
	} else if (url.match(/harvesting.*/)) {
		sidebar_lsit.eq(0).removeClass("active");
		sidebar_lsit.eq(1).removeClass("active");
		sidebar_lsit.eq(2).removeClass("active");
		sidebar_lsit.eq(3).addClass("active");
		sidebar_lsit.eq(4).removeClass("active");
		sidebar_lsit.eq(5).removeClass("active");
	} else if (url.match(/measurement_28.*|measurement_42.*|hauling_28.*|hauling_42.*/)) {
		sidebar_lsit.eq(0).removeClass("active");
		sidebar_lsit.eq(1).removeClass("active");
		sidebar_lsit.eq(2).removeClass("active");
		sidebar_lsit.eq(3).removeClass("active");
		sidebar_lsit.eq(4).addClass("active");
		sidebar_lsit.eq(5).removeClass("active");
	} else if (url.match(/settings.*/)) {
		sidebar_lsit.eq(0).removeClass("active");
		sidebar_lsit.eq(1).removeClass("active");
		sidebar_lsit.eq(2).removeClass("active");
		sidebar_lsit.eq(3).removeClass("active");
		sidebar_lsit.eq(4).removeClass("active");
		sidebar_lsit.eq(5).addClass("active");
	}
});