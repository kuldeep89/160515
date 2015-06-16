<?php
require_once dirname(__FILE__) . '/config.php';

require_once dirname(__FILE__) . '/views/header.tpl.php';

?>
<?php
//Sandbox Url: https://sandbox-quickbooks.api.intuit.com
//Operation: POST /v3/company/<companyID>/customer
//Content type: application/json
$CustomerService = new QuickBooks_IPP_Service_Customer();
$customer = $CustomerService->query($Context, $realm, "SELECT * FROM Customer WHERE Id = '67' ");

foreach ($customer as $customer_data)
{
	print_r($customer_data);		
}
?>

<h3>Under Constructions...</h3>

<?php
require_once dirname(__FILE__) . '/views/footer.tpl.php';