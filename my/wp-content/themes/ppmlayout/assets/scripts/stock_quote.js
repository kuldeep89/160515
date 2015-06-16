	/**
	* PayProMedia Stock Widget
	* Author: Enrique Marrufo
	* Date: 17 August 2013
	*
	* Notes: This widget queries http://finance.google.co.uk/finance/info for selected 
	*		 stocks and returns a json string. PHP is used to parse the json string on 
	*		 the initial load then ajax queries http://finance.google.co.uk/finance/info
	*		 every seven seconds to update the stoc price, change, and change percent. 
	*		 The updated data is injected by traversing the DOM using jquery.
	*
	**/

var stock_quote = {
	onLoad: function() {
		// If no stock tickers added, show "No Tickers Available" warning
		if ($('.widget-item').length == 0) {
			$('#no_tickers').show();
			$('#save_data').attr('disabled','disabled');
		}
	},
	addTicker: function() {
		// Add stocker ticker to table
		$('#stock_tickers').append('<tr><td><input type="text" id="" onkeyup="$(this).attr(\'id\', \'stock_ticker_\'+$(this).val());" class="widget-item" /></td><td><a onclick="stock_quote.removeTicker($(this));" class="btn mini red"><i class="icon-trash"></i> Delete</a></td></tr>');

		// Focus on last added ticker box
		$('#stock_tickers input:last-child').focus();

		// If not hidden, hide "No Tickers Available" warning
		if ($('#no_tickers').is(':visible')) {
			$('#no_tickers').hide();
			$('#save_data').removeAttr('disabled');
		}
	},
	removeTicker: function(elementId) {
		// Remove stock ticker
		elementId.parent().parent().remove();

		// If no stock tickers added, show "No Tickers Available" warning
		if ($('.widget-item').length == 0) {
			$('#no_tickers').show();
			$('#save_data').attr('disabled','disabled');
		}
	}
}

		////////////////
		// * Note: The JS change( $value ) takes the stock value at the time and determains which $class to assign.
		////////////////
		function change( value ) {
			if ( value > 0 ) {
				chan = "gain";
				return chan; 
			} else if ( value < 0 ) {
				chan = "loss";
				return chan;
			} else {
				chan = "neutral";
				return chan;
			}
		}
		
		////////////////
		// * Note: The JS arrow( $value ) takes the stock value at the time and determains which $class to assign.
		////////////////
		function arrow( value ) {
			if ( value > 0 ) {
				arrw = "<i class=\"icon-caret-up\"></i>";
				return arrw; 
			} else if ( value < 0 ) {
				arrw = "<i class=\"icon-caret-down\"></i>";
				return arrw;
			} else {
				arrw = "<i class=\"icon-caret-right\"></i>";
				return arrw;
			}
		}
		
		////////////////
		// * Note: The JS query_stock_quotes() uses ajax to send a query to url http//finance.google.co.uk/finance/info 
		//         using client=ig&q=MSFT,GOOG,YHOO,AAPL,AMZN as the data. On success jquery will populate the indecated 
		//         DOM elements with updated data from the query. 
		////////////////				 
		function query_stock_quotes() {
		
			var uri = 'client=ig&q=';
			var first = true;
				$('.stock-symbol').each(function() {
					
					$(this).parent().attr('id', $(this).text().trim());
					
					if (!first) {
						uri += ',';
					} 
					
					uri += $(this).text().trim();
					
					first = false;
				});
			
				$.ajax({
					url: 'http://finance.google.com/finance/info',
					data: uri,
					dataType: 'jsonp',
					success: function( data ) {
					
					////////////////
					// * Note: the code below takes the date and time creating an array to be used to determain if 
					//         the market is open or closed. If cloded will display a message indicating so. 
					//         If open will display the current market time. 
					////////////////	
					var srt = data[0].lt;
					var time = srt.split(" ");
					
					if ( time[2] == '4:00pm' ) {
						var market_time = "The market is closed until 9:30AM EDT";
					} else {
						var market_time = data[0].lt;
					}
					$('.stock-time').html(market_time);
					
					////////////////
					// * Note: This is the first row in the HTML table.
					////////////////
					var track = 0;
						$(data).each(function() {
							$('#' + data[track].t+ ' .price').html(data[track].l);
							$('#' + data[track].t+ ' .change').removeClass('gain loss neutral').addClass(change(data[track].c)).html(data[track].c+' '+arrow(data[track].c));
							$('#' + data[track].t+ ' .percent-change').removeClass('gain loss neutral').addClass(change(data[track].cp)).html(data[track].cp+'%'+' '+arrow(data[track].cp));
							track += 1;
						});					
					}
				});
		}
