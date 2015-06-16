<?php

require_once dirname(__FILE__) . '/config.php';

require_once dirname(__FILE__) . '/views/header.tpl.php';

?>
<style>
.margin-class {
	margin-top: 5px;
	margin-left: 10px;
}
</style>
<h4>Add Customer</h4>
<form method='post' action='' >
	<!-- Customer Information -->
	<div>
		<label>Customer Name</label><input class='margin-class' name='customer_title' type='text' placeholder='---Customer Title---' />
		<input class='margin-class' type='text' name='customer_first_name' placeholder='---First Name---' />
		<input class='margin-class' type='text' name='customer_middle_name' placeholder='---Middle Name---' />
		<input class='margin-class' type='text' name='customer_family_name' placeholder='---Family Name---' />
	</div>
	<div>
		<label>Display Name</label><input type='text' class='margin-class' name='customer_display_name' placeholder='---Customer Display Name---' />
	</div>
	<div>
		<label>Free Form Number</label><input type='text' class='margin-class' name='form_number' placeholder='---Free Form Number---' />
	</div>
	<div>
		<label>Set Line1</label><input type='text' class='margin-class' name='line1' placeholder='---Line1---' />
	</div>
	<div>
		<label>Set Line2</label><input type='text' class='margin-class' name='line2' placeholder='---Line2---' />
	</div>
	<div>
		<label>Set City</label><input type='text' class='margin-class' name='city' placeholder='---City---' />
	</div>
	<div>
		<label>Sub Division Code</label><input type='text' class='margin-class' name='sub_div_code' placeholder='---Sub Division Code---' />
	</div>
	
	<div>
		<label>Postal Code</label><input type='text' class='margin-class' name='postal_code' placeholder='---Postal Code---' />
	</div>
	<div>
		<label>Primary Email Address</label><input type='text' class='margin-class' name='primary_email_address' placeholder='---Email Address---' />
	</div>
	<div>
		<input type='submit' value='Add Customer' />
	</div>
</form>
<pre>

<?php

if($_POST) {
	$CustomerService = new QuickBooks_IPP_Service_Customer();
	
	$Customer = new QuickBooks_IPP_Object_Customer();
	$Customer->setTitle($_POST['customer_title']);
	$Customer->setGivenName($_POST['customer_first_name']);
	$Customer->setMiddleName($_POST['customer_middle_name']);
	$Customer->setFamilyName($_POST['customer_family_name']);
	$Customer->setDisplayName($_POST['customer_display_name'] . mt_rand(0, 1000));
	// Terms (e.g. Net 30, etc.)
	$Customer->setSalesTermRef(4);
	// Phone #
	$PrimaryPhone = new QuickBooks_IPP_Object_PrimaryPhone();
	$PrimaryPhone->setFreeFormNumber($_POST['form_number']);
	$Customer->setPrimaryPhone($PrimaryPhone);

	// Mobile #
	$Mobile = new QuickBooks_IPP_Object_Mobile();
	$Mobile->setFreeFormNumber($_POST['form_number']);
	$Customer->setMobile($Mobile);

	// Fax #
	$Fax = new QuickBooks_IPP_Object_Fax();
	$Fax->setFreeFormNumber($_POST['form_number']);
	$Customer->setFax($Fax);

	// Bill address
	$BillAddr = new QuickBooks_IPP_Object_BillAddr();
	$BillAddr->setLine1($_POST['line1']);
	$BillAddr->setLine2($_POST['line2']);
	$BillAddr->setCity($_POST['city']);
	$BillAddr->setCountrySubDivisionCode($_POST['sub_div_code']);
	$BillAddr->setPostalCode($_POST['postal_code']);
	$Customer->setBillAddr($BillAddr);

	// Email
	$PrimaryEmailAddr = new QuickBooks_IPP_Object_PrimaryEmailAddr();
	$PrimaryEmailAddr->setAddress($_POST['primary_email_address']);
	$Customer->setPrimaryEmailAddr($PrimaryEmailAddr);

	if ($resp = $CustomerService->add($Context, $realm, $Customer))
	{
		print('Our new customer ID is: [' . $resp . '] (name "' . $Customer->getDisplayName() . '")');
	}
	else
	{
		print($CustomerService->lastError($Context));
	}

	/*
	print('<br><br><br><br>');
	print("\n\n\n\n\n\n\n\n");
	print('Request [' . $IPP->lastRequest() . ']');
	print("\n\n\n\n");
	print('Response [' . $IPP->lastResponse() . ']');
	print("\n\n\n\n\n\n\n\n\n");
	*/
}
?>

</pre>

<?php

require_once dirname(__FILE__) . '/views/footer.tpl.php';
