$(function() {
	
	$('#faq-entry-form').submit(function() {

		var entry			= {};
		entry.faq			= $('#faq').html();
		entry.response		= $('#response').html();
		entry.categories	= $('#select-categories').val();
		entry.entry_id		= $('#entry_id').val();
		
	/*
			console.log("Entry title: "+entry.title);
		console.log("Published date: "+entry.publish_date);
		console.log("Entry content: "+entry.content);
		console.log('Keywords: '+entry.keywords);
		console.log('Description: '+entry.description);
		console.log('Browser Title: '+entry.browser_title);
		console.log('Tags: '+entry.tags);
		console.log('Categories: '+entry.categories);
		console.log('Entry ID: '+entry.id);
		console.log('Author ID: '+entry.author_id);
*/
		
		var json	= JSON.stringify(entry);

		$.ajax({
		
            url: '/'+mbp+'/faq/update-entry/'+entry.id,
            type: 'POST',
            data: { 'json': json},
            dataType: 'text',
            success: function(msg) {
            	
            	msg	= (typeof(msg) == 'object')? msg.responseText:msg
            	
	            if( msg.indexOf('UPDATED') != -1 ) {
					add_success("You have successfully updated this entry.");
				}
				else {
					add_error("Failed to update entry, please try again.");
					console.log(msg);
				}
				
				window.scrollTo(0,0);
				
            },
			error: function(msg) {
				
				msg	= (typeof(msg) == 'object')? msg.responseText:msg
				
				if(  msg.indexOf('UPDATED') != -1 ) {
					add_success("You have successfully updated this entry.");
				}
				else {
					add_error("Failed to update entry, please try again.");
					console.log(msg);
				}
				
				window.scrollTo(0,0);
				
			}
			
        });
		
		return false;
		
	});
	
});