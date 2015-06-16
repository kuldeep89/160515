$(function() {

	$('.icon-list li').click(function() {
		
		var icon	= $(this).find('.icon').text().trim();
		$('#icon').val(icon);
		
	});
	
	$('.color-box').click(function() {
		$('#color').val($(this).attr('alt'));
	});
	
});