var DWELL_TIME = 2000; // Milliseconds
var SPEED = 15; // Lower is faster

function scrollToBottom(){
	$("body").animate({ scrollTop: 0}, 0);
	window.setTimeout(function(){
		$("body").animate({ scrollTop: $(document).height() }, $('body').height()*SPEED, 'linear', function(){
			location.reload(true);
		});
	}, DWELL_TIME);
}
$(document).ready(scrollToBottom);
