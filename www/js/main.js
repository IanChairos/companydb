$(function(){
	
	$("body").on('click','.button-faker:not(.modal-confirm)', function(){
		var target = $(this).attr('data-target');
		if( !target )
			return;
		$(target).click();
	});
	
	$("body").on('click','.toggler-vertical-slide', function(){
		var target = $(this).attr('data-target');
		if( !target )
			return;
		var tgt = $(target);
		if( tgt.is(":visible") ){
			tgt.slideUp(50);
		}else{
			tgt.slideDown(50);
		}
	});
	
	$("body").on('change','.submit-form-onchange', function(){
		$(this).parents('form').find("input[type='submit']").click();
	});
	
	$("body").on('click','.alert .close', function(){
		$(this).parents('.alert').fadeOut(250);
	});
	
	$('body').on('click','.modal-confirm',function(){
		var self	= $(this);
		var title	= self.attr('data-modal-title') ? self.attr('data-modal-title') : 'Are you sure?';
		var message	= self.attr('data-modal-message');
		var target	= $('#modal-window');
		
		$('#modal-label').text(title);
		$('#modal-message').text(message);
		if( self.hasClass('button-faker') ) {
			$('#modal-confirm-button').on('click',function(){
				var buttonTarget = self.attr('data-target');
				if( !buttonTarget )
					return;
				$(buttonTarget).click();
			});
		}else{
			var href = self.attr('href');
			$('#modal-confirm-button').attr('href',href);
		}
		target.modal();
		return false;
	});
	
});