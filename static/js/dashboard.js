var DWELL_TIME = 2000; // Milliseconds
var SPEED = 5; // Lower is faster

function scrollToBottom(){
	$("body").animate({ scrollTop: 0}, 0);
	window.setInterval(function(){
		$("body").animate({ scrollTop: $(document).height() }, $('body').height()*SPEED, 'linear', function(){
			//window.location.reload(false);
			$(document).scrollTop(0);
		});
	}, DWELL_TIME);
}
$(document).ready(scrollToBottom);
