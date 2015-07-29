$(document).ready(function(){

	// Navbar Highlight Current Page
	$('.navbar ul a[href="'+window.location.pathname+'"]').parent('li').addClass('active');

	$('.btn#doneTraining').click(function(event){
		if($('#password').val() || $('#username').val())
			if(!confirm('Are you sure you want to finish training? Anything currently entered into the trained user information section will be discarded.'))
				event.preventDefault();
	});

	$('#instructorSelect input').click(function(event){
		$('#instructorCredentials').toggle($(this).val());
	});

	$('input#username').focus();
  	$('.indicator.trained').tooltip();

  	$('.carousel').carousel({
	  interval: 7500
	});

});