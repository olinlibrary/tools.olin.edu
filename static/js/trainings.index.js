$(window).scroll(function(){
	$('.header').css({
		'top': $(this).scrollTop()
	});
})