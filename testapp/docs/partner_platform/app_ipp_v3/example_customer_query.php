<?php

require_once dirname(__FILE__) . '/config.php';

require_once dirname(__FILE__) . '/views/header.tpl.php';

?>

<pre>

<?php

$CustomerService = new QuickBooks_IPP_Service_Customer();

$customers = $CustomerService->query($Context, $realm, "SELECT * FROM Customer Where Id = '67'");



?>
<h4 class='alert alert-success'>Customer Data</h4>
<table class="table table-bordered">
<tr>
<th>Id</th>
<th>Customer Name</th>
<th>Balance</th>
</tr>
<?php
foreach ($customers as $Customer)
{	
	echo '<tr>';
	echo '<td>' . trim($Customer->getId(), '{-}').' </td><td>' . $Customer->getFullyQualifiedName().'</td><td> '.$Customer->getBalance().' </td>';
	echo '</tr>';
}
?>
</table>
<?php

//print_r($customers);
print("\n\n\n\n");
//print('Request [' . $CustomerService->lastRequest() . ']');
print("\n\n\n\n");
//print('Response [' . $CustomerService->lastResponse() . ']');
print("\n\n\n\n");
	
?>

</pre>

<?php

require_once dirname(__FILE__) . '/views/footer.tpl.php';

?>