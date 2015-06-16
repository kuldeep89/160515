<?php

	/**
	*
	* Author: Enrique Marrufo
	*
	**/
	
	//////////////////
	// * Note: The PHP closed( $current_time ) takes the $current_time and creates an array from
	//         the date and time to be used to determain if the market is open or closed.
	/////////////////
	function closed( $current_time ) {
	
		@list($month, $date, $time, $gmt) = explode(" ", $current_time);

		if ( $time == '4:00pm' ) {
			$market_time = "The market is closed until 9:30am GMT-4";
		} else {
			$market_time = $current_time;
		}
		return $market_time;
	}

	////////////////
	// * Note: The PHP change( $value ) takes the stock value at the time and determains which $class to assign.
	////////////////
	function change( $value ) {
		if ( $value > 0 ) {
			$class = "gain";
			return $class;
		} else if ( $value < 0 ) {
			$class = "loss";
			return $class;
		} else {
			$class = "neutral";
			return $class;
		}
	}

	////////////////
	// * Note: The PHP arrow( $value ) takes the stock value at the time and determains which $class to assign.
	////////////////
	function arrow( $value ) {
		if ( $value > 0 ) {
			$class = "<i class=\"icon-caret-up\"></i>";
			return $class;
		} else if ( $value < 0 ) {
			$class = "<i class=\"icon-caret-down\"></i>";
			return $class;
		} else {
			$class = "<i class=\"icon-caret-right\"></i>";
			return $class;
		}
	}
	
	/////////////////
	// * Note: The PHP validate_stock_choice( $stock_id ) takes a stock id as its perameter returning
	//         true if the stock id is valid. If not returns an error message.
	/////////////////
	function validate_stock_choice( $stock_id ) {
		$query = @file_get_contents('http://finance.google.co.uk/finance/info?client=ig&q='.$stock_id);

		if ($query) {
			return true;
		} else {
			return "Sorry that was an invalid Stock ID.";
		}
	}

	////////////////
	// * Note: The PHP catch_exception( $query ) takeing the $quote-query variable to determain if the query
	//         sent by PHP returns true, if not it returns an error message.
	////////////////
	function catch_exception( $query ) {
		if ( $query ) {

		} else {
			echo "Something went wrong. Please refresh.";
		}
	}
	
	
	////////////////
	// * Note: The PHP linkify( $text ) takes a strong replaces charactors in the string and returns a clickable url. 
	////////////////
	function linkify($text) {
	    $text= preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target=\"_blank\">$3</a>", $text);
	    $text= preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target=\"_blank\">$3</a>", $text);
	    $text= preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);
	    return($text);
	}
		
?>