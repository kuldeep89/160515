<?

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
	*
	**/

		class Stock_quotes extends CI_Controller {
	
			public function index() {
				
				$this->load->model('stock_model');
				$this->load->view('stock_quote');
			}
		}